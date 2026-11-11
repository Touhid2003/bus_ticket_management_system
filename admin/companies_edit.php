
<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

// Get Company ID
$company_id = $_GET['company_id'];

// Update Company
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    $conn->query("UPDATE bus_companies SET
        company_name='$name',
        email='$email',
        phone='$phone',
        address='$address',
        status='$status'
        WHERE company_id=$company_id");

    if ($conn->affected_rows >= 0) {
        header('Location: companies.php?success=updated');
        exit;
    }
}

// Display Company Data
$data = $conn->query("SELECT * FROM bus_companies WHERE company_id=$company_id");

$row = $data->fetch_object();

if (!$row) {
    header('Location: companies.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Company | BusGo</title>

    <?php include '../includes/admin_css.php'; ?>

</head>

<body>

<?php include '../includes/topbar.php'; ?>

<div class="layout">

    <?php include '../includes/admincmsidebar.php'; ?>

    <div class="container-fluid py-4 px-4">

        <div class="company-card edit-form-card p-4">
            <div>
                <h3>Edit Company</h3>
                <p>Update company information.</p>
            </div>

            <form method="POST">

                <div class="mb-3">
                    <label>Company Name</label>
                    <input type="text" name="name"
                        class="form-control"
<<<<<<< HEAD
                        value="<?php
                         echo $row->company_name; ?>"
=======
                        value="<?php echo $row->company_name; ?>"
>>>>>>> 5da370d56de4d399ca9db4d3d22009ae3879bc79
                        required>
                </div>

                <div class="mb-3">
                    <label>Email Address</label>
                    <input type="email" name="email"
                        class="form-control"
                        value="<?php echo $row->email; ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label>Phone Number</label>
                    <input type="text" name="phone"
                        class="form-control"
                        value="<?php echo $row->phone; ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label>Company Address</label>
                    <textarea name="address"
                        class="form-control"
                        rows="3"><?php echo $row->address; ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Status</label>

                    <select name="status" class="form-select">

                        <option value="active"
                            <?php echo $row->status == 'active' ? 'selected' : ''; ?>>
                            Active
                        </option>

                        <option value="inactive"
                            <?php echo $row->status == 'inactive' ? 'selected' : ''; ?>>
                            Inactive
                        </option>

                    </select>
                </div>

                <div class="d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        Update Company
                    </button>

                    <a href="companies.php" class="btn btn-secondary">
                        Back to List
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include '../includes/admin_script.php'; ?>

</body>
</html>
