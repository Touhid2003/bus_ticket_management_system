<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$sql = "SELECT 
        schedules.schedule_id,
        buses.bus_name,
        routes.source,
        routes.destination,
        schedules.departure_date,
        schedules.departure_time,
        schedules.arrival_time,
        schedules.ticket_price,
        schedules.status

        FROM  schedules INNER JOIN buses
        ON schedules.bus_id = buses.bus_id
        INNER JOIN routes
        ON schedules.route_id = routes.route_id
        ORDER BY schedules.departure_date ASC";

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
        include '../includes/schedules_bar.php';
        ?>





        <!-- ========== MAIN ========== -->
        <div class="container-fluid py-4 px-4">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h3>Schedules Management</h3>
                    <p>Manage all schedules from here.</p>
                </div>
                
            </div>

            <div id="companyMessage" class="alert d-none" role="status" aria-live="polite"></div>

            <!-- Search and Table -->
            <div class="company-card">

                <div class="table-header">
                    <h5>All Schedules</h5>

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
                                <th>Routes Name</th>
                                <th>Departure Date</th>
                                <th>Departure Time</th>
                                <th>Arrival Time</th>
                                <th>Ticket Price</th>
                                <th>Status</th>
                                <th>Action </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            if($raw->num_rows>0){
                             while ($row = $raw->fetch_assoc()) { ?>

                                <tr>
                                    <td>
                                         <?php echo $row['schedule_id']; ?>
                                    </td>
                                    <td>
                                         <?php echo $row['bus_name']; ?>
                                    </td>

                                    <td>
                                        <div class="company-name">
                                           <?php echo $row['source']. "→" .$row['destination']; ?>
                                        </div>
                                    </td>
                                    <td>
                                          <?php echo $row['departure_date']; ?>
                                       
                                    </td>
                                    <td>
                                <?php echo date('h:i A', strtotime($row['departure_time'])); ?>
                                        

                                    </td>


                                    <td>
                                        <?php echo date('h:i A', strtotime($row['arrival_time'])); ?>
                                       

                                    </td>

                                    <td>

                                         <?php echo $row['ticket_price']. " TK"; ?>
                                    </td>
                                     <td>

                                         <?php echo $row['status']; ?>
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

                             } };
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

                <form id="companyForm" action="../function/route_add.php" method="post">

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