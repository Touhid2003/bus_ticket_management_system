<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$bus_id = $_GET['bus_id'];

$sql = "SELECT * FROM schedules WHERE bus_id = '$bus_id'";
$result = $conn->query($sql);

$sql2 = "SELECT * FROM seats WHERE bus_id = '$bus_id'";
$result2 = $conn->query($sql2);

if ($result->num_rows > 0 || $result2->num_rows > 0) {
    header('Location: buses.php?error=bus_in_use');
    exit;
}

$sql = "DELETE FROM buses WHERE bus_id = '$bus_id'";

if ($conn->query($sql)) {
    header('Location: buses.php?success=deleted');
    exit;
}

header('Location: buses.php?error=delete_failed');
exit;
?>