<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_GET['user_id'];

// Get Customer Information
$sql = "SELECT * FROM users
        WHERE user_id = '$user_id'
        AND role = 'customer'";

$result = $conn->query($sql);
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: users.php');
    exit;
}

// Get Customer Booking Information
$sql = "SELECT bookings.booking_id, bookings.booking_date,
        bookings.total_amount, bookings.booking_status,
        routes.source, routes.destination, schedules.departure_date
        FROM bookings
        INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
        INNER JOIN routes ON schedules.route_id = routes.route_id
        WHERE bookings.user_id = '$user_id'
        ORDER BY bookings.booking_date DESC";

$bookings = $conn->query($sql);


$escape = function ($value) {
    return htmlspecialchars($value);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/adminusersidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header">
            <div><h3><?php echo $escape($user['name']); ?></h3><p>Customer details and bookings.</p></div>
            <a class="btn btn-secondary" href="users.php">Back to Customers</a>
        </div>
        <section class="company-card mb-4">
            <p><strong>Email:</strong> <?php echo $escape($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo $escape($user['phone']); ?></p>
            <p><strong>Created:</strong> <?php echo $escape($user['created_at']); ?></p>
        </section>
        <section class="company-card">
            <h5>Bookings</h5>
            <div class="table-responsive">
                <table class="table company-table">
                    <thead><tr><th>#</th><th>Route</th><th>Departure</th><th>Booked</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if ($bookings->num_rows === 0) { ?>
                        <tr><td colspan="6" class="text-center">No bookings found.</td></tr>
                    <?php } ?>
                    <?php while ($booking = $bookings->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $escape($booking['booking_id']); ?></td>
                            <td><?php echo $escape($booking['source'] . ' → ' . $booking['destination']); ?></td>
                            <td><?php echo $escape($booking['departure_date']); ?></td>
                            <td><?php echo $escape($booking['booking_date']); ?></td>
                            <td>৳ <?php echo $escape(number_format((float)$booking['total_amount'], 2)); ?></td>
                            <td><?php echo $escape(ucfirst($booking['booking_status'])); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
