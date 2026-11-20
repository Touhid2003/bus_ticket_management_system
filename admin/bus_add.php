<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: buses.php');
    exit;
}

$company_id = $_POST['company_id'];
$bus_name = $_POST['bus_name'];
$bus_type = $_POST['bus_type'];
$total_seats = $_POST['total_seats'];
$bus_number = $_POST['bus_number'];
$status = $_POST['status'];

$sql = "INSERT INTO buses (company_id, bus_name, bus_type, total_seats, bus_number, status)
        VALUES ('$company_id', '$bus_name', '$bus_type', '$total_seats', '$bus_number', '$status')";

if ($conn->query($sql)) {
    header('Location: buses.php?success=added');
    exit;
}

header('Location: buses.php?error=add_failed');
exit;
?>
