<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_GET['company_id'];


$sql = "SELECT * FROM buses WHERE company_id = '$company_id'";
$result = $conn->query($sql);


$sql2 = "SELECT * FROM users
         WHERE company_id = '$company_id'
         AND role = 'company_admin'";
$result2 = $conn->query($sql2);


if ($result->num_rows > 0 || $result2->num_rows > 0) {
    header('Location: companies.php?error=company_in_use');
    exit;
}

$sql = "DELETE FROM bus_companies WHERE company_id = '$company_id'";

if ($conn->query($sql)) {
    header('Location: companies.php?success=deleted');
    exit;
}

header('Location: companies.php?error=delete_failed');
exit;
?>
