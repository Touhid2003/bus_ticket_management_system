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
$buses = $conn->query("SELECT bus_id, bus_name FROM buses ORDER BY bus_name");
$routes = $conn->query("SELECT route_id, source, destination, distance FROM routes ORDER BY source, destination");

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
                <button class="btn-add" id="openForm" type="button">Add Schedule</button>
            </div>

            <?php if (isset($_GET['success'])) { ?><div class="alert alert-success" role="status">Schedule changes saved.</div><?php } ?>
            <?php if (isset($_GET['error'])) { ?><div class="alert alert-danger" role="alert">Schedule action failed or it has bookings.</div><?php } ?>

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
                                           <?php echo $row['source'] . ' → ' . $row['destination']; ?>
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

                                         <?php echo number_format((float)$row['ticket_price'], 2) . ' TK'; ?>
                                    </td>
                                     <td>

                                         <?php echo $row['status']; ?>
                                    </td>






                                    <td>
                                        <a class="action-btn edit" href="schedule_edit.php?schedule_id=<?php echo $row['schedule_id']; ?>">
                                            Edit
                                        </a>

                                        <a href="schedule_delete.php?schedule_id=<?php echo $row['schedule_id']; ?>"
                                            class="action-btn delete"
                                            onclick="return confirm('Delete this schedule?')">
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
                        <h4>Add New Schedule</h4>
                        <p>Enter schedule information below.</p>
                    </div>

                    <button class="close-btn" id="closeForm">&times;</button>
                </div>

                <form id="companyForm" action="schedule_add.php" method="post">

                    <div class="mb-3">
                        <label>Bus</label>
                        <select name="bus_id" class="form-select" required>
                            <?php while ($bus = $buses->fetch_assoc()) { ?>
                                <option value="<?php echo $bus['bus_id']; ?>"><?php echo $bus['bus_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Route</label>
                        <select name="route_id" class="form-select" required>
                            <?php while ($route = $routes->fetch_assoc()) { ?>
                                <option value="<?php echo $route['route_id']; ?>">
                                    <?php echo $route['source'] . ' → ' . $route['destination'] . ' (' . $route['distance'] . ' km)'; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Departure Date</label>
                        <input type="date" class="form-control" name="departure_date" required>
                    </div>
                    <div class="mb-3">
                        <label>Departure Time</label>
                        <input type="time" class="form-control" name="departure_time" required>
                    </div>
                    <div class="mb-3">
                        <label>Arrival Time</label>
                        <input type="time" class="form-control" name="arrival_time" required>
                    </div>
                    <div class="mb-3">
                        <label>Ticket Price</label>
                        <input type="number" min="0" step="0.01" class="form-control" name="ticket_price" required>
                    </div>
                    <div class="mb-4">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-buttons">

                        <button type="button" class="btn-cancel" id="cancelForm">
                            Cancel
                        </button>

                        <button type="submit" class="btn-save">
                            Save Schedule
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