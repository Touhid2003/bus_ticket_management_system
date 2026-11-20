<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Update Profile
if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = "UPDATE users SET
            name = '$name',
            email = '$email',
            phone = '$phone'
            WHERE user_id = '$user_id'
            AND role = 'super_admin'";

    if ($conn->query($sql)) {

        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;

        header('Location: settings.php?success=updated');
        exit;
    }

    header('Location: settings.php?error=update_failed');
    exit;
}


$sql = "SELECT name, email, phone FROM users
        WHERE user_id = '$user_id'
        AND role = 'super_admin'";

$result = $conn->query($sql);
$admin = $result->fetch_assoc();

if (!$admin) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header"><div><h3>Account Settings</h3><p>Update your administrator profile.</p></div></div>
        <?php if (isset($_GET['success'])) { ?><div class="alert alert-success" role="status">Profile updated.</div><?php } ?>
        <?php if (isset($_GET['error'])) { ?><div class="alert alert-danger" role="alert">Profile could not be updated.</div><?php } ?>
        <div class="company-card edit-form-card p-4">
            <form method="post">
                <div class="mb-3"><label>Name</label><input class="form-control" name="name" value="<?php echo htmlspecialchars($admin['name'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="mb-3"><label>Email</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="mb-3"><label>Phone</label><input class="form-control" name="phone" value="<?php echo htmlspecialchars($admin['phone'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
