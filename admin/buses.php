<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$sql = "SELECT buses.*, bus_companies.company_name
        FROM buses
        LEFT JOIN bus_companies ON buses.company_id = bus_companies.company_id
        ORDER BY buses.bus_id";
$raw = $conn->query($sql);
$companies = $conn->query("SELECT company_id, company_name FROM bus_companies ORDER BY company_name");

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
        <button class="btn-add" id="openForm" type="button">Add Bus</button>
    </div>

    <?php if (isset($_GET['success'])) { ?><div class="alert alert-success" role="status">Bus changes saved.</div><?php } ?>
    <?php if (isset($_GET['error'])) { ?><div class="alert alert-danger" role="alert">Bus action failed or the bus is in use.</div><?php } ?>

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
                        <th>Company</th>
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
        <?php echo htmlspecialchars($row['company_name'] ?? $row['company_id'], ENT_QUOTES, 'UTF-8'); ?>
    </td>
    

    <td>
        <div class="company-name">
       <?php echo $row['bus_name']; ?>
        </div>
    </td>

    <td>
        <?php echo $row['bus_type']; ?>
    </td>

    <td>
        <?php echo $row['total_seats']; ?>
    </td>

    <td>
         <?php echo $row['status']; ?>
    </td>
    <td>
         <?php echo $row['bus_number']; ?>
   
</td>
    

   

    <td>
       <a class="action-btn edit" href="bus_edit.php?bus_id=<?php echo $row['bus_id']; ?>">
    Edit
</a>
       
        <a href="bus_delete.php?bus_id=<?php echo $row['bus_id']; ?>"
    class="action-btn delete"
    onclick="return confirm('Delete this bus?')">
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
                <h4>Add New Bus</h4>
                <p>Enter bus information below.</p>
            </div>

            <button class="close-btn" id="closeForm">&times;</button>
        </div>

        <form id="companyForm" action="bus_add.php" method="post">

            <div class="mb-3">
                <label>Company</label>
                <select class="form-select" name="company_id" required>
                    <option value="">Select Company</option>
                    <?php while ($company = $companies->fetch_assoc()) { ?>
                        <option value="<?php echo $company['company_id']; ?>">
                            <?php echo $company['company_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Bus Name</label>
                <input type="text" class="form-control" name="bus_name" required>
            </div>
            <div class="mb-3">
                <label>Bus Type</label>
                <select class="form-select" name="bus_type" required>
                    <option value="AC">AC</option>
                    <option value="Non-AC">Non-AC</option>
                    <option value="Sleeper">Sleeper</option>
                    <option value="Business Class">Business Class</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Total Seats</label>
                <input type="number" min="1" class="form-control" name="total_seats" required>
            </div>
            <div class="mb-3">
                <label>Bus Number</label>
                <input type="text" class="form-control" name="bus_number" required>
            </div>
            <div class="mb-4">
                <label>Status</label>
                <select class="form-select" name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="form-buttons">

                <button type="button" class="btn-cancel" id="cancelForm">
                    Cancel
                </button>

                <button type="submit" class="btn-save">
                    Save Bus
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