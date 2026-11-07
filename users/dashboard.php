<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header('Location: index.php');
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
include '../includes/usersidebar.php';
?>



        <!-- ========== MAIN ========== -->
         <main class="content">
  <div class="header-card">
    <h2>Welcome to JR, <?php echo ($_SESSION['name']);?>! 👋</h2>
      <p>Find buses, book tickets and enjoy a comfortable journey. </p>
  </div>

    <!-- Statistics Cards -->
    

    <!-- Charts -->
    
  </main>

<?php
include '../includes/admin_script.php';
?>
</body>

</html>