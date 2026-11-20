<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$sql = "SELECT bookings.booking_id, users.name, buses.bus_name, routes.source,
        routes.destination, bookings.booking_date, bookings.total_amount,
        bookings.booking_status
        FROM bookings
        INNER JOIN users ON bookings.user_id = users.user_id
        INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
        INNER JOIN buses ON schedules.bus_id = buses.bus_id
        INNER JOIN routes ON schedules.route_id = routes.route_id
        ORDER BY bookings.booking_date DESC";
$bookings = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookings | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header"><div><h3>Bookings</h3><p>View all customer bookings.</p></div></div>
        <div class="company-card">
            <div class="table-responsive">
                <table class="table company-table">
                    <thead><tr><th>#</th><th>Customer</th><th>Bus</th><th>Route</th><th>Booked At</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php while ($booking = $bookings->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $booking['booking_id']; ?></td>
                            <td><?php echo $booking['name']; ?></td>
                            <td><?php echo $booking['bus_name']; ?></td>
                            <td><?php echo $booking['source'] . ' → ' . $booking['destination']; ?></td>
                            <td><?php echo $booking['booking_date']; ?></td>
                            <td><?php echo number_format((float)$booking['total_amount'], 2); ?> TK</td>
                            <td><?php echo $booking['booking_status']; ?></td>
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
