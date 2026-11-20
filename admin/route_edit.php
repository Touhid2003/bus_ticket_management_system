<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$route_id = $_GET['route_id'];

// Update Route
if (isset($_POST['update'])) {

    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $distance = $_POST['distance'];
    $status = $_POST['status'];

    $sql = "UPDATE routes SET
            source = '$source',
            destination = '$destination',
            distance = '$distance',
            status = '$status'
            WHERE route_id = '$route_id'";

    if ($conn->query($sql)) {
        header('Location: route.php?success=updated');
        exit;
    }
}

// Get Route Information
$sql = "SELECT * FROM routes WHERE route_id = '$route_id'";
$result = $conn->query($sql);
$route = $result->fetch_assoc();

if (!$route) {
    header('Location: route.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Route | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/route_bar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="company-card edit-form-card p-4">
            <h3>Edit Route</h3>
            <form method="post">
                <div class="mb-3"><label>Source</label><input class="form-control" name="source" value="<?php echo htmlspecialchars($route['source'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="mb-3"><label>Destination</label><input class="form-control" name="destination" value="<?php echo htmlspecialchars($route['destination'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="mb-3"><label>Distance</label><input type="number" min="0.01" step="0.01" class="form-control" name="distance" value="<?php echo $route['distance']; ?>" required></div>
                <div class="mb-3">
                    <label>Status</label>
                    <select class="form-select" name="status" required>
                        <option value="active" <?php echo $route['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $route['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit" name="update">Update Route</button>
                <a class="btn btn-secondary" href="route.php">Cancel</a>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
