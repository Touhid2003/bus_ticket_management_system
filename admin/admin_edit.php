<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$userId = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
if ($userId < 1) {
    header('Location: admin.php?error=invalid_admin');
    exit;
}

<?php

// Get Admin Information
$sql = "SELECT * FROM users WHERE user_id = $userId AND role = 'company_admin'";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    header('Location: admin.php?error=invalid_admin');
    exit;
}

// Store Admin Data
$form = [
    'name' => $admin['name'],
    'email' => $admin['email'],
    'phone' => $admin['phone'],
    'company_id' => $admin['company_id']
];

$error = '';

// Update Admin Information
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company_id = (int)$_POST['company_id'];

    if ($name == '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone == '' || $company_id < 1) {
        $error = 'Enter valid information.';
    } else {

        $checkCompany = mysqli_query($conn, "SELECT company_id FROM bus_companies WHERE company_id = $company_id");

        if (mysqli_num_rows($checkCompany) == 0) {
            $error = 'Select a valid company.';
        } else {

            $sql = "UPDATE users 
                    SET name = '$name', email = '$email', phone = '$phone', company_id = $company_id 
                    WHERE user_id = $userId AND role = 'company_admin'";

            if (mysqli_query($conn, $sql)) {
                header('Location: admin.php?success=updated');
                exit;
            } else {
                $error = 'Unable to update admin.';
            }
        }
    }
}

// Get Company List
$companies = mysqli_query($conn, "SELECT company_id, company_name FROM bus_companies ORDER BY company_name");

?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Admin | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/adminbar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="company-card edit-form-card p-4">
            <div class="mb-4">
                <h3>Edit Admin</h3>
                <p>Update company administrator information.</p>
            </div>

            <?php if ($error !== '') { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php } ?>

            <form method="post" action="admin_edit.php">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="<?php echo $form['name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?php echo $form['email']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                           value="<?php echo $form['phone']; ?>" required>
                </div>

                <div class="mb-4">
                    <label for="company_id" class="form-label">Company</label>
                    <select id="company_id" name="company_id" class="form-select" required>
                        <option value="">Select Company</option>
                        <?php while ($company = $companies->fetch_assoc()) { ?>
                            <option value="<?php echo $company['company_id']; ?>"
                                <?php echo $company['company_id'] === $form['company_id'] ? 'selected' : ''; ?>>
                                <?php echo $company['company_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Admin</button>
                    <a href="admin.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
