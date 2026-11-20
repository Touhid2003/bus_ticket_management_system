<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$bus_id = $_GET['bus_id'];

// Get Bus Information
$sql = "SELECT * FROM buses
        WHERE bus_id = '$bus_id'
        AND company_id = '$company_id'";

$result = $conn->query($sql);
$bus = $result->fetch_assoc();

if (!$bus) {
    header('Location: buses.php?error=bus_not_found');
    exit;
}

// Update Bus
$updateError = false;

if (isset($_POST['update'])) {

    $bus_name = $_POST['bus_name'];
    $bus_type = $_POST['bus_type'];
    $total_seats = $_POST['total_seats'];
    $bus_number = $_POST['bus_number'];
    $status = $_POST['status'];

    $sql = "UPDATE buses SET
            bus_name = '$bus_name',
            bus_type = '$bus_type',
            total_seats = '$total_seats',
            bus_number = '$bus_number',
            status = '$status'
            WHERE bus_id = '$bus_id'
            AND company_id = '$company_id'";

    if ($conn->query($sql)) {
        header('Location: buses.php?success=updated');
        exit;
    } else {
        $updateError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Bus | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/c_buses_bar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="company-card edit-form-card p-4">
            <h3>Edit Bus</h3>
            <?php if ($updateError) { ?><div class="alert alert-danger" role="alert">Bus could not be updated.</div><?php } ?>
            <form method="post" action="bus_edit.php?bus_id=<?php echo $bus_id; ?>">
                <div class="mb-3">
                    <label>Bus Name</label>
                    <input type="text" class="form-control" name="bus_name"
                           value="<?php echo htmlspecialchars($bus['bus_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Bus Type</label>
                    <select class="form-select" name="bus_type" required>
                        <?php foreach (['AC', 'Non-AC', 'Sleeper', 'Business Class'] as $type) { ?>
                            <option value="<?php echo $type; ?>" <?php echo $bus['bus_type'] === $type ? 'selected' : ''; ?>><?php echo $type; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Total Seats</label>
                    <input type="number" min="1" class="form-control" name="total_seats"
                           value="<?php echo (int)$bus['total_seats']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Bus Number</label>
                    <input type="text" class="form-control" name="bus_number"
                           value="<?php echo htmlspecialchars($bus['bus_number'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select class="form-select" name="status" required>
                        <option value="active" <?php echo $bus['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $bus['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn-save">Update Bus</button>
                <a href="buses.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
