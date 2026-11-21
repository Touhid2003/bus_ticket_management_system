<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];

// Get Company Payments
$sql = "SELECT payments.payment_id, payments.booking_id,
        users.name, payments.payment_method,
        payments.transaction_id, payments.amount,
        payments.payment_status, payments.payment_date
        FROM payments
        INNER JOIN bookings ON payments.booking_id = bookings.booking_id
        INNER JOIN users ON bookings.user_id = users.user_id
        INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
        INNER JOIN buses ON schedules.bus_id = buses.bus_id
        WHERE buses.company_id = '$company_id'
        ORDER BY payments.payment_date DESC";

$payments = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payments | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/cmsidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header">
            <div><h3>Payments</h3><p>Payments for your company bookings.</p></div>
            <a class="action-btn view" href="reports.php"><i class="bi bi-printer"></i> Income Report</a>
        </div>
        <div class="company-card">
            <div class="table-header">
                <h5>All Payments</h5>
                <input type="text" id="searchInput" class="form-control search-box" placeholder="Search payments...">
            </div>
            <div class="table-responsive">
                <table class="table company-table" id="companyTable">
                    <thead>
                        <tr><th>#</th><th>Booking</th><th>Customer</th><th>Method</th><th>Transaction</th><th>Amount</th><th>Status</th><th>Paid At</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo (int)$payment['payment_id']; ?></td>
                                <td><?php echo (int)$payment['booking_id']; ?></td>
                                <td><?php echo ($payment['name']); ?></td>
                                <td><?php echo ($payment['payment_method']); ?></td>
                                <td><?php echo ($payment['transaction_id']); ?></td>
                                <td><?php echo number_format((float)$payment['amount'], 2); ?> TK</td>
                                <td><?php echo ($payment['payment_status']); ?></td>
                                <td><?php echo ($payment['payment_date']); ?></td>
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
