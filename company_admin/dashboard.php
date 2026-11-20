<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: index.php');
    exit;
}



$company_id = $_SESSION['company_id'];

// Company Name
$result = $conn->query("SELECT company_name FROM bus_companies WHERE company_id = '$company_id'");
$company_name = $result->fetch_assoc()['company_name'] ?? '';

// Total Buses
$result = $conn->query("SELECT COUNT(*) AS total FROM buses WHERE company_id = '$company_id'");
$totalBuses = $result->fetch_assoc()['total'];

// Total Users
$result = $conn->query("
    SELECT COUNT(DISTINCT bookings.user_id) AS total
    FROM bookings
    INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
    INNER JOIN buses ON schedules.bus_id = buses.bus_id
    WHERE buses.company_id = '$company_id'
");
$totalUsers = $result->fetch_assoc()['total'];

// Total Bookings
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM bookings
    INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
    INNER JOIN buses ON schedules.bus_id = buses.bus_id
    WHERE buses.company_id = '$company_id'
");
$totalBookings = $result->fetch_assoc()['total'];

// Total Revenue
$result = $conn->query("
    SELECT COALESCE(SUM(payments.amount), 0) AS total
    FROM payments
    INNER JOIN bookings ON payments.booking_id = bookings.booking_id
    INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
    INNER JOIN buses ON schedules.bus_id = buses.bus_id
    WHERE buses.company_id = '$company_id'
    AND payments.payment_status = 'paid'
");
$totalRevenue = $result->fetch_assoc()['total'];

// Recent Payments
$result = $conn->query("
    SELECT payments.payment_id, payments.booking_id, users.name,
           payments.payment_method, payments.amount,
           payments.payment_status, payments.payment_date
    FROM payments
    INNER JOIN bookings ON payments.booking_id = bookings.booking_id
    INNER JOIN users ON bookings.user_id = users.user_id
    INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
    INNER JOIN buses ON schedules.bus_id = buses.bus_id
    WHERE buses.company_id = '$company_id'
    ORDER BY payments.payment_date DESC
    LIMIT 5
");
$recentPayments = $result;

?>



<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | BusGo</title>
   <?php
include '../includes/admin_css.php';
?>
</head>

<body>

<?php
include '../includes/topbar.php';
?>


    <div class="layout">

   <?php
include '../includes/cmsidebar.php';
?>



        <!-- ========== MAIN ========== -->
         <main class="content">
  <div class="header-card">
    <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>! 👋</h2>
      <p>Here's your dashboard overview. </p>
  </div>

    <!-- Statistics Cards -->
     <section class="row g-3 mb-4">
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-primary-subtle text-primary"><i class="bi bi-building"></i></div>
          <div><div class="text-secondary small fw-medium">Company</div><div class="fs-3 fw-bold" id="statCompanies">
            <?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?>
          </div><div class="text-secondary small">Your company profile</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-purple-subtle"><i class="bi bi-bus-front"></i></div>
          <div><div class="text-secondary small fw-medium">Total Buses</div><div class="fs-3 fw-bold" id="statBuses"><?php echo (int)$totalBuses; ?></div><div class="text-secondary small">Your company buses</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-info-subtle text-info-emphasis"><i class="bi bi-people"></i></div>
          <div><div class="text-secondary small fw-medium">Total Users</div><div class="fs-3 fw-bold" id="statUsers">
            <?php echo (int)$totalUsers; ?>
          </div><div class="text-secondary small">Customers with bookings</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-warning-subtle text-warning-emphasis"><i class="bi bi-ticket-perforated"></i></div>
          <div><div class="text-secondary small fw-medium">Total Bookings</div><div class="fs-3 fw-bold" id="statBookings"><?php echo (int)$totalBookings; ?></div><div class="text-secondary small">Bookings for your buses</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-success-subtle text-success-emphasis"><i class="bi bi-cash-stack"></i></div>
          <div><div class="text-secondary small fw-medium">Paid Revenue</div><div class="fs-3 fw-bold" id="statRevenue">৳ <?php echo number_format((float)$totalRevenue, 2); ?></div><div class="text-secondary small">Payments marked paid</div></div>
        </div></div>
      </div>
    </section>

    <section class="company-card mb-4">
      <div class="table-header">
        <h5>Recent Payments</h5>
        <a class="action-btn view" href="payments.php">View all</a>
      </div>
      <div class="table-responsive">
        <table class="table company-table">
          <thead>
            <tr><th>#</th><th>Booking</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Paid At</th></tr>
          </thead>
          <tbody>
            <?php if ($recentPayments->num_rows > 0) { ?>
              <?php while ($payment = $recentPayments->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo (int)$payment['payment_id']; ?></td>
                  <td><?php echo (int)$payment['booking_id']; ?></td>
                  <td><?php echo htmlspecialchars($payment['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($payment['payment_method'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo number_format((float)$payment['amount'], 2); ?> TK</td>
                  <td><?php echo htmlspecialchars($payment['payment_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($payment['payment_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr><td colspan="7" class="text-center">No payments yet.</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
      

            <!-- Statistics Cards -->
           

<?php
include '../includes/admin_script.php';
?>
</body>

</html>