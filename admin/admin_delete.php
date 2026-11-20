<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['user_id'])) {

    $user_id = $_GET['user_id'];

    $sql = "DELETE FROM users
            WHERE user_id = '$user_id'
            AND role = 'company_admin'";

    if ($conn->query($sql)) {
        header('Location: admin.php?success=deleted');
        exit;
    }
}

header('Location: admin.php?error=delete_failed');
exit;
?>