    <?php
    session_start();

    include '../config/db.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
        header('Location: ../login.php');
        exit;
    }

    $company_id = $_SESSION['company_id'];


$sql = "SELECT
        schedules.schedule_id,
        buses.company_id,
        buses.bus_name,
        buses.bus_number,
        buses.bus_type,
        routes.source,
        routes.destination,
        schedules.departure_date,
        schedules.departure_time,
        schedules.arrival_time,
        schedules.ticket_price,
        schedules.status
        FROM schedules
        INNER JOIN buses ON schedules.bus_id = buses.bus_id
        INNER JOIN routes ON schedules.route_id = routes.route_id
        WHERE buses.company_id = '$company_id'
        ORDER BY schedules.departure_date ASC";

$raw = $conn->query($sql);

// Get Active Company Buses
$sql = "SELECT bus_id, bus_name, bus_number, bus_type
        FROM buses
        WHERE company_id = '$company_id'
        AND status = 'active'";

$rew = $conn->query($sql);

// Get Active Routes
$sql = "SELECT route_id, source, destination, distance
        FROM routes
        WHERE status = 'active'";

$routeResult = $conn->query($sql);
    ?>
    <!DOCTYPE html>
    <html lang="en">

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
            include '../includes/c_schedules_bar.php';
            ?>





            <!-- ========== MAIN ========== -->
            <div class="container-fluid py-4 px-4">

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h3>Schedule Management</h3>
                        <p>Manage all schedules from here.</p>
                    </div>
                    <button class="btn-add" id="openForm">
                        <i class="bi bi-plus-lg"></i> Add Schedule
                    </button>
                </div>

                <?php if (isset($_GET['success'])) { ?><div class="alert alert-success" role="status">Schedule changes saved.</div><?php } ?>
                <?php if (isset($_GET['error'])) { ?><div class="alert alert-danger" role="alert">Schedule action failed or the schedule has bookings.</div><?php } ?>

                <div id="companyMessage" class="alert d-none" role="status" aria-live="polite"></div>

                <!-- Search and Table -->
                <div class="company-card">

                    <div class="table-header">
                        <h5>All Schedules</h5>

                        <input type="text" id="searchInput"
                            class="form-control search-box"
                            placeholder="Search schedules...">
                    </div>

                    <div class="table-responsive">
                        <table class="table company-table" id="companyTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Route</th>
                                    <th>Bus Name</th>
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
                                if ($raw->num_rows > 0) {
                                    while ($row = $raw->fetch_assoc()) { ?>

                                        <tr>
                                            <td>
                                                <?php echo (int)$row['schedule_id']; ?>
                                            </td>


                                            <td>
                                                <div class="company-name">
                                                    <?php echo htmlspecialchars($row['source'] . ' → ' . $row['destination'], ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($row['bus_name'] . " - " .
                                                    $row['bus_number'] . " (" .
                                                    $row['bus_type'] . ")", ENT_QUOTES, 'UTF-8');

                                                ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($row['departure_date'], ENT_QUOTES, 'UTF-8'); ?>

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
                                                <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td>
                                                <a class="action-btn edit" href="schedule_edit.php?schedule_id=<?php echo (int)$row['schedule_id']; ?>">
                                                    Edit
                                                </a>

                                                <a class="action-btn delete"
                                                   href="schedule_delete.php?schedule_id=<?php echo (int)$row['schedule_id']; ?>"
                                                   onclick="return confirm('Delete this schedule?')">
                                                    Delete
                                                </a>

                                            </td>
                                        </tr>


                                <?php

                                    }
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
                            <h4>Add New Schedule</h4>
                            <p>Enter schedule information below.</p>
                        </div>

                        <button class="close-btn" id="closeForm">&times;</button>
                    </div>

                    <form id="companyForm" action="../function/c_schedules_add.php" method="post">

                        <div class="mb-3">
                            <label class="form-label">Select Bus</label>
                            <select name="bus_id" class="form-select" required>
                                <option value="">Select Bus</option>
                                <?php while ($row = $rew->fetch_assoc()) { ?>
                                    <option value="<?php echo (int)$row['bus_id']; ?>">
                                        <?php echo htmlspecialchars($row['bus_name'] . ' - ' . $row['bus_number'] . ' (' . $row['bus_type'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Route</label>
                            <select name="route_id" class="form-select" required>
                                <option value="">Select Route</option>
                                <?php while ($row = $routeResult->fetch_assoc()) { ?>
                                    <option value="<?php echo (int)$row['route_id']; ?>">
                                        <?php echo htmlspecialchars($row['source'] . ' → ' . $row['destination'] . ' (' . $row['distance'] . ' KM)', ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Departure Date</label>
                            <input type="date" class="form-control"
                                name="departure_date"  required>
                        </div>

                        <div class="mb-3">
                            <label>Departure Time</label>
                            <input type="time" class="form-control"
                                name="departure_time" required>
                        </div>

                        <div class="mb-3">
                            <label>Arrival Time</label>
                            <input type="time" class="form-control"
                                name="arrival_time" required>
                        </div>

                        <div class="mb-3">
                            <label>Ticket Price</label>
                            <input type="number" class="form-control"
                                name="ticket_price" min="0" step="0.01" placeholder="Ticket Price" required>
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