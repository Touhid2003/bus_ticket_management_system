<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

    include '../function/admin.php';

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
include '../includes/adminbar.php';
?>





        <!-- ========== MAIN ========== -->
      <div class="container-fluid py-4 px-4">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h3>Admin Management</h3>
            <p>Manage all system administrators and their access permissions from here.</p>
        </div>

        <button class="btn-add" id="openForm">
            <i class="bi bi-plus-lg"></i> Add Company
        </button>
    </div>

    <div id="companyMessage" class="alert d-none" role="status" aria-live="polite"></div>

    <!-- Search and Table -->
    <div class="company-card">

        <div class="table-header">
            <h5>All Admin</h5>

            <input type="text" id="searchInput"
                   class="form-control search-box"
                   placeholder="Search company...">
        </div>

        <div class="table-responsive">
            <table class="table company-table" id="companyTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Admin Role</th>
                        <th>Company</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    while($row = $result->fetch_assoc() ){

                    
                    ?>

            <tr>
    
        <td><?php echo $row['user_id']; ?></td>
    

    <td>
        <div class="company-name">
        <?php echo $row['name']; ?>
        </div>
    </td>

    <td>
        <?php echo $row['email']; ?>
    </td>

    <td>
        <?php echo $row['role']; ?>
    </td>

    <td>
         <?php echo $row['company_name']?? 'N/A'; ?>
    </td>
    <td>
         <?php echo $row['created_at']; ?>
   
</td>
    
<td>
    <?php echo $row['company_status']?? 'N/A'; ?>
    </td>
   

    <td>
       <a class="action-btn edit">
    Edit
</a>
       
        <a
    class="action-btn delete">
    Delete
       </a>

    </td>
</tr>


<?php 
};

?>
</tbody>
            </table>
        </div>

    </div>
</div>

<div class="form-overlay" id="formOverlay">

    <div class="form-popup">

        <div class="form-heading">
            <div>
                <h4>Add New Admin</h4>
                <p>Enter admin information below.</p>
            </div>

            <button class="close-btn" id="closeForm">&times;</button>
        </div>

        <form id="companyForm" action="admin_add.php" method="post">

            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" class="form-control"
                       name="name" placeholder="Enter Full Name" required>
            </div>

            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" class="form-control"
                       name="email" placeholder="Enter Email Address" required>
            </div>

            <div class="mb-3">
                <label>Phone Number</label>
                <input type="text" class="form-control"
                       name="phone" placeholder="Enter Phone Number" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" class="form-control"
                       name="password" placeholder="Enter Password" required>
            </div>

            <div class="mb-4">
                <label>Role</label>
                <select class="form-select" name="role" required>
                    <option value="company_admin">Company Admin</option>
                </select>
            </div>

            <div class="mb-4">
                <label>Company</label>

                <select class="form-select" name="company_id" required>
                    <option value="">Select Company</option>

                    <?php
                    $companies = $conn->query("SELECT * FROM bus_companies");

                    while ($company = $companies->fetch_assoc()) {
                    ?>
                        <option value="<?php echo $company['company_id']; ?>">
                            <?php echo htmlspecialchars($company['company_name']); ?>
                        </option>
                    <?php } ?>

                </select>
            </div>

            <div class="form-buttons">

                <button type="button" class="btn-cancel" id="cancelForm">
                    Cancel
                </button>

                <button type="submit" class="btn-save">
                    Save Admin
                </button>

            </div>

        </form>

    </div>
</div>

<?php
include '../includes/admin_script.php';
?>

</body>

</html>