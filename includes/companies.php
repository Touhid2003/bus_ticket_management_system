<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: login.php');
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
            <h1 class="h3 fw-bold mb-1">Companies</h1>
            <p class="text-secondary mb-4">Companies, fleet, bookings ও revenue-এর সারসংক্ষেপ।</p>

            <!-- Statistics Cards -->
           

            <!-- Charts -->
           

            <!-- Recent Bookings -->
            
        </main>
    </div>

<?php
include '../includes/admin_script.php';
?>
</body>

</html>