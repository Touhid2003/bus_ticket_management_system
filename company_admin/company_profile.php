<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];

// Update Company Profile
$updateError = false;

if (isset($_POST['update'])) {

    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE bus_companies SET
            company_name = '$company_name',
            email = '$email',
            phone = '$phone',
            address = '$address'
            WHERE company_id = '$company_id'";

    if ($conn->query($sql)) {
        header('Location: company_profile.php?success=updated');
        exit;
    } else {
        $updateError = true;
    }
}

// Get Company Information
$sql = "SELECT company_name, email, phone, address, status
        FROM bus_companies
        WHERE company_id = '$company_id'";

$result = $conn->query($sql);
$company = $result->fetch_assoc();

if (!$company) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Company Profile | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/cmsidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header">
            <div><h3>Company Profile</h3><p>Update your company contact information.</p></div>
        </div>
        <?php if (isset($_GET['success'])) { ?><div class="alert alert-success" role="status">Company profile updated.</div><?php } ?>
        <?php if ($updateError || isset($_GET['error'])) { ?><div class="alert alert-danger" role="alert">Company profile could not be updated.</div><?php } ?>

        <div class="company-card edit-form-card p-4">
            <form method="post">
                <div class="mb-3">
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control"
                           value="<?php echo htmlspecialchars($company['company_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo htmlspecialchars($company['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?php echo htmlspecialchars($company['phone'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($company['address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($company['status'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                </div>
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
