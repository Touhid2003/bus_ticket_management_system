
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer' || !isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// HTML Escape
function escapeHtml($value)
{
    return htmlspecialchars($value);
}

// Load Schedule
function loadSchedule($conn, $scheduleId)
{
    $scheduleId = (int)$scheduleId;

    $sql = "SELECT s.schedule_id, s.bus_id, s.departure_date, s.departure_time,
                   s.ticket_price, b.bus_name, r.source, r.destination
            FROM schedules s
            INNER JOIN buses b ON s.bus_id = b.bus_id
            INNER JOIN routes r ON s.route_id = r.route_id
            WHERE s.schedule_id = $scheduleId
            AND s.status = 'scheduled'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

// Load Selected Seats
function loadSelectedSeats($conn, $busId, $seatIds, $lock = false)
{
    $seats = [];

    $busId = (int)$busId;

    foreach ($seatIds as $seatId) {

        $seatId = (int)$seatId;

        $sql = "SELECT seat_id, seat_number
                FROM seats
                WHERE bus_id = $busId
                AND seat_id = $seatId";

        if ($lock) {
            $sql .= " FOR UPDATE";
        }

        $result = $conn->query($sql);

        if ($row = $result->fetch_assoc()) {
            $seats[$row['seat_id']] = $row['seat_number'];
        }
    }

    return $seats;
}

// Find Booked Seats
function findBookedSeats($conn, $scheduleId, $seatIds)
{
    $bookedSeats = [];

    $scheduleId = (int)$scheduleId;

    foreach ($seatIds as $seatId) {

        $seatId = (int)$seatId;

        $sql = "SELECT bs.seat_id
                FROM booking_seats bs
                INNER JOIN bookings b ON bs.booking_id = b.booking_id
                WHERE b.schedule_id = $scheduleId
                AND b.booking_status IN ('pending', 'confirmed')
                AND bs.seat_id = $seatId";

        $result = $conn->query($sql);

        if ($row = $result->fetch_assoc()) {
            $bookedSeats[] = $row['seat_id'];
        }
    }

    return $bookedSeats;
}

// Load Booking
function loadBooking($conn, $bookingId, $userId)
{
    $bookingId = (int)$bookingId;
    $userId = (int)$userId;

    $sql = "SELECT b.booking_id, b.booking_status, b.total_amount,
                   s.departure_date, s.departure_time, bus.bus_name,
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
            WHERE b.booking_id = $bookingId
            AND b.user_id = $userId";

    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        return null;
    }

    $booking = $result->fetch_assoc();
    $booking['seats'] = [];

    $sql = "SELECT s.seat_number, bs.passenger_name, bs.passenger_phone
            FROM booking_seats bs
            INNER JOIN seats s ON bs.seat_id = s.seat_id
            WHERE bs.booking_id = $bookingId
            ORDER BY s.seat_number";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $booking['seats'][] = $row;
    }

    return $booking;
}

if (!isset($_SESSION['cancel_booking_csrf'])) {
    $_SESSION['cancel_booking_csrf'] = bin2hex(random_bytes(32));
}


// Show Booking Details
if (isset($_GET['booking_id'])) {

    $bookingId = (int)$_GET['booking_id'];

    $booking = loadBooking($conn, $bookingId, $_SESSION['user_id']);

    if (!$booking) {
        http_response_code(404);
    }

} else {

    // Check POST Request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: home.php');
        exit;
    }

    $scheduleId = (int)($_POST['schedule_id'] ?? 0);

    if ($scheduleId < 1) {
        header('Location: home.php');
        exit;
    }

    $schedule = loadSchedule($conn, $scheduleId);

    if (!$schedule) {
        http_response_code(404);
        exit('This bus schedule is unavailable.');
    }

    // Get Selected Seat IDs
    $seatIds = $_POST['seat_ids'] ?? [];

    if (!is_array($seatIds) || count($seatIds) < 1 || count($seatIds) > 4) {
        header('Location: seat.php?schedule_id=' . $scheduleId);
        exit;
    }

    $selectedSeats = [];

    foreach ($seatIds as $seatId) {

        $seatId = (int)$seatId;

        if ($seatId < 1 || in_array($seatId, $selectedSeats)) {
            header('Location: seat.php?schedule_id=' . $scheduleId);
            exit;
        }

        $selectedSeats[] = $seatId;
    }

    $seatIds = $selectedSeats;

    // Load Seats
    $seats = loadSelectedSeats($conn, $schedule['bus_id'], $seatIds);

    $errors = [];

    $submittedName = $_POST['passenger_name'] ?? '';
   
    $submittedPhone = $_POST['passenger_phone'] ?? '';
     $isBookingSubmit = isset($_POST['booking_submit']);
    

    if (!is_string($submittedName)) {
        $submittedName = '';
    }

    if (!is_string($submittedPhone)) {
        $submittedPhone = '';
    }

    $selectionAvailable = true;

    if (count($seats) != count($seatIds)) {
        $selectionAvailable = false;
        $errors[] = 'One or more selected seats are not available for this bus.';
    }

    if (findBookedSeats($conn, $scheduleId, $seatIds)) {
        $selectionAvailable = false;
        $errors[] = 'One or more selected seats have just been booked. Please choose again.';
    }

   
if ($isBookingSubmit) {

    if (strlen($submittedName) > 100 || $submittedName == '') {
        $errors[] = 'Enter a passenger name of no more than 100 characters.';
    }

    // Passenger Phone Validation
    if (strlen($submittedPhone) < 7 || strlen($submittedPhone) > 20) {
        $errors[] = 'Enter a valid phone number.';
    }
}

    // Booking Process
    if ($isBookingSubmit && empty($errors)){

        $sortedSeatIds = $seatIds;
        sort($sortedSeatIds);

        try {

            $conn->begin_transaction();

            // Lock Selected Seats
            $lockedSeats = loadSelectedSeats(
                $conn,
                $schedule['bus_id'],
                $sortedSeatIds,
                true
            );

            if (count($lockedSeats) != count($sortedSeatIds)) {
                throw new Exception('Selected seats are unavailable.');
            }

            if (findBookedSeats($conn, $scheduleId, $sortedSeatIds)) {
                throw new Exception('Selected seats are already booked.');
            }

            // Calculate Total Amount
            $totalAmount = $schedule['ticket_price'] * count($seatIds);

            $userId = (int)$_SESSION['user_id'];

            // Escape Passenger Information
            $name = $conn->real_escape_string($submittedName);
            $phone = $conn->real_escape_string($submittedPhone);

            // Insert Booking
            $sql = "INSERT INTO bookings
                    (user_id, schedule_id, booking_date, total_amount, booking_status)
                    VALUES ($userId, $scheduleId, NOW(), $totalAmount, 'pending')";

            $conn->query($sql);

            $bookingId = $conn->insert_id;

            // Insert Booking Seats
            $price = $schedule['ticket_price'];

            foreach ($seatIds as $seatId) {

                $sql = "INSERT INTO booking_seats
                        (booking_id, seat_id, passenger_name, passenger_phone, seat_price)
                        VALUES ($bookingId, $seatId, '$name', '$phone', $price)";

                $conn->query($sql);
            }

            // Complete Transaction
            $conn->commit();

            header('Location: payment.php?booking_id=' . $bookingId);
            exit;

        } catch (Exception $exception) {

            $conn->rollback();

            $selectionAvailable = false;

            $errors[] = 'The booking could not be completed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo isset($booking) && $booking ? 'Booking received' : 'Passenger details'; ?> · Routeline Bus</title>
    <link rel="stylesheet" href="../assets/css/seat.css">
    <style>
        .passenger-fields {
            margin: 18px 0;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        .passenger-fields h2 {
            margin-bottom: 12px;
            font-size: 18px;
        }

        .passenger-fields label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 600;
        }

        .passenger-fields input {
            width: 100%;
            padding: 11px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--card);
            color: var(--ink);
            font: inherit;
        }

        .notice {
            margin: 0 0 16px;
            padding: 12px 14px;
            border-radius: 10px;
            background: var(--taken);
        }

        .notice.error {
            border-left: 4px solid #dc3545;
        }

        .notice.success {
            border-left: 4px solid #16a34a;
        }

        .booking-seats {
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <header>
        <div class="bar">
            <div class="brand"><i>🚌</i>Routeline</div>
            <div class="steps">Step 2 of 3 · Passenger details</div>
        </div>
    </header>

    <main class="wrap">
        <?php if (isset($booking)): ?>
            <div class="head">
                <a class="back" href="home.php">← Back to buses</a>
                <h1><?php echo $booking ? 'Booking received' : 'Booking not found'; ?></h1>
                <?php if ($booking): ?>
                    <div class="muted">Booking #<?php echo (int)$booking['booking_id']; ?></div>
                <?php endif; ?>
            </div>
            <section class="card">
                <?php if ($booking): ?>
                    <?php if (isset($_GET['cancelled'])): ?>
                        <div class="notice success">
                            Booking cancelled successfully.<br>
                            Cancellation charge: <strong>৳ <?php echo number_format((float)($_GET['charge'] ?? 0), 2); ?></strong><br>
                            Refund amount: <strong>৳ <?php echo number_format((float)($_GET['refund'] ?? 0), 2); ?></strong>
                        </div>
                    <?php elseif (isset($_GET['cancel_error'])): ?>
                        <div class="notice error"><?php echo escapeHtml((string)$_GET['cancel_error']); ?></div>
                    <?php endif; ?>
                    <div class="notice success">
                        Your booking is <strong><?php echo escapeHtml(ucfirst($booking['booking_status'])); ?></strong>.
                        <?php if ($booking['booking_status'] === 'pending'): ?>
                            Complete payment by bKash to finish your booking.
                        <?php elseif ($booking['booking_status'] === 'cancelled'): ?>
                            This booking is no longer active.
                        <?php endif; ?>
                    </div>
                    <p><strong><?php echo escapeHtml($booking['source']); ?> → <?php echo escapeHtml($booking['destination']); ?></strong></p>
                    <p class="muted">
                        <?php echo escapeHtml($booking['bus_name']); ?> ·
                        <?php echo escapeHtml(date('D, d M Y', strtotime($booking['departure_date']))); ?> ·
                        <?php echo escapeHtml(date('h:i A', strtotime($booking['departure_time']))); ?>
                    </p>
                    <ul class="booking-seats">
                        <?php foreach ($booking['seats'] as $seat): ?>
                            <li>
                                Seat <?php echo escapeHtml($seat['seat_number']); ?> —
                                <?php echo escapeHtml($seat['passenger_name']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p><strong>Total: ৳ <?php echo number_format((float)$booking['total_amount'], 2); ?></strong></p>
                    <?php if ($booking['booking_status'] === 'pending'): ?>
                        <a class="go" href="payment.php?booking_id=<?php echo (int)$booking['booking_id']; ?>">Continue to bKash payment</a>
                        <form action="cancel_booking.php" method="post" onsubmit="return confirm('Cancel this unpaid booking?');" style="margin-top:12px;">
                            <input type="hidden" name="booking_id" value="<?php echo (int)$booking['booking_id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo escapeHtml($_SESSION['cancel_booking_csrf'] ?? ''); ?>">
                            <button type="submit" class="go" style="border:0;background:#dc3545;">Cancel Unpaid Booking</button>
                        </form>
                    <?php elseif ($booking['booking_status'] === 'confirmed'): ?>
                        <?php
                            $departureTimestamp = strtotime($booking['departure_date'] . ' ' . $booking['departure_time']);
                            $hoursBeforeDeparture = ($departureTimestamp - time()) / 3600;
                        ?>
                        <a class="go" href="ticket.php?booking_id=<?php echo (int)$booking['booking_id']; ?>" style="display:inline-block;text-align:center;margin-bottom:12px;">View Ticket</a>
                        <?php if ($hoursBeforeDeparture > 0): ?>
                            <?php
                                $paidAmount = min((float)$booking['paid_amount'], (float)$booking['total_amount']);
                                $chargePercent = $hoursBeforeDeparture >= 5 ? 60 : 100;
                                $chargeAmount = $paidAmount * $chargePercent / 100;
                                $refundAmount = $paidAmount - $chargeAmount;
                                $refundPercent = 100 - $chargePercent;
                            ?>
                            <div class="notice">
                                Cancellation charge: <strong><?php echo $chargePercent; ?>%</strong>
                                (৳ <?php echo number_format($chargeAmount, 2); ?>)<br>
                                Refund: <strong><?php echo $refundPercent; ?>%</strong>
                                (৳ <?php echo number_format($refundAmount, 2); ?>)
                            </div>
                            <form action="cancel_booking.php" method="post" onsubmit="return confirm('Cancel this booking?');">
                                <input type="hidden" name="booking_id" value="<?php echo (int)$booking['booking_id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo escapeHtml($_SESSION['cancel_booking_csrf'] ?? ''); ?>">
                                <label for="cancel_reason">Reason (optional)</label>
                                <input type="text" id="cancel_reason" name="reason" maxlength="500" placeholder="Enter cancellation reason">
                                <button type="submit" class="go" style="border:0;margin-top:12px;background:#dc3545;">Cancel Booking</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="notice error">Booking not found.</div>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <div class="head">
                <a class="back" href="seat.php?schedule_id=<?php echo (int)$schedule['schedule_id']; ?>">← Back to seat selection</a>
                <h1>Passenger details</h1>
                <div class="muted">
                    <?php echo escapeHtml($schedule['source']); ?> → <?php echo escapeHtml($schedule['destination']); ?>
                    · <?php echo escapeHtml($schedule['bus_name']); ?>
                    · <?php echo escapeHtml(date('D, d M Y', strtotime($schedule['departure_date']))); ?>
                </div>
            </div>

            <section class="card">
                <?php foreach ($errors as $error): ?>
                    <div class="notice error"><?php echo $error; ?></div>
                <?php endforeach; ?>

                <?php if ($selectionAvailable): ?>
                    <form method="post" action="passenger_details.php">
                        <input type="hidden" name="booking_submit" value="1">
                        <input type="hidden" name="schedule_id" value="<?php echo (int)$schedule['schedule_id']; ?>">
                        
                        <?php foreach ($seatIds as $seatId): ?>
                            <input type="hidden" name="seat_ids[]" value="<?php echo (int)$seatId; ?>">
                        <?php endforeach; ?>
                        <fieldset class="passenger-fields">
                            <h2>Passenger details</h2>
                            <p class="muted">
                                Seats:
                                <?php foreach ($seatIds as $index => $seatId): ?>
                                    <?php echo $index ? ', ' : ''; ?><?php echo escapeHtml($seats[$seatId]); ?>
                                <?php endforeach; ?>
                            </p>
                            <p class="muted">These details will be used for all selected seats.</p>
                            <label for="passenger-name">Full name</label>
                            <input id="passenger-name" type="text" name="passenger_name" maxlength="100" required
                                value="<?php echo escapeHtml($submittedName); ?>">
                            <label for="passenger-phone">Phone number</label>
                            <input id="passenger-phone" type="tel" name="passenger_phone" maxlength="20" required
                                value="<?php echo escapeHtml($submittedPhone); ?>">
                        </fieldset>
                        <button class="go" type="submit">Confirm booking</button>
                    </form>
                <?php else: ?>
                    <a class="go" href="seat.php?schedule_id=<?php echo (int)$schedule['schedule_id']; ?>">Choose seats again</a>
                <?php endif; ?>
            </section>

            <aside class="card sum">
                <h2>Your trip</h2>
                <div class="rt"><?php echo escapeHtml($schedule['source']); ?> → <?php echo escapeHtml($schedule['destination']); ?></div>
                <div class="muted">Departure: <?php echo escapeHtml(date('h:i A', strtotime($schedule['departure_time']))); ?></div>
                <ul class="list">
                    <?php foreach ($seatIds as $seatId): ?>
                        <?php if (isset($seats[$seatId])): ?>
                            <li>
                                <span>Seat <?php echo escapeHtml($seats[$seatId]); ?></span>
                                <span>৳ <?php echo number_format((float)$schedule['ticket_price'], 2); ?></span>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="tot">
                    <span>Total</span>
                    <b>৳ <?php echo number_format((float)$schedule['ticket_price'] * count($seatIds), 2); ?></b>
                </div>
            </aside>
        <?php endif; ?>
    </main>
</body>

</html>