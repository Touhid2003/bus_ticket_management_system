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

$bookingId = (int)($_GET['booking_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if ($bookingId < 1) {
    header('Location: home.php');
    exit;
}

$sql = "SELECT b.booking_id, b.total_amount, b.booking_status, b.booking_date,
               s.departure_date, s.departure_time, s.arrival_time,
               bus.bus_name, bus.bus_number, bus.bus_type,
               r.source, r.destination,
               u.name AS passenger_name
        FROM bookings b
        INNER JOIN users u ON b.user_id = u.user_id
        INNER JOIN schedules s ON b.schedule_id = s.schedule_id
        INNER JOIN buses bus ON s.bus_id = bus.bus_id
        INNER JOIN routes r ON s.route_id = r.route_id
        WHERE b.booking_id = $bookingId
        AND b.user_id = $userId";

$result = $conn->query($sql);
$ticket = $result->fetch_assoc();

if (!$ticket) {
    http_response_code(404);
    exit('Ticket not found.');
}

if ($ticket['booking_status'] !== 'confirmed') {
    header('Location: passenger_details.php?booking_id=' . $bookingId);
    exit;
}

$seatSql = "SELECT seats.seat_number, booking_seats.passenger_name,
                   booking_seats.passenger_phone, booking_seats.seat_price
            FROM booking_seats
            INNER JOIN seats ON booking_seats.seat_id = seats.seat_id
            WHERE booking_seats.booking_id = $bookingId
            ORDER BY seats.seat_number";
$seatResult = $conn->query($seatSql);
$seats = [];
while ($row = $seatResult->fetch_assoc()) {
    $seats[] = $row;
}

function escapeTicketHtml($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bus Ticket #<?php echo (int)$ticket['booking_id']; ?></title>
    <link rel="stylesheet" href="../assets/css/seat.css">
    <style>
        .ticket-wrap { max-width: 760px; margin: 24px auto; }
        .ticket-card { background: #fff; color: #10282f; border: 2px dashed #8aa0a7; border-radius: 18px; padding: 24px; }
        .ticket-head { display:flex; justify-content:space-between; gap:20px; padding-bottom:16px; border-bottom:2px solid #dde5e7; }
        .ticket-status { background:#198754; color:#fff; padding:6px 12px; border-radius:999px; font-weight:700; }
        .ticket-route { display:grid; grid-template-columns:1fr auto 1fr; align-items:center; text-align:center; gap:16px; margin:24px 0; }
        .ticket-route strong { display:block; font-size:24px; }
        .ticket-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:12px; }
        .ticket-grid div { background:#f3f6f7; border-radius:10px; padding:12px; }
        .ticket-grid small { display:block; color:#5a6f76; }
        .ticket-seats { margin-top:18px; }
        .ticket-actions { display:flex; gap:10px; margin-top:16px; }
        @media print {
            body { background:#fff; }
            header, .ticket-actions, .back { display:none !important; }
            .ticket-wrap { margin:0; max-width:none; }
            .ticket-card { border:2px solid #333; }
        }
        @media (max-width:600px) {
            .ticket-head, .ticket-route { display:block; }
            .ticket-grid { grid-template-columns:1fr; }
            .ticket-route strong { margin:8px 0; }
        }
    </style>
</head>
<body>
<header>
    <div class="bar">
        <div class="brand"><i>🚌</i>Routeline</div>
        <div class="steps">Confirmed Ticket</div>
    </div>
</header>
<main class="ticket-wrap">
    <a class="back" href="passenger_details.php?booking_id=<?php echo $bookingId; ?>">← Back to booking</a>
    <section class="ticket-card">
        <div class="ticket-head">
            <div>
                <h1 style="font-size:24px;">Routeline Bus Ticket</h1>
                <p style="margin:4px 0;">Booking #<?php echo $bookingId; ?></p>
            </div>
            <div class="ticket-status">CONFIRMED</div>
        </div>

        <div class="ticket-route">
            <div><small>From</small><strong><?php echo escapeTicketHtml($ticket['source']); ?></strong></div>
            <div>→</div>
            <div><small>To</small><strong><?php echo escapeTicketHtml($ticket['destination']); ?></strong></div>
        </div>

        <div class="ticket-grid">
            <div><small>Passenger</small><strong><?php echo escapeTicketHtml($ticket['passenger_name']); ?></strong></div>
            <div><small>Bus</small><strong><?php echo escapeTicketHtml($ticket['bus_name'] . ' (' . $ticket['bus_number'] . ')'); ?></strong></div>
            <div><small>Bus Type</small><strong><?php echo escapeTicketHtml($ticket['bus_type']); ?></strong></div>
            <div><small>Departure</small><strong><?php echo escapeTicketHtml(date('d M Y, h:i A', strtotime($ticket['departure_date'] . ' ' . $ticket['departure_time']))); ?></strong></div>
            <div><small>Arrival</small><strong><?php echo escapeTicketHtml(date('d M Y, h:i A', strtotime($ticket['departure_date'] . ' ' . $ticket['arrival_time']))); ?></strong></div>
            <div><small>Total Amount</small><strong>৳ <?php echo number_format((float)$ticket['total_amount'], 2); ?></strong></div>
        </div>

        <div class="ticket-seats">
            <strong>Seats</strong>
            <?php if (count($seats) > 0): ?>
                <ul>
                    <?php foreach ($seats as $seat): ?>
                        <li>
                            Seat <?php echo escapeTicketHtml($seat['seat_number']); ?> —
                            <?php echo escapeTicketHtml($seat['passenger_name']); ?>
                            (৳ <?php echo number_format((float)$seat['seat_price'], 2); ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
    <div class="ticket-actions">
        <button type="button" class="go" onclick="window.print()">Print Ticket</button>
    </div>
</main>
</body>
</html>
