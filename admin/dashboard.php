<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

// Total Bookings
$result = $conn->query("SELECT COUNT(*) AS total FROM bookings");
$totalBookings = $result->fetch_assoc()['total'];

// Total Revenue
$result = $conn->query("SELECT SUM(amount) AS total FROM payments WHERE payment_status = 'paid'");
$totalRevenue = $result->fetch_assoc()['total'];

$recentBookings = $conn->query(
    "SELECT bookings.booking_id, users.name, buses.bus_name, routes.source,
            routes.destination, bookings.booking_date, bookings.total_amount,
            bookings.booking_status
     FROM bookings
     INNER JOIN users ON bookings.user_id = users.user_id
     INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
     INNER JOIN buses ON schedules.bus_id = buses.bus_id
     INNER JOIN routes ON schedules.route_id = routes.route_id
     ORDER BY bookings.booking_date DESC
     LIMIT 5"
);
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
include '../includes/sidebar.php';
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
          <div><div class="text-secondary small fw-medium">Total Companies</div><div class="fs-3 fw-bold" id="statCompanies">
            <?php 
           
          $totalCompanies = $conn->query("SELECT COUNT(*) AS total FROM bus_companies");
          $total = $totalCompanies->fetch_assoc();

          echo $total['total'];

          ?>          </div><div class="text-secondary small">All registered companies</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-purple-subtle"><i class="bi bi-bus-front"></i></div>
          <div><div class="text-secondary small fw-medium">Total Buses</div><div class="fs-3 fw-bold" id="statBuses">
               <?php 
           
          $totalCompanies = $conn->query("SELECT COUNT(*) AS total FROM buses");
          $total = $totalCompanies->fetch_assoc();

          echo $total['total'];

          ?>
          </div><div class="text-secondary small">All registered buses</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-info-subtle text-info-emphasis"><i class="bi bi-people"></i></div>
          <div><div class="text-secondary small fw-medium">Total Users</div><div class="fs-3 fw-bold" id="statUsers">
            <?php 
            $totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'customer' " );

            $total = $totalUsers->fetch_assoc();
            echo $total['total'];
            
            ?>

          </div><div class="text-secondary small">Customer accounts</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-warning-subtle text-warning-emphasis"><i class="bi bi-ticket-perforated"></i></div>
          <div><div class="text-secondary small fw-medium">Total Bookings</div><div class="fs-3 fw-bold" id="statBookings"><?php echo $totalBookings; ?></div><div class="text-secondary small">All recorded bookings</div></div>
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
        <h5>Recent Bookings</h5>
        <a class="action-btn view" href="bookings.php">View all</a>
      </div>
      <div class="table-responsive">
        <table class="table company-table">
          <thead>
            <tr><th>#</th><th>Customer</th><th>Bus</th><th>Route</th><th>Booked At</th><th>Amount</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php if ($recentBookings->num_rows > 0) { ?>
              <?php while ($booking = $recentBookings->fetch_assoc()) { ?>
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
            <?php } else { ?>
              <tr><td colspan="7" class="text-center">No bookings yet.</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </section>


    
  </main>

<?php
include '../includes/admin_script.php';
?>
</body>

</html>