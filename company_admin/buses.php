<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: index.php');
    exit;
}

$company_id = $_SESSION['company_id'];

// Get Company Buses
$sql = "SELECT * FROM buses
        WHERE company_id = '$company_id'
        ORDER BY bus_id";

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
                                      <?php echo (int)$row['bus_id'];?>
                                    </td>

                                    <td>
                                        <div class="company-name">
                                            <?php echo htmlspecialchars($row['bus_name'], ENT_QUOTES, 'UTF-8'); ?>
                                           
                                        </div>
                                    </td>
                                <td>
                                        <?php echo htmlspecialchars($row['bus_type'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo (int)$row['total_seats']; ?>
                                    </td>
                                    <td>

                                       <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>

                                    </td>
                                        

                                    <td>

                                       <?php echo htmlspecialchars($row['bus_number'], ENT_QUOTES, 'UTF-8'); ?>

                                    </td>

                                    





                                    <td>
                                        <a class="action-btn edit" href="bus_edit.php?bus_id=<?php echo (int)$row['bus_id']; ?>">
                                            Edit
                                        </a>

                                        <a class="action-btn delete"
                                           href="bus_delete.php?bus_id=<?php echo (int)$row['bus_id']; ?>"
                                           onclick="return confirm('Delete this bus?')">
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
                            <option value="AC">AC</option>
                            <option value="Non-AC">Non-AC</option>
                            <option value="Sleeper">Sleeper</option>
                            <option value="Business Class">Business Class</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>	Total Seats</label>
                        <input type="number" min="1" class="form-control"
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
                            Save Bus
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