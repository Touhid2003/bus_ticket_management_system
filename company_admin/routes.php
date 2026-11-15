<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: index.php');
    exit;
}
$sql = "SELECT * FROM routes";

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
include '../includes/c_routes_bar.php';
?>
<div class="container-fluid py-4 px-4">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h3>Routes Management</h3>
                    <p>Manage all routes from here.</p>
                </div>
                
            </div>

            <div id="companyMessage" class="alert d-none" role="status" aria-live="polite"></div>

            <!-- Search and Table -->
            <div class="company-card">

                <div class="table-header">
                    <h5>All Routes</h5>

                    <input type="text" id="searchInput"
                        class="form-control search-box"
                        placeholder="Search company...">
                </div>

                <div class="table-responsive">
                    <table class="table company-table" id="companyTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Routes Name</th>
                                <th>Source</th>
                                <th>Destination</th>
                                <th>Distance</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php  
                            while($row = $result->fetch_assoc()){ ?>
                        
                           

                                <tr>
                                    <td>
                                      <?php echo $row['route_id'];?>
                                    </td>

                                    <td>
                                        <div class="company-name">
                                            <?php echo $row['source']. "→". $row['destination']; ?>
                                           
                                        </div>
                                    </td>
                                <td>
                                        <?php echo $row['source']; ?>
                                    </td>

                                    <td>
                                        <?php echo $row['destination']; ?>
                                    </td>
                                    <td>

                                       <?php echo $row['distance']; ?>

                                    </td>
                                        

                                    <td>

                                       <?php echo $row['status']; ?>

                                    </td>


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