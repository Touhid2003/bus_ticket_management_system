<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once '../config/db.php';

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'customer' ||
    !isset($_SESSION['user_id'])
) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.php');
    exit;
}

$bookingId = (int)($_POST['booking_id'] ?? 0);
$reason = trim((string)($_POST['reason'] ?? ''));
$csrfToken = (string)($_POST['csrf_token'] ?? '');

if (
    $bookingId < 1 ||
    !isset($_SESSION['cancel_booking_csrf']) ||
    !hash_equals($_SESSION['cancel_booking_csrf'], $csrfToken)
) {
    header('Location: tickets.php?cancel_error=' . rawurlencode('Invalid cancellation request.'));
    exit;
}

$userId = (int)$_SESSION['user_id'];
$reason = substr($reason, 0, 500);
$transactionStarted = false;

try {
    $conn->begin_transaction();
    $transactionStarted = true;

    $sql = "SELECT b.booking_id, b.total_amount, b.booking_status,
                   s.departure_date, s.departure_time
            FROM bookings b
            INNER JOIN schedules s ON b.schedule_id = s.schedule_id
            WHERE b.booking_id = $bookingId
            AND b.user_id = $userId
            FOR UPDATE";

    $result = $conn->query($sql);
    $booking = $result->fetch_assoc();

    if (!$booking) {
        throw new Exception('Booking not found.');
    }
    if ($booking['booking_status'] === 'cancelled') {
        throw new Exception('This booking is already cancelled.');
    }
    if (!in_array($booking['booking_status'], ['pending', 'confirmed'], true)) {
        throw new Exception('This booking cannot be cancelled.');
    }

    $departureTimestamp = strtotime($booking['departure_date'] . ' ' . $booking['departure_time']);
    $secondsBeforeDeparture = $departureTimestamp - time();

    if ($secondsBeforeDeparture <= 0) {
        throw new Exception('The bus departure time has passed.');
    }

    $totalAmount = (float)$booking['total_amount'];

    $sql = "SELECT amount
            FROM payments
            WHERE booking_id = $bookingId
            AND payment_status = 'paid'
            FOR UPDATE";
    $paymentResult = $conn->query($sql);
    $paidAmount = 0.0;
    while ($payment = $paymentResult->fetch_assoc()) {
        $paidAmount += (float)$payment['amount'];
    }
    $paidAmount = min($paidAmount, $totalAmount);

    if ($booking['booking_status'] === 'confirmed' && $paidAmount > 0) {
        $chargePercent = $secondsBeforeDeparture >= 5 * 3600 ? 60 : 100;
    } else {
        $chargePercent = 0;
    }

    $cancellationCharge = round($paidAmount * $chargePercent / 100, 2);
    $refundAmount = round($paidAmount - $cancellationCharge, 2);

    $safeReason = $conn->real_escape_string($reason);
    $sql = "INSERT INTO cancellations
            (booking_id, user_id, reason, cancellation_charge, refund_amount,
             cancellation_status, requested_at)
            VALUES (
                $bookingId, $userId, '$safeReason', $cancellationCharge, $refundAmount,
                'approved', NOW()
            )";
    $conn->query($sql);
    $cancellationId = $conn->insert_id;

    if ($refundAmount > 0) {
        $refundTransactionId = $conn->real_escape_string('CANCEL-' . $cancellationId);
        $sql = "INSERT INTO refunds
                (booking_id, cancellation_id, refund_amount, refund_method,
                 transaction_id, refund_status, refund_date)
                VALUES (
                    $bookingId, $cancellationId, $refundAmount, 'Original Payment',
                    '$refundTransactionId', 'pending', NOW()
                )";
        $conn->query($sql);
    }

    $conn->query(
        "UPDATE bookings
         SET booking_status = 'cancelled'
         WHERE booking_id = $bookingId
         AND user_id = $userId"
    );

    $conn->commit();
    unset($_SESSION['cancel_booking_csrf']);

    header(
        'Location: tickets.php?cancelled=1&charge='
        . urlencode(number_format($cancellationCharge, 2, '.', ''))
        . '&refund=' . urlencode(number_format($refundAmount, 2, '.', ''))
    );
    exit;
} catch (mysqli_sql_exception $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }
    error_log('Booking cancellation failed: ' . $exception->getMessage());
    header('Location: tickets.php?cancel_error=' . rawurlencode('Ticket cancellation failed. Please try again.'));
    exit;
} catch (Exception $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }
    header('Location: tickets.php?cancel_error=' . rawurlencode($exception->getMessage()));
    exit;
}
