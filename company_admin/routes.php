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
                                      <?php echo (int)$row['route_id'];?>
                                    </td>

                                    <td>
                                        <div class="company-name">
                                            <?php echo htmlspecialchars($row['source'] . ' → ' . $row['destination'], ENT_QUOTES, 'UTF-8'); ?>
                                           
                                        </div>
                                    </td>
                                <td>
                                        <?php echo htmlspecialchars($row['source'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['destination'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td>

                                       <?php echo number_format((float)$row['distance'], 2); ?>

                                    </td>
                                        

                                    <td>

                                       <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>

                                    </td>


        <?php   }; ?>
                           
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
           

<?php
include '../includes/admin_script.php';
?>
</body>

</html>