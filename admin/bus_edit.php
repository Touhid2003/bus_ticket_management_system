<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$bus_id = filter_input(INPUT_GET, 'bus_id', FILTER_VALIDATE_INT);
if (!$bus_id || $bus_id < 1) {
    header('Location: buses.php');
    exit;
}

if (isset($_POST['update'])) {

    $company_id = $_POST['company_id'];
    $bus_name = $_POST['bus_name'];
    $bus_type = $_POST['bus_type'];
    $total_seats = $_POST['total_seats'];
    $bus_number = $_POST['bus_number'];
    $status = $_POST['status'];

    $sql = "UPDATE buses SET
            company_id = '$company_id',
            bus_name = '$bus_name',
            bus_type = '$bus_type',
            total_seats = '$total_seats',
            bus_number = '$bus_number',
            status = '$status'
            WHERE bus_id = '$bus_id'";

    if ($conn->query($sql)) {
        header('Location: buses.php?success=updated');
        exit;
    }
}

// Get Bus Information
$sql = "SELECT * FROM buses WHERE bus_id = '$bus_id'";
$result = $conn->query($sql);
$bus = $result->fetch_assoc();

if (!$bus) {
    header('Location: buses.php');
    exit;
}

// Get Companies
$companies = $conn->query("SELECT * FROM bus_companies");
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
    <?php include '../includes/buses.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="company-card edit-form-card p-4">
            <h3>Edit Bus</h3>
            <form method="post">
                <div class="mb-3">
                    <label>Company</label>
                    <select class="form-select" name="company_id" required>
                        <?php while ($company = $companies->fetch_assoc()) { ?>
                            <option value="<?php echo $company['company_id']; ?>" <?php echo $bus['company_id'] === $company['company_id'] ? 'selected' : ''; ?>>
                                <?php echo $company['company_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3"><label>Bus Name</label><input class="form-control" name="bus_name" value="<?php echo $bus['bus_name']; ?>" required></div>
                <div class="mb-3">
                    <label>Bus Type</label>
                    <select class="form-select" name="bus_type" required>
                        <?php foreach (['AC', 'Non-AC', 'Sleeper', 'Business Class'] as $type) { ?>
                            <option value="<?php echo $type; ?>" <?php echo $bus['bus_type'] === $type ? 'selected' : ''; ?>><?php echo $type; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3"><label>Total Seats</label><input type="number" min="1" class="form-control" name="total_seats" value="<?php echo $bus['total_seats']; ?>" required></div>
                <div class="mb-3"><label>Bus Number</label><input class="form-control" name="bus_number" value="<?php echo $bus['bus_number']; ?>" required></div>
                <div class="mb-3">
                    <label>Status</label>
                    <select class="form-select" name="status" required>
                        <option value="active" <?php echo $bus['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $bus['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit" name="update">Update Bus</button>
                <a class="btn btn-secondary" href="buses.php">Cancel</a>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
