<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$schedule_id = $_GET['schedule_id'];

// Update Schedule
if (isset($_POST['update'])) {

    $bus_id = $_POST['bus_id'];
    $route_id = $_POST['route_id'];
    $departure_date = $_POST['departure_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $ticket_price = $_POST['ticket_price'];
    $status = $_POST['status'];

    $sql = "UPDATE schedules SET
            bus_id = '$bus_id',
            route_id = '$route_id',
            departure_date = '$departure_date',
            departure_time = '$departure_time',
            arrival_time = '$arrival_time',
            ticket_price = '$ticket_price',
            status = '$status'
            WHERE schedule_id = '$schedule_id'";

    if ($conn->query($sql)) {
        header('Location: schedules.php?success=updated');
        exit;
    }
}


$sql = "SELECT * FROM schedules WHERE schedule_id = '$schedule_id'";
$result = $conn->query($sql);
$schedule = $result->fetch_assoc();

if (!$schedule) {
    header('Location: schedules.php');
    exit;
}


$buses = $conn->query("SELECT * FROM buses ORDER BY bus_name");

$routes = $conn->query("SELECT * FROM routes ORDER BY source, destination");
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
        <?php include '../includes/schedules_bar.php'; ?>
        <main class="container-fluid py-4 px-4">
            <div class="company-card edit-form-card p-4">
                <h3>Edit Schedule</h3>
                <form method="post">
                    <div class="mb-3">
                        <label>Bus</label>
                        <select class="form-select" name="bus_id" required>
                            <?php while ($bus = $buses->fetch_assoc()) { ?>
                                <option value="<?php echo $bus['bus_id']; ?>" <?php echo $schedule['bus_id'] === (int)$bus['bus_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($bus['bus_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Route</label>
                        <select class="form-select" name="route_id" required>
                            <?php while ($route = $routes->fetch_assoc()) { ?>
                                <option value="<?php echo $route['route_id']; ?>" <?php echo $schedule['route_id'] === $route['route_id'] ? 'selected' : ''; ?>>
                                    <?php echo $route['source'] . ' → ' . $route['destination']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3"><label>Departure Date</label><input type="date" class="form-control" name="departure_date" value="<?php echo $schedule['departure_date']; ?>" required></div>
                    <div class="mb-3">
                        <label>Departure Time</label>
                        <input type="time" class="form-control" name="departure_time" value="<?php echo substr($schedule['departure_time'], 0, 5); ?>" required>
                    </div>
                    <div class="mb-3"><label>Arrival Time</label><input type="time" class="form-control" name="arrival_time" value="<?php echo substr($schedule['arrival_time'], 0, 5); ?>" required></div>
                    <div class="mb-3"><label>Ticket Price</label><input type="number" min="0" step="0.01" class="form-control" name="ticket_price" value="<?php echo $schedule['ticket_price']; ?>" required></div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select class="form-select" name="status" required>
                            <?php foreach (['scheduled', 'completed', 'cancelled'] as $status) { ?>
                                <option value="<?php echo $status; ?>" <?php echo $schedule['status'] === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit" name="update">Update Schedule</button>
                    <a class="btn btn-secondary" href="schedules.php">Cancel</a>
                </form>
            </div>
        </main>
    </div>
    <?php include '../includes/admin_script.php'; ?>
</body>

</html>