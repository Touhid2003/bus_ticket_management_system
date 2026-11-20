<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];

// Get Company Bookings
$sql = "SELECT bookings.booking_id, users.name, buses.bus_name,
        routes.source, routes.destination, schedules.departure_date,
        bookings.booking_date, bookings.total_amount,
        bookings.booking_status
        FROM bookings
        INNER JOIN users ON bookings.user_id = users.user_id
        INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
        INNER JOIN buses ON schedules.bus_id = buses.bus_id
        INNER JOIN routes ON schedules.route_id = routes.route_id
        WHERE buses.company_id = '$company_id'
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
    <?php include '../includes/cmsidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header">
            <div><h3>Bookings</h3><p>Bookings for your company buses.</p></div>
        </div>
        <div class="company-card">
            <div class="table-header">
                <h5>All Bookings</h5>
                <input type="text" id="searchInput" class="form-control search-box" placeholder="Search bookings...">
            </div>
            <div class="table-responsive">
                <table class="table company-table" id="companyTable">
                    <thead>
                        <tr><th>#</th><th>Customer</th><th>Bus</th><th>Route</th><th>Departure</th><th>Booked At</th><th>Amount</th><th>Status</th><th>Passengers</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo (int)$booking['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['bus_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['source'] . ' → ' . $booking['destination'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['departure_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo number_format((float)$booking['total_amount'], 2); ?> TK</td>
                                <td><?php echo htmlspecialchars($booking['booking_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a class="action-btn view" href="passengers.php?booking_id=<?php echo (int)$booking['booking_id']; ?>">View</a></td>
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
