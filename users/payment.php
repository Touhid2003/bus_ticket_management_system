
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once '../config/db.php';
$paymentConfig = require '../config/payment.php';


// Check Customer Login
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'customer' ||
    !isset($_SESSION['user_id'])
) {
    header('Location: ../login.php');
    exit;
}


// Escape HTML
function escapePaymentHtml($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}


// Load Customer Booking
function loadCustomerBooking($conn, $bookingId, $userId)
{
    $bookingId = (int)$bookingId;
    $userId = (int)$userId;

    $sql = "SELECT b.booking_id, b.total_amount, b.booking_status,
                   s.departure_date, s.departure_time, bus.bus_name,
                   r.source, r.destination
            FROM bookings b
            INNER JOIN schedules s ON b.schedule_id = s.schedule_id
            INNER JOIN buses bus ON s.bus_id = bus.bus_id
            INNER JOIN routes r ON s.route_id = r.route_id
            WHERE b.booking_id = $bookingId
            AND b.user_id = $userId";

    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        return null;
    }

    $booking = $result->fetch_assoc();

    // Load Booking Seats
    $booking['seats'] = [];

    $sql = "SELECT s.seat_number
            FROM booking_seats bs
            INNER JOIN seats s ON bs.seat_id = s.seat_id
            WHERE bs.booking_id = $bookingId
            ORDER BY s.seat_number";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $booking['seats'][] = $row['seat_number'];
    }

    return $booking;
}


// Load bKash Payment
function loadBkashPayment($conn, $bookingId)
{
    $bookingId = (int)$bookingId;

    $sql = "SELECT payment_id, payment_method, transaction_id, payment_status
            FROM payments
            WHERE booking_id = $bookingId
            AND payment_method IN ('bKash', 'bKash Demo', 'bKash Sandbox')
            ORDER BY payment_id DESC
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        return null;
    }

    return $result->fetch_assoc();
}

function bkashSandboxReady($config)
{
    $baseHost = parse_url($config['bkash_sandbox_base_url'] ?? '', PHP_URL_HOST);
    $callback = trim($config['bkash_sandbox_callback_url'] ?? '');
    $callbackScheme = parse_url($callback, PHP_URL_SCHEME);
    $callbackHost = parse_url($callback, PHP_URL_HOST);
    $callbackIsSafe = $callbackScheme === 'https' ||
        ($callbackScheme === 'http' && in_array(strtolower((string)$callbackHost), ['localhost', '127.0.0.1', '::1'], true));

    return !empty($config['bkash_sandbox_enabled']) &&
        strtolower((string)$baseHost) === 'tokenized.sandbox.bka.sh' &&
        !empty($config['bkash_sandbox_app_key']) &&
        !empty($config['bkash_sandbox_app_secret']) &&
        !empty($config['bkash_sandbox_username']) &&
        !empty($config['bkash_sandbox_password']) &&
        $callbackHost !== null &&
        $callbackIsSafe;
}

function bkashSandboxRequest($config, $path, $payload, $idToken = null)
{
    if (!function_exists('curl_init')) {
        throw new Exception('PHP cURL extension is required for bKash sandbox.');
    }

    $baseUrl = rtrim($config['bkash_sandbox_base_url'], '/');
    $headers = ['Content-Type: application/json', 'Accept: application/json'];

    if ($idToken === null) {
        $credentials = base64_encode($config['bkash_sandbox_app_key'] . ':' . $config['bkash_sandbox_app_secret']);
        $headers[] = 'Authorization: Basic ' . $credentials;
        $headers[] = 'username: ' . $config['bkash_sandbox_username'];
        $headers[] = 'password: ' . $config['bkash_sandbox_password'];
    } else {
        $headers[] = 'Authorization: ' . $idToken;
        $headers[] = 'X-APP-Key: ' . $config['bkash_sandbox_app_key'];
    }

    $curl = curl_init($baseUrl . $path);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $responseBody = curl_exec($curl);
    $httpStatus = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);

    if ($responseBody === false || $httpStatus < 200 || $httpStatus >= 300) {
        error_log('bKash sandbox HTTP request failed. Status: ' . $httpStatus . '; ' . $curlError);
        throw new Exception('Could not communicate with bKash sandbox.');
    }

    $response = json_decode($responseBody, true);
    if (!is_array($response)) {
        error_log('bKash sandbox returned an invalid JSON response.');
        throw new Exception('bKash sandbox returned an invalid response.');
    }
    if (isset($response['statusCode']) && (string)$response['statusCode'] !== '0000') {
        error_log('bKash sandbox API error: ' . ($response['statusMessage'] ?? 'Unknown API error'));
        throw new Exception('bKash sandbox rejected the request.');
    }

    return $response;
}

function bkashSandboxAccessToken($config)
{
    $response = bkashSandboxRequest(
        $config,
        '/tokenized/checkout/token/grant',
        [
            'app_key' => $config['bkash_sandbox_app_key'],
            'app_secret' => $config['bkash_sandbox_app_secret'],
        ]
    );

    if (empty($response['id_token'])) {
        throw new Exception('bKash sandbox did not issue an access token.');
    }

    return $response['id_token'];
}


// Get Booking ID
$bookingId = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);

if ($bookingId < 1) {
    header('Location: home.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

$booking = loadCustomerBooking($conn, $bookingId, $userId);

if (!$booking) {
    http_response_code(404);
    exit('Booking not found.');
}


$requestHost = parse_url(
    'http://' . ($_SERVER['HTTP_HOST'] ?? ''),
    PHP_URL_HOST
);

$bkashDemoMode = !empty($paymentConfig['bkash_demo_mode']) &&
    in_array(strtolower($requestHost), ['localhost', '127.0.0.1', '::1']) &&
    in_array($_SERVER['REMOTE_ADDR'] ?? '', [
        '127.0.0.1',
        '::1',
        '::ffff:127.0.0.1'
    ]);
$bkashSandboxReady = bkashSandboxReady($paymentConfig);

$errors = [];


// Create CSRF Token
if (!isset($_SESSION['bkash_payment_csrf'])) {
    $_SESSION['bkash_payment_csrf'] = bin2hex(random_bytes(32));
}

if (isset($_GET['bkash_callback'])) {
    try {
        if (!$bkashSandboxReady) {
            throw new Exception('bKash sandbox is not configured.');
        }

        $sandboxPaymentId = trim((string)($_GET['paymentID'] ?? ''));
        $callbackStatus = strtolower(trim((string)($_GET['status'] ?? '')));
        if (!preg_match('/^[A-Za-z0-9._-]{1,100}$/', $sandboxPaymentId)) {
            throw new Exception('Invalid bKash sandbox payment reference.');
        }

        $safeSandboxPaymentId = $conn->real_escape_string($sandboxPaymentId);
        $pendingTransactionId = $conn->real_escape_string('BKASH-PENDING-' . $sandboxPaymentId);

        if ($callbackStatus !== 'success') {
            $conn->query(
                "UPDATE payments
                 SET payment_status = 'failed'
                 WHERE booking_id = $bookingId
                 AND payment_method = 'bKash Sandbox'
                 AND transaction_id = '$pendingTransactionId'
                 AND payment_status = 'pending'"
            );
            header('Location: payment.php?booking_id=' . $bookingId . '&bkash_failed=1');
            exit;
        }

        $pendingResult = $conn->query(
            "SELECT payment_id
             FROM payments
             WHERE booking_id = $bookingId
             AND payment_method = 'bKash Sandbox'
             AND transaction_id = '$pendingTransactionId'
             AND payment_status = 'pending'
             LIMIT 1"
        );
        $pendingPayment = $pendingResult->fetch_assoc();

        if (!$pendingPayment) {
            $paidResult = $conn->query(
                "SELECT payment_id FROM payments
                 WHERE booking_id = $bookingId
                 AND payment_method = 'bKash Sandbox'
                 AND payment_status = 'paid'
                 LIMIT 1"
            );
            if ($paidResult->num_rows > 0) {
                header('Location: payment.php?booking_id=' . $bookingId . '&paid=1');
                exit;
            }
            throw new Exception('The bKash sandbox payment could not be matched to this booking.');
        }

        $idToken = bkashSandboxAccessToken($paymentConfig);
        $execution = bkashSandboxRequest(
            $paymentConfig,
            '/tokenized/checkout/execute/' . rawurlencode($sandboxPaymentId),
            [],
            $idToken
        );

        $executionAmount = isset($execution['amount']) ? (float)$execution['amount'] : -1;
        if (
            ($execution['transactionStatus'] ?? '') !== 'Completed' ||
            ($execution['currency'] ?? '') !== 'BDT' ||
            !isset($execution['trxID']) ||
            abs($executionAmount - (float)$booking['total_amount']) >= 0.01 ||
            (isset($execution['paymentID']) && $execution['paymentID'] !== $sandboxPaymentId)
        ) {
            error_log('bKash sandbox payment verification failed for booking ' . $bookingId . '.');
            throw new Exception('bKash sandbox could not verify the payment.');
        }

        $safeTransactionId = $conn->real_escape_string((string)$execution['trxID']);
        $paymentRowId = (int)$pendingPayment['payment_id'];
        $conn->begin_transaction();
        $transactionStarted = true;

        $lockedBookingResult = $conn->query(
            "SELECT booking_status FROM bookings
             WHERE booking_id = $bookingId AND user_id = $userId
             FOR UPDATE"
        );
        $lockedBooking = $lockedBookingResult->fetch_assoc();
        $lockedPaymentResult = $conn->query(
            "SELECT payment_status FROM payments
             WHERE payment_id = $paymentRowId AND booking_id = $bookingId
             FOR UPDATE"
        );
        $lockedPayment = $lockedPaymentResult->fetch_assoc();

        if (
            !$lockedBooking ||
            $lockedBooking['booking_status'] !== 'pending' ||
            !$lockedPayment ||
            $lockedPayment['payment_status'] !== 'pending'
        ) {
            throw new Exception('This booking is no longer awaiting payment.');
        }

        $conn->query(
            "UPDATE payments
             SET transaction_id = '$safeTransactionId', payment_status = 'paid',
                 payment_date = NOW()
             WHERE payment_id = $paymentRowId"
        );
        $conn->query(
            "UPDATE bookings
             SET booking_status = 'confirmed'
             WHERE booking_id = $bookingId AND user_id = $userId
             AND booking_status = 'pending'"
        );
        if ($conn->affected_rows !== 1) {
            throw new Exception('Booking could not be confirmed.');
        }

        $conn->commit();
        $transactionStarted = false;
        header('Location: payment.php?booking_id=' . $bookingId . '&paid=1');
        exit;
    } catch (Throwable $exception) {
        if (isset($transactionStarted) && $transactionStarted) {
            $conn->rollback();
        }
        error_log('bKash sandbox callback error: ' . $exception->getMessage());
        header('Location: payment.php?booking_id=' . $bookingId . '&bkash_error=1');
        exit;
    }
}


// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $isDemoRequest = ($_POST['payment_mode'] ?? '') == 'demo';
    $isSandboxRequest = ($_POST['payment_mode'] ?? '') == 'sandbox';
    $csrfToken = $_POST['csrf_token'] ?? '';


    // Validate Form
    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION['bkash_payment_csrf'], $csrfToken)
    ) {
        $errors[] = 'Your form session expired. Please try again.';

    } elseif ($booking['booking_status'] != 'pending') {

        $errors[] = 'This booking is not awaiting payment.';

    } elseif ($isDemoRequest && !$bkashDemoMode) {

        $errors[] = 'Demo payment is disabled.';

    } elseif ($isSandboxRequest && !$bkashSandboxReady) {

        $errors[] = 'bKash sandbox is not configured. Set the sandbox credentials and callback URL first.';

    } elseif (!$isDemoRequest && !$isSandboxRequest) {

        $errors[] = 'Select a valid payment method.';

    } else {

        $transactionStarted = false;
        try {
            if ($isDemoRequest) {
                $conn->begin_transaction();
                $transactionStarted = true;

                $result = $conn->query(
                    "SELECT total_amount, booking_status
                     FROM bookings
                     WHERE booking_id = $bookingId AND user_id = $userId
                     FOR UPDATE"
                );
                $lockedBooking = $result->fetch_assoc();
                if (!$lockedBooking || $lockedBooking['booking_status'] !== 'pending') {
                    throw new Exception('This booking is not awaiting payment.');
                }

                $paidResult = $conn->query(
                    "SELECT payment_id FROM payments
                     WHERE booking_id = $bookingId
                     AND payment_status = 'paid'
                     LIMIT 1 FOR UPDATE"
                );
                if ($paidResult->num_rows > 0) {
                    throw new Exception('Payment has already been verified.');
                }

                $amount = (float)$lockedBooking['total_amount'];
                $demoTransactionId = 'DEMO-' . strtoupper(bin2hex(random_bytes(8)));
                $conn->query(
                    "INSERT INTO payments
                     (booking_id, payment_method, transaction_id, amount, payment_status, payment_date)
                     VALUES ($bookingId, 'bKash Demo', '$demoTransactionId', $amount, 'paid', NOW())"
                );
                $conn->query(
                    "UPDATE bookings SET booking_status = 'confirmed'
                     WHERE booking_id = $bookingId AND user_id = $userId
                     AND booking_status = 'pending'"
                );
                if ($conn->affected_rows !== 1) {
                    throw new Exception('Booking could not be confirmed.');
                }

                $conn->commit();
                $transactionStarted = false;
                header('Location: payment.php?booking_id=' . $bookingId . '&demo=1');
                exit;
            }

            $callbackBase = rtrim($paymentConfig['bkash_sandbox_callback_url'], '?&');
            $callbackUrl = $callbackBase
                . (strpos($callbackBase, '?') === false ? '?' : '&')
                . 'booking_id=' . $bookingId . '&bkash_callback=1';

            $idToken = bkashSandboxAccessToken($paymentConfig);
            $createResponse = bkashSandboxRequest(
                $paymentConfig,
                '/tokenized/checkout/create',
                [
                    'mode' => '0011',
                    'payerReference' => (string)$userId,
                    'callbackURL' => $callbackUrl,
                    'amount' => number_format((float)$booking['total_amount'], 2, '.', ''),
                    'currency' => 'BDT',
                    'intent' => 'sale',
                    'merchantInvoiceNumber' => 'BT-' . $bookingId . '-' . strtoupper(bin2hex(random_bytes(4))),
                ],
                $idToken
            );

            $paymentId = (string)($createResponse['paymentID'] ?? '');
            $checkoutUrl = (string)($createResponse['bkashURL'] ?? '');
            $checkoutHost = parse_url($checkoutUrl, PHP_URL_HOST);
            if (
                !preg_match('/^[A-Za-z0-9._-]{1,100}$/', $paymentId) ||
                parse_url($checkoutUrl, PHP_URL_SCHEME) !== 'https' ||
                !preg_match('/(^|\.)bka\.sh$/i', (string)$checkoutHost)
            ) {
                error_log('bKash sandbox create response did not contain a valid checkout URL or payment ID.');
                throw new Exception('bKash sandbox could not start checkout.');
            }

            $conn->begin_transaction();
            $transactionStarted = true;
            $result = $conn->query(
                "SELECT total_amount, booking_status
                 FROM bookings
                 WHERE booking_id = $bookingId AND user_id = $userId
                 FOR UPDATE"
            );
            $lockedBooking = $result->fetch_assoc();
            if (!$lockedBooking || $lockedBooking['booking_status'] !== 'pending') {
                throw new Exception('This booking is no longer awaiting payment.');
            }

            $paidResult = $conn->query(
                "SELECT payment_id FROM payments
                 WHERE booking_id = $bookingId
                 AND payment_status = 'paid'
                 LIMIT 1 FOR UPDATE"
            );
            if ($paidResult->num_rows > 0) {
                throw new Exception('Payment has already been verified.');
            }

            $placeholder = $conn->real_escape_string('BKASH-PENDING-' . $paymentId);
            $amount = (float)$lockedBooking['total_amount'];
            $conn->query(
                "INSERT INTO payments
                 (booking_id, payment_method, transaction_id, amount, payment_status, payment_date)
                 VALUES ($bookingId, 'bKash Sandbox', '$placeholder', $amount, 'pending', NOW())"
            );
            $conn->commit();
            $transactionStarted = false;
            header('Location: ' . $checkoutUrl);
            exit;
        } catch (mysqli_sql_exception $exception) {
            if ($transactionStarted) {
                $conn->rollback();
            }
            error_log('bKash payment database error: ' . $exception->getMessage());
            $errors[] = 'Payment could not be started. Please try again.';
        } catch (Throwable $exception) {
            if ($transactionStarted) {
                $conn->rollback();
            }
            error_log('bKash payment error: ' . $exception->getMessage());
            $errors[] = $exception->getMessage();
        }
    }
}


// Load Payment Information
$payment = loadBkashPayment($conn, $bookingId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>bKash payment · Routeline Bus</title>
    <link rel="stylesheet" href="../assets/css/seat.css">
    <style>
        .payment-card { max-width: 700px; }
        .payment-card label { display: block; margin: 14px 0 5px; font-weight: 600; }
        .payment-card input { width: 100%; padding: 11px; border: 1px solid var(--line); border-radius: 8px; background: var(--card); color: var(--ink); font: inherit; }
        .notice { margin: 0 0 16px; padding: 12px 14px; border-radius: 10px; background: var(--taken); }
        .notice.error { border-left: 4px solid #dc3545; }
        .notice.success { border-left: 4px solid #16a34a; }
        .instructions { padding-left: 22px; }
    </style>
</head>
<body>
<header>
    <div class="bar">
        <div class="brand"><i>🚌</i>Routeline</div>
        <div class="steps">Step 3 of 3 · bKash payment</div>
    </div>
</header>

<main class="wrap">
    <div class="head">
        <a class="back" href="passenger_details.php?booking_id=<?php echo (int)$booking['booking_id']; ?>">← Back to booking</a>
        <h1>bKash payment</h1>
        <div class="muted">
            <?php echo escapePaymentHtml($booking['source']); ?> → <?php echo escapePaymentHtml($booking['destination']); ?>
            · <?php echo escapePaymentHtml($booking['bus_name']); ?>
            · Booking #<?php echo (int)$booking['booking_id']; ?>
        </div>
    </div>

    <section class="card payment-card">
        <?php foreach ($errors as $error): ?>
            <div class="notice error"><?php echo escapePaymentHtml($error); ?></div>
        <?php endforeach; ?>

        <?php if (isset($_GET['bkash_failed'])): ?>
            <div class="notice error">bKash checkout was cancelled or failed. You can try again.</div>
        <?php elseif (isset($_GET['bkash_error'])): ?>
            <div class="notice error">bKash payment could not be verified. Please contact support before trying again.</div>
        <?php elseif (isset($_GET['paid'])): ?>
            <div class="notice success">bKash sandbox payment verified and booking confirmed.</div>
        <?php elseif (isset($_GET['demo'])): ?>
            <div class="notice success">Demo payment successful and booking confirmed. No real money was moved.</div>
        <?php endif; ?>

        <p><strong>Seats:</strong> <?php echo escapePaymentHtml(implode(', ', $booking['seats'])); ?></p>
        <p><strong>Amount due: ৳ <?php echo number_format((float)$booking['total_amount'], 2); ?></strong></p>

        <?php if ($booking['booking_status'] !== 'pending'): ?>
            <div class="notice">This booking is <?php echo escapePaymentHtml($booking['booking_status']); ?> and is not awaiting payment.</div>
        <?php elseif ($payment && $payment['payment_status'] === 'paid'): ?>
            <div class="notice success">This bKash payment has been verified.</div>
        <?php else: ?>
            <?php if ($bkashSandboxReady): ?>
                <p>Test the real bKash sandbox checkout. No production payment is taken.</p>
                <form method="post" action="payment.php">
                    <input type="hidden" name="booking_id" value="<?php echo (int)$booking['booking_id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo escapePaymentHtml($_SESSION['bkash_payment_csrf']); ?>">
                    <input type="hidden" name="payment_mode" value="sandbox">
                    <button class="go" type="submit">Pay with bKash Sandbox</button>
                </form>
            <?php else: ?>
                <div class="notice error">
                    bKash sandbox is not configured. Add sandbox credentials and callback URL using the
                    <code>BKASH_SANDBOX_*</code> environment variables to enable it.
                </div>
            <?php endif; ?>

            <?php if ($bkashDemoMode): ?>
                <hr>
                <p><strong>Local demo:</strong> simulate a payment without connecting to bKash.</p>
                <form method="post" action="payment.php">
                    <input type="hidden" name="booking_id" value="<?php echo (int)$booking['booking_id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo escapePaymentHtml($_SESSION['bkash_payment_csrf']); ?>">
                    <input type="hidden" name="payment_mode" value="demo">
                    <button class="go" type="submit">Simulate Payment (Demo)</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
