<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$booking_id = $_GET['booking_id'] ?? 0;


$sql = "SELECT booking_seats.passenger_name,
        booking_seats.passenger_phone,
        seats.seat_number,
        bookings.booking_id,
        bookings.booking_status,
        users.name AS booked_by,
        buses.bus_name,
        routes.source,
        routes.destination,
        schedules.departure_date
        FROM booking_seats
        INNER JOIN bookings ON booking_seats.booking_id = bookings.booking_id
        INNER JOIN users ON bookings.user_id = users.user_id
        INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
        INNER JOIN buses ON schedules.bus_id = buses.bus_id
        INNER JOIN routes ON schedules.route_id = routes.route_id
        LEFT JOIN seats ON booking_seats.seat_id = seats.seat_id
        WHERE buses.company_id = '$company_id'";


if ($booking_id > 0) {
    $sql .= " AND bookings.booking_id = '$booking_id'";
}

$sql .= " ORDER BY bookings.booking_date DESC, booking_seats.booking_seats";

$passengers = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passengers | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/cmsidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header">
            <div><h3>Passengers</h3><p>Passenger details for your company bookings.</p></div>
            <?php if ($booking_id > 0) { ?><a class="btn btn-secondary" href="bookings.php">All Bookings</a><?php } ?>
        </div>
        <div class="company-card">
            <div class="table-header">
                <h5>All Passengers</h5>
                <input type="text" id="searchInput" class="form-control search-box" placeholder="Search passengers...">
            </div>
            <div class="table-responsive">
                <table class="table company-table" id="companyTable">
                    <thead>
                        <tr><th>Passenger</th><th>Phone</th><th>Seat</th><th>Booking</th><th>Booked By</th><th>Bus</th><th>Route</th><th>Departure</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($passenger = $passengers->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo ($passenger['passenger_name']); ?></td>
                                <td><?php echo ($passenger['passenger_phone']); ?></td>
                                <td><?php echo ($passenger['seat_number'] ?? 'N/A'); ?></td>
                                <td><?php echo (int)$passenger['booking_id']; ?></td>
                                <td><?php echo ($passenger['booked_by']); ?></td>
                                <td><?php echo ($passenger['bus_name']); ?></td>
                                <td><?php echo ($passenger['source'] . ' → ' . $passenger['destination'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo ($passenger['departure_date']); ?></td>
                                <td><?php echo ($passenger['booking_status']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
