<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $company_id = $_POST['company_id'];

    $sql = "UPDATE users SET
            name = '$name',
            email = '$email',
            phone = '$phone',
            company_id = '$company_id'
            WHERE user_id = '$user_id'";

    if ($conn->query($sql)) {
        header('Location: admin.php?success=updated');
        exit;
    }
}

header('Location: admin.php?error=update_failed');
exit;
?>