<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$sql = "SELECT * FROM  routes";

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
        include '../includes/route_bar.php';
        ?>





        <!-- ========== MAIN ========== -->
        <div class="container-fluid py-4 px-4">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h3>Routes Management</h3>
                    <p>Manage all Routes from here.</p>
                </div>
                <button class="btn-add" id="openForm">
                    <i class="bi bi-plus-lg"></i> Add Routes
                </button>
            </div>

            <?php if (isset($_GET['success'])) { ?><div class="alert alert-success" role="status">Route changes saved.</div><?php } ?>
            <?php if (isset($_GET['error'])) { ?><div class="alert alert-danger" role="alert">Route action failed or the route is in use.</div><?php } ?>

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
                                <th>Route Name</th>
                                <th>Source</th>
                                <th>Destination</th>
                                <th>Distance</th>
                                <th>Status</th>
                                <th>Action </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            while ($row = $raw->fetch_assoc()) { ?>

                                <tr>
                                    <td>
                                        <?php echo (int)$row['route_id'];  ?>
                                    </td>

                                    <td>
                                        <div class="company-name">
                                            <?php echo$row['source'] . ' → ' . $row['destination']; ?>
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





                                    <td>
                                        <a class="action-btn edit" href="route_edit.php?route_id=<?php echo $row['route_id']; ?>">
                                            Edit
                                        </a>

                                        <a href="route_delete.php?route_id=<?php echo $row['route_id']; ?>"
                                            class="action-btn delete"
                                            onclick="return confirm('Delete this route?')">
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
                        <h4>Add New Routes</h4>
                        <p>Enter Routes information below.</p>
                    </div>

                    <button class="close-btn" id="closeForm">&times;</button>
                </div>

                <form id="companyForm" action="route_add.php" method="post">

                    <div class="mb-3">
                        <label>Source</label>
                        <input type="text" class="form-control"
                            name="Source" placeholder=" Source" required>
                    </div>

                    <div class="mb-3">
                        <label>Destination</label>
                        <input type="text" class="form-control"
                            name="Destination" placeholder="Destination" required>
                    </div>

                    <div class="mb-3">
                        <label>Distance</label>
                        <input type="text" class="form-control"
                            name="Distance" placeholder="Distance" required>
                    </div>



                    <div class="mb-4">
                        <label>Status</label>
                        <select name="Status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-buttons">

                        <button type="button" class="btn-cancel" id="cancelForm">
                            Cancel
                        </button>

                        <button type="submit" class="btn-save">
                            Save Route
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