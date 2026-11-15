<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: index.php');
    exit;
}
$sql = "SELECT * FROM buses WHERE company_id = 1";

$result = $conn->query($sql);


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
include '../includes/c_buses_bar.php';
?>
<div class="container-fluid py-4 px-4">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h3>Bus Management</h3>
                    <p>Manage all bus from here.</p>
                </div>
                <button class="btn-add" id="openForm">
                    <i class="bi bi-plus-lg"></i> Add bus
                </button>
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
                                <th>Bus Name</th>
                                <th>Bus Type</th>
                                <th>Total Seats</th>
                                <th>Status</th>
                                <th>Bus Number</th>
                                <th>Action </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php  
                            while($row = $result->fetch_assoc()){ ?>
                        
                           

                                <tr>
                                    <td>
                                      <?php echo $row['bus_id'];?>
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
                                        <a class="action-btn edit">
                                            Edit
                                        </a>

                                        <a
                                            class="action-btn delete">
                                            Delete
                                        </a>

                                    </td>
                                </tr>

        <?php   }; ?>
                           
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="form-overlay" id="formOverlay">

            <div class="form-popup">

                <div class="form-heading">
                    <div>
                        <h4>Add New Buses</h4>
                        <p>Enter Buses information below.</p>
                    </div>

                    <button class="close-btn" id="closeForm">&times;</button>
                </div>

                <form id="companyForm" action="../function/c_buses_add.php" method="post">

                    <div class="mb-3">
                        <label>Bus Name</label>
                        <input type="text" class="form-control"
                            name="bus_name" placeholder="Bus Name" required>
                    </div>

                    <div class="mb-4">
                        <label>Bus Type</label>
                        <select name="bus_type" class="form-select" required>
                            <option value="AC">Non-AC</option>
                            <option value="Non-AC">Non-AC</option>
                            <option value="Sleeper">Sleeper</option>
                            <option value="Business Class">Business Class</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>	Total Seats</label>
                        <input type="text" class="form-control"
                            name="total_seats" placeholder="Total Seats" required>
                    </div>


                    <div class="mb-4">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                     <div class="mb-3">
                        <label>	Bus Number</label>
                        <input type="text" class="form-control"
                            name="bus_number" placeholder="Bus Number" required>
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


      

    </div>
           

<?php
include '../includes/admin_script.php';
?>
</body>

</html>