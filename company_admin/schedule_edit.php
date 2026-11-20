<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$schedule_id = $_GET['schedule_id'];

// Get Schedule Information
$sql = "SELECT schedules.*
        FROM schedules
        INNER JOIN buses ON schedules.bus_id = buses.bus_id
        WHERE schedules.schedule_id = '$schedule_id'
        AND buses.company_id = '$company_id'";

$result = $conn->query($sql);
$schedule = $result->fetch_assoc();

if (!$schedule) {
    header('Location: schedules.php?error=schedule_not_found');
    exit;
}

// Update Schedule
$updateError = false;

if (isset($_POST['update'])) {

    $bus_id = $_POST['bus_id'];
    $route_id = $_POST['route_id'];
    $departure_date = $_POST['departure_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $ticket_price = $_POST['ticket_price'];
    $status = $_POST['status'];

    // Check Bus
    $sql = "SELECT * FROM buses
            WHERE bus_id = '$bus_id'
            AND company_id = '$company_id'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $sql = "UPDATE schedules SET
                bus_id = '$bus_id',
                route_id = '$route_id',
                departure_date = '$departure_date',
                departure_time = '$departure_time',
                arrival_time = '$arrival_time',
                ticket_price = '$ticket_price',
                status = '$status'
                WHERE schedule_id = '$schedule_id'
                AND bus_id IN (
                    SELECT bus_id FROM buses
                    WHERE company_id = '$company_id'
                )";

        if ($conn->query($sql)) {
            header('Location: schedules.php?success=updated');
            exit;
        }
    }

    $updateError = true;
}

// Get Company Buses
$sql = "SELECT bus_id, bus_name, bus_number, bus_type
        FROM buses
        WHERE company_id = '$company_id'
        ORDER BY bus_name";

$buses = $conn->query($sql);

// Get Routes
$routes = $conn->query("SELECT route_id, source, destination
                        FROM routes
                        ORDER BY source, destination");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Schedule | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/c_schedules_bar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="company-card edit-form-card p-4">
            <h3>Edit Schedule</h3>
            <?php if ($updateError) { ?><div class="alert alert-danger" role="alert">Schedule could not be updated.</div><?php } ?>
            <form method="post" action="schedule_edit.php?schedule_id=<?php echo $schedule_id; ?>">
                <div class="mb-3">
                    <label>Bus</label>
                    <select class="form-select" name="bus_id" required>
                        <?php while ($bus = $buses->fetch_assoc()) { ?>
                            <option value="<?php echo (int)$bus['bus_id']; ?>"
                                <?php echo (int)$schedule['bus_id'] === (int)$bus['bus_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bus['bus_name'] . ' - ' . $bus['bus_number'] . ' (' . $bus['bus_type'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Route</label>
                    <select class="form-select" name="route_id" required>
                        <?php while ($route = $routes->fetch_assoc()) { ?>
                            <option value="<?php echo (int)$route['route_id']; ?>"
                                <?php echo (int)$schedule['route_id'] === (int)$route['route_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($route['source'] . ' → ' . $route['destination'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Departure Date</label>
                    <input type="date" class="form-control" name="departure_date"
                           value="<?php echo htmlspecialchars($schedule['departure_date'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Departure Time</label>
                    <input type="time" class="form-control" name="departure_time"
                           value="<?php echo htmlspecialchars(substr($schedule['departure_time'], 0, 5), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Arrival Time</label>
                    <input type="time" class="form-control" name="arrival_time"
                           value="<?php echo htmlspecialchars(substr($schedule['arrival_time'], 0, 5), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Ticket Price</label>
                    <input type="number" min="0" step="0.01" class="form-control" name="ticket_price"
                           value="<?php echo htmlspecialchars($schedule['ticket_price'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select class="form-select" name="status" required>
                        <?php foreach (['scheduled', 'completed', 'cancelled'] as $status) { ?>
                            <option value="<?php echo $status; ?>" <?php echo $schedule['status'] === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn-save">Update Schedule</button>
                <a href="schedules.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
