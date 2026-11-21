<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer' || !isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
if (!isset($_SESSION['cancel_booking_csrf'])) {
    $_SESSION['cancel_booking_csrf'] = bin2hex(random_bytes(32));
}

$result = $conn->query(
    "SELECT b.booking_id, b.booking_status, b.total_amount, b.booking_date,
            s.departure_date, s.departure_time,
            bus.bus_name, bus.bus_number,
            r.source, r.destination,
            COALESCE((
                SELECT SUM(p.amount)
                FROM payments p
                WHERE p.booking_id = b.booking_id
                AND p.payment_status = 'paid'
            ), 0) AS paid_amount
     FROM bookings b
     INNER JOIN schedules s ON b.schedule_id = s.schedule_id
     INNER JOIN buses bus ON s.bus_id = bus.bus_id
     INNER JOIN routes r ON s.route_id = r.route_id
     WHERE b.user_id = $userId
     ORDER BY b.booking_date DESC"
);
$bookings = [];
while ($booking = $result->fetch_assoc()) {
    $bookings[] = $booking;
}

function escapeTicketsHtml($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Tickets · Routeline Bus</title>
    <link rel="stylesheet" href="../assets/css/seat.css">
    <link rel="stylesheet" href="../assets/css/customer_topbar.css">
    <style>
        .tickets-wrap { max-width: 1000px; margin: 28px auto; padding: 0 20px 40px; }
        .tickets-heading { margin-bottom:18px; }
        .tickets-heading h1 { font-size:30px; }
        .tickets-heading p { color:var(--muted); margin:5px 0 0; }
        .tickets-list { display:grid; gap:14px; }
        .ticket-card { display:grid; grid-template-columns:1fr auto; gap:18px; align-items:center; padding:18px; background:var(--card); border:1px solid var(--line); border-radius:16px; }
        .ticket-card h2 { font-size:19px; margin:0 0 7px; }
        .ticket-card p { margin:4px 0; }
        .ticket-status { display:inline-block; margin-top:7px; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; text-transform:uppercase; }
        .ticket-status-confirmed { background:#d1e7dd; color:#0f5132; }
        .ticket-status-pending { background:#fff3cd; color:#664d03; }
        .ticket-status-cancelled { background:#f8d7da; color:#842029; }
        .ticket-actions { display:flex; justify-content:flex-end; gap:9px; flex-wrap:wrap; }
        .ticket-actions form { margin:0; }
        .ticket-view, .ticket-cancel { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:9px 14px; border:0; border-radius:9px; font-weight:700; text-decoration:none; cursor:pointer; }
        .ticket-view { background:var(--brand); color:var(--brand-ink); }
        .ticket-cancel { background:#dc3545; color:#fff; }
        .ticket-view:hover { opacity:.88; color:var(--brand-ink); }
        .ticket-cancel:hover { background:#bb2d3b; }
        .tickets-notice { margin-bottom:16px; padding:13px 15px; border-radius:10px; background:var(--taken); }
        .tickets-notice.success { border-left:4px solid #198754; }
        .tickets-notice.error { border-left:4px solid #dc3545; }
        .empty-tickets { padding:24px; background:var(--card); border:1px solid var(--line); border-radius:16px; color:var(--muted); }
        @media (max-width:650px) {
            .ticket-card { grid-template-columns:1fr; }
            .ticket-actions { justify-content:flex-start; }
            .tickets-heading h1 { font-size:25px; }
        }
    </style>
</head>
<body>
<?php include 'customer_topbar.php'; ?>
<main class="tickets-wrap">
    <?php if (isset($_GET['cancelled'])): ?>
        <div class="tickets-notice success">
            Ticket cancelled. Cancellation charge: ৳ <?php echo number_format((float)($_GET['charge'] ?? 0), 2); ?>.
            Refund due: ৳ <?php echo number_format((float)($_GET['refund'] ?? 0), 2); ?>.
            <?php if ((float)($_GET['refund'] ?? 0) > 0): ?>Refund status: pending processing.<?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['cancel_error'])): ?>
        <div class="tickets-notice error"><?php echo escapeTicketsHtml($_GET['cancel_error']); ?></div>
    <?php endif; ?>

    <div class="tickets-heading">
        <h1>My Tickets</h1>
        <p>View, print, or cancel your bookings.</p>
    </div>

    <div class="tickets-list">
        <?php if (count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $status = strtolower($booking['booking_status']);
                    $departureTimestamp = strtotime($booking['departure_date'] . ' ' . $booking['departure_time']);
                    $canCancel = in_array($status, ['pending', 'confirmed'], true) && $departureTimestamp > time();
                    $paidAmount = min((float)$booking['paid_amount'], (float)$booking['total_amount']);
                    $hoursBeforeDeparture = ($departureTimestamp - time()) / 3600;
                    $chargePercent = $status === 'confirmed' && $paidAmount > 0
                        ? ($hoursBeforeDeparture >= 5 ? 60 : 100)
                        : 0;
                    $cancellationCharge = round($paidAmount * $chargePercent / 100, 2);
                    $refundAmount = round($paidAmount - $cancellationCharge, 2);
                    $refundPercent = 100 - $chargePercent;
                ?>
                <article class="ticket-card">
                    <div>
                        <h2><?php echo escapeTicketsHtml($booking['source']); ?> → <?php echo escapeTicketsHtml($booking['destination']); ?></h2>
                        <p>
                            Booking #<?php echo (int)$booking['booking_id']; ?>
                            · <?php echo escapeTicketsHtml($booking['bus_name'] . ' (' . $booking['bus_number'] . ')'); ?>
                        </p>
                        <p>
                            <?php echo escapeTicketsHtml(date('d M Y, h:i A', $departureTimestamp)); ?>
                            · ৳ <?php echo number_format((float)$booking['total_amount'], 2); ?>
                        </p>
                        <span class="ticket-status ticket-status-<?php echo escapeTicketsHtml($status); ?>">
                            <?php echo escapeTicketsHtml($status); ?>
                        </span>
                    </div>
                    <div class="ticket-actions">
                        <?php if ($status === 'confirmed'): ?>
                            <a class="ticket-view" href="ticket.php?booking_id=<?php echo (int)$booking['booking_id']; ?>">View / Print Ticket</a>
                        <?php endif; ?>
                        <?php if ($canCancel): ?>
                            <form action="cancel_booking.php" method="post" onsubmit="return confirm('Cancel this ticket? Cancellation charge: <?php echo $chargePercent; ?>%. Refund: <?php echo $refundPercent; ?>% (৳ <?php echo number_format($refundAmount, 2); ?>).');">
                                <input type="hidden" name="booking_id" value="<?php echo (int)$booking['booking_id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo escapeTicketsHtml($_SESSION['cancel_booking_csrf']); ?>">
                                <button type="submit" class="ticket-cancel">Cancel Ticket</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-tickets">You have no tickets yet.</div>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
