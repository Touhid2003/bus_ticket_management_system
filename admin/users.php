<?php
session_start();
include_once '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin'){
    header('location: ../login.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
   include '../includes/adminusersidebar.php';
?>
 <div class="container-fluid py-4 px-4">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h3>Customers Management</h3>
            <p>View and manage all users from here.</p>
        </div>
    </div>

    <?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">Customer deleted.</div>';
}

if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">Customer action failed.</div>';
}
?>

    <div id="companyMessage" class="alert d-none" role="status" aria-live="polite"></div>

    <!-- Search and Table -->
    <div class="company-card">

        <div class="table-header">
            <h3>All Customers</h3>

            <input type="text" id="searchInput"
                   class="form-control search-box"
                   placeholder="Search Customer...">
        </div>

        <div class="table-responsive">
            <table class="table company-table" id="companyTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
        
                

<?php
include '../function/users.php';

while ($row = $result->fetch_assoc()) {
     ?>

<tr>
    
        <td><?php echo (int)$row['user_id']; ?></td>
    

    <td>
        <div class="company-name">
            <?php echo $row['name']; ?>
        </div>
    </td>

    <td><?php echo $row['email']; ?></td>

    <td><?php echo $row['phone']; ?></td>

    <td>
    <?php echo $row['created_at']; ?>
</td>
    

    <td>
       
       <a
    class="action-btn delete"
    onclick="return confirm('Are you sure to delete this customer?')"
    href="users_delete.php?user_id=<?php echo $row['user_id']; ?>">
    Delete
</a>

  <a class="action-btn view" href="users_view.php?user_id=<?php echo $row['user_id']; ?>">
    view
</a>

    </td>
</tr>

<?php } ?>

</tbody>
            </table>
        </div>

    </div>
</div>

<?php
include '../includes/admin_script.php';
?>
    
</body>
</html>