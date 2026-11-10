<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}
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
    <h2>Welcome back, <?php echo ($_SESSION['name']);?>! 👋</h2>
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

          ?></div><div class="text-success small fw-semibold">+2 this month</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-purple-subtle"><i class="bi bi-bus-front"></i></div>
          <div><div class="text-secondary small fw-medium">Total Buses</div><div class="fs-3 fw-bold" id="statBuses">186</div><div class="text-success small fw-semibold">+11 this month</div></div>
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

          </div><div class="text-success small fw-semibold">+4.2% vs last month</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-warning-subtle text-warning-emphasis"><i class="bi bi-ticket-perforated"></i></div>
          <div><div class="text-secondary small fw-medium">Total Bookings</div><div class="fs-3 fw-bold" id="statBookings">9,342</div><div class="text-success small fw-semibold">+8.1% vs last month</div></div>
        </div></div>
      </div>
      <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100"><div class="card-body d-flex gap-3">
          <div class="icon-box bg-success-subtle text-success-emphasis"><i class="bi bi-cash-stack"></i></div>
          <div><div class="text-secondary small fw-medium">Total Revenue</div><div class="fs-3 fw-bold" id="statRevenue">৳ 58.7 L</div><div class="text-success small fw-semibold">+12.4% vs last month</div></div>
        </div></div>
      </div>
    </section>

    <!-- Charts -->
    
  </main>

<?php
include '../includes/admin_script.php';
?>
</body>

</html>