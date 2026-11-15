<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

 $sql = "SELECT * FROM buses";

 $raw = $conn->query($sql);

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
include '../includes/buses.php';
?>





        <!-- ========== MAIN ========== -->
      <div class="container-fluid py-4 px-4">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h3>Buses Management</h3>
            <p>Manage all buses from here.</p>
        </div>
    </div>

    <div id="companyMessage" class="alert d-none" role="status" aria-live="polite"></div>

    <!-- Search and Table -->
    <div class="company-card">

        <div class="table-header">
            <h5>All Buses</h5>

            <input type="text" id="searchInput"
                   class="form-control search-box"
                   placeholder="Search company...">
        </div>

        <div class="table-responsive">
            <table class="table company-table" id="companyTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Id</th>
                        <th>Bus Name</th>
                        <th>Bus Type</th>
                        <th>Total Seats</th>
                        <th>Status</th>
                        <th>Bus Number </th>
                        <th>Action </th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    while($row = $raw->fetch_assoc()){ ?>

            <tr>
    
        <td> <?php echo $row['bus_id'] ?></td>
         <td>
        <?php echo $row['company_id'];  ?>
    </td>
    

    <td>
        <div class="company-name">
       <?php  echo $row['bus_name']; ?>
        </div>
    </td>

    <td>
        <?php echo $row['bus_type'];  ?>
    </td>

    <td>
        <?php echo $row['total_seats']; ?>
    </td>

    <td>
         <?php echo $row['status']; ?>
    </td>
    <td>
         <?php echo $row['bus_number'];  ?>
   
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