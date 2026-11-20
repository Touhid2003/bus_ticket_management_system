<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$sql = "SELECT payments.payment_id, payments.booking_id, users.name,
        payments.payment_method, payments.transaction_id, payments.amount,
        payments.payment_status, payments.payment_date
        FROM payments
        INNER JOIN bookings ON payments.booking_id = bookings.booking_id
        INNER JOIN users ON bookings.user_id = users.user_id
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
    <?php include '../includes/sidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header"><div><h3>Payments</h3><p>View payment transactions.</p></div></div>
        <div class="company-card">
            <div class="table-responsive">
                <table class="table company-table">
                    <thead><tr><th>#</th><th>Booking</th><th>Customer</th><th>Method</th><th>Transaction</th><th>Amount</th><th>Status</th><th>Paid At</th></tr></thead>
                    <tbody>
                    <?php while ($payment = $payments->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $payment['payment_id']; ?></td>
                            <td><?php echo $payment['booking_id']; ?></td>
                            <td><?php echo $payment['name']; ?></td>
                            <td><?php echo $payment['payment_method']; ?></td>
                            <td><?php echo $payment['transaction_id']; ?></td>
                            <td><?php echo number_format((float)$payment['amount'], 2); ?> TK</td>
                            <td><?php echo $payment['payment_status']; ?></td>
                            <td><?php echo $payment['payment_date']; ?></td>
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
