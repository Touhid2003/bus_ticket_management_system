<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$route_id = $_GET['route_id'];

// Check Route Usage
$sql = "SELECT * FROM schedules WHERE route_id = '$route_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header('Location: route.php?error=route_in_use');
    exit;
}

// Delete Route
$sql = "DELETE FROM routes WHERE route_id = '$route_id'";

if ($conn->query($sql)) {
    header('Location: route.php?success=deleted');
    exit;
}

header('Location: route.php?error=delete_failed');
exit;
?>