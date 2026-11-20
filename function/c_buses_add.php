<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = (int)($_SESSION['company_id'] ?? 0);
if ($company_id < 1) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../company_admin/buses.php');
    exit;
}

$bus_name = $_POST['bus_name'] ?? '';
$bus_type = $_POST['bus_type'] ?? '';
$total_seats = (int)($_POST['total_seats'] ?? 0);
$status = $_POST['status'] ?? '';
$bus_number = $_POST['bus_number'] ?? '';

$sql = 'INSERT INTO buses (company_id, bus_name, bus_type, total_seats, status, bus_number)
        VALUES (?, ?, ?, ?, ?, ?)';
$statement = $conn->prepare($sql);
$statement->bind_param('ississ', $company_id, $bus_name, $bus_type, $total_seats, $status, $bus_number);

if ($statement->execute()) {
    $statement->close();
    header('Location: ../company_admin/buses.php?success=added');
    exit;
}

$statement->close();
header('Location: ../company_admin/buses.php?error=add_failed');
exit;
?>