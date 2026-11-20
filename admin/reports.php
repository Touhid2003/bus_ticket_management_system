<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}
// Booking Statistics
$bookingStats = $conn->query("SELECT booking_status, COUNT(*) AS total FROM bookings GROUP BY booking_status");

// Payment Statistics
$paymentStats = $conn->query("SELECT payment_status, COUNT(*) AS total, SUM(amount) AS amount FROM payments GROUP BY payment_status");

// Total Bookings
$result = $conn->query("SELECT COUNT(*) AS total FROM bookings");
$totalBookings = $result->fetch_assoc()['total'];

// Paid Revenue
$result = $conn->query("SELECT SUM(amount) AS total FROM payments WHERE payment_status = 'paid'");
$paidRevenue = $result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header"><div><h3>Reports</h3><p>Booking and payment summary.</p></div></div>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><div class="company-card p-4"><h5>Total Bookings</h5><p class="fs-3 mb-0"><?php echo (int)$totalBookings; ?></p></div></div>
            <div class="col-md-6"><div class="company-card p-4"><h5>Paid Revenue</h5><p class="fs-3 mb-0">৳ <?php echo number_format((float)$paidRevenue, 2); ?></p></div></div>
        </div>
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="company-card p-4">
                    <h5>Bookings by Status</h5>
                    <table class="table company-table"><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody>
                    <?php while ($item = $bookingStats->fetch_assoc()) { ?>
                        <tr><td><?php echo $item['booking_status']; ?></td><td><?php echo $item['total']; ?></td></tr>
                    <?php } ?>
                    </tbody></table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="company-card p-4">
                    <h5>Payments by Status</h5>
                    <table class="table company-table"><thead><tr><th>Status</th><th>Total</th><th>Amount</th></tr></thead><tbody>
                    <?php while ($item = $paymentStats->fetch_assoc()) { ?>
                        <tr><td><?php echo $item['payment_status']; ?></td><td><?php echo $item['total']; ?></td><td><?php echo number_format((float)$item['amount'], 2); ?> TK</td></tr>
                    <?php } ?>
                    </tbody></table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
