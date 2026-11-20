
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
            AND payment_method IN ('bKash', 'bKash Demo')
            ORDER BY payment_id DESC
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        return null;
    }

    return $result->fetch_assoc();
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


// bKash Settings
$bkashMerchantNumber = trim($paymentConfig['bkash_merchant_number'] ?? '');

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

$errors = [];
$submitted = isset($_GET['submitted']) && $_GET['submitted'] == '1';
$transactionInput = '';


// Create CSRF Token
if (!isset($_SESSION['bkash_payment_csrf'])) {
    $_SESSION['bkash_payment_csrf'] = bin2hex(random_bytes(32));
}


// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $isDemoRequest = ($_POST['payment_mode'] ?? '') == 'demo';

    $transactionInput = $_POST['transaction_id'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!is_string($transactionInput)) {
        $transactionInput = '';
    }

    $transactionInput = trim($transactionInput);


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

    } elseif (!$isDemoRequest && $bkashMerchantNumber == '') {

        $errors[] = 'bKash payment is not configured yet.';

    } elseif (
        !$isDemoRequest &&
        !preg_match('/^[A-Za-z0-9_-]{4,100}$/', $transactionInput)
    ) {
        $errors[] = 'Enter a valid bKash transaction ID.';

    } else {

        $transactionStarted = false;

        try {

            // Start Transaction
            $conn->begin_transaction();
            $transactionStarted = true;


            // Check Booking
            $sql = "SELECT total_amount, booking_status
                    FROM bookings
                    WHERE booking_id = $bookingId
                    AND user_id = $userId
                    FOR UPDATE";

            $result = $conn->query($sql);
            $lockedBooking = $result->fetch_assoc();

            if (
                !$lockedBooking ||
                $lockedBooking['booking_status'] != 'pending'
            ) {
                throw new Exception('This booking is not awaiting payment.');
            }

            $amount = (float)$lockedBooking['total_amount'];


            // Check Already Paid
            $sql = "SELECT payment_id
                    FROM payments
                    WHERE booking_id = $bookingId
                    AND payment_method IN ('bKash', 'bKash Demo')
                    AND payment_status = 'paid'
                    LIMIT 1
                    FOR UPDATE";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                throw new Exception('Payment has already been verified.');
            }


            // Demo Payment
            if ($isDemoRequest) {

                $demoTransactionId = 'DEMO-' . strtoupper(bin2hex(random_bytes(8)));

                $sql = "INSERT INTO payments
                        (booking_id, payment_method, transaction_id,
                         amount, payment_status, payment_date)
                        VALUES (
                            $bookingId,
                            'bKash Demo',
                            '$demoTransactionId',
                            $amount,
                            'paid',
                            NOW()
                        )";

                $conn->query($sql);


                // Confirm Booking
                $sql = "UPDATE bookings
                        SET booking_status = 'confirmed'
                        WHERE booking_id = $bookingId
                        AND user_id = $userId
                        AND booking_status = 'pending'";

                $conn->query($sql);

                if ($conn->affected_rows != 1) {
                    throw new Exception('Booking could not be confirmed.');
                }

                $conn->commit();
                $transactionStarted = false;

                header('Location: payment.php?booking_id=' . $bookingId . '&demo=1');
                exit;

            } else {

                // Escape Transaction ID
                $safeTransactionId = $conn->real_escape_string($transactionInput);


                // Check Existing Payment
                $sql = "SELECT payment_id, payment_status
                        FROM payments
                        WHERE booking_id = $bookingId
                        AND payment_method = 'bKash'
                        ORDER BY payment_id DESC
                        LIMIT 1
                        FOR UPDATE";

                $result = $conn->query($sql);
                $existingPayment = $result->fetch_assoc();


                if (
                    $existingPayment &&
                    $existingPayment['payment_status'] == 'paid'
                ) {

                    throw new Exception('Payment has already been verified.');

                } elseif (
                    $existingPayment &&
                    $existingPayment['payment_status'] == 'pending'
                ) {

                    // Update Existing Payment
                    $paymentId = (int)$existingPayment['payment_id'];

                    $sql = "UPDATE payments
                            SET transaction_id = '$safeTransactionId',
                                amount = $amount,
                                payment_date = NOW()
                            WHERE payment_id = $paymentId";

                } else {

                    // Insert New Payment
                    $sql = "INSERT INTO payments
                            (booking_id, payment_method, transaction_id,
                             amount, payment_status, payment_date)
                            VALUES (
                                $bookingId,
                                'bKash',
                                '$safeTransactionId',
                                $amount,
                                'pending',
                                NOW()
                            )";
                }

                $conn->query($sql);

                $conn->commit();
                $transactionStarted = false;

                header('Location: payment.php?booking_id=' . $bookingId . '&submitted=1');
                exit;
            }

        } catch (mysqli_sql_exception $exception) {

            if ($transactionStarted) {
                $conn->rollback();
            }

            error_log('bKash payment error: ' . $exception->getMessage());

            $errors[] = 'Payment could not be completed. Please try again.';

        } catch (Exception $exception) {

            if ($transactionStarted) {
                $conn->rollback();
            }

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

        <?php if ($submitted && $payment && $payment['payment_status'] === 'pending'): ?>
            <div class="notice success">
                Transaction ID submitted. Payment is pending verification; your booking is not confirmed yet.
            </div>
        <?php endif; ?>

        <p><strong>Seats:</strong> <?php echo escapePaymentHtml(implode(', ', $booking['seats'])); ?></p>
        <p><strong>Amount due: ৳ <?php echo number_format((float)$booking['total_amount'], 2); ?></strong></p>

        <?php if ($payment && $payment['payment_method'] === 'bKash Demo' && $payment['payment_status'] === 'paid'): ?>
            <div class="notice success">Demo payment successful and booking confirmed. No real money was moved.</div>
        <?php elseif ($booking['booking_status'] !== 'pending'): ?>
            <div class="notice">This booking is <?php echo escapePaymentHtml($booking['booking_status']); ?> and is not awaiting payment.</div>
        <?php elseif ($payment && $payment['payment_status'] === 'paid'): ?>
            <div class="notice success">This bKash payment has been verified.</div>
        <?php elseif ($bkashMerchantNumber === ''): ?>
            <div class="notice error">
                <?php if ($bkashDemoMode): ?>
                    Real bKash payments are unavailable until a merchant number is configured. Use the demo button below for testing.
                <?php else: ?>
                    bKash merchant number is not configured. Set it in <code>config/payment.php</code> before accepting payments.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Pay the exact amount shown to this bKash merchant number, then submit the transaction ID. Do not enter your bKash PIN or OTP here.</p>
            <p><strong>bKash merchant number: <?php echo escapePaymentHtml($bkashMerchantNumber); ?></strong></p>
            <ol class="instructions">
                <li>Complete the payment in the bKash app or through your authorized bKash channel.</li>
                <li>Enter the transaction ID from the payment confirmation.</li>
                <li>The bus operator must verify the payment before the booking is confirmed.</li>
            </ol>
            <form method="post" action="payment.php">
                <input type="hidden" name="booking_id" value="<?php echo (int)$booking['booking_id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo escapePaymentHtml($_SESSION['bkash_payment_csrf']); ?>">
                <label for="transaction-id">bKash transaction ID</label>
                <input id="transaction-id" type="text" name="transaction_id" maxlength="100" required
                       value="<?php echo escapePaymentHtml($transactionInput !== '' ? $transactionInput : ($payment['transaction_id'] ?? '')); ?>">
                <button class="go" type="submit">
                    <?php echo $payment && $payment['payment_status'] === 'pending' ? 'Update transaction ID' : 'Submit transaction ID'; ?>
                </button>
            </form>
        <?php endif; ?>

        <?php if ($bkashDemoMode && $booking['booking_status'] === 'pending' &&
                  (!$payment || $payment['payment_status'] !== 'paid')): ?>
            <hr>
            <p><strong>Testing only:</strong> simulate payment without sending money.</p>
            <form method="post" action="payment.php">
                <input type="hidden" name="booking_id" value="<?php echo (int)$booking['booking_id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo escapePaymentHtml($_SESSION['bkash_payment_csrf']); ?>">
                <input type="hidden" name="payment_mode" value="demo">
                <button class="go" type="submit">Simulate bKash payment (demo only)</button>
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
