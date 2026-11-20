<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$bus_id = $_GET['bus_id'];

// Check Bus
$sql = "SELECT * FROM buses
        WHERE bus_id = '$bus_id'
        AND company_id = '$company_id'";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: buses.php?error=bus_not_found');
    exit;
}

// Check Bus Schedules
$sql = "SELECT * FROM schedules WHERE bus_id = '$bus_id'";
$result = $conn->query($sql);

// Check Bus Seats
$sql2 = "SELECT * FROM seats WHERE bus_id = '$bus_id'";
$result2 = $conn->query($sql2);

if ($result->num_rows > 0 || $result2->num_rows > 0) {
    header('Location: buses.php?error=bus_in_use');
    exit;
}

// Delete Bus
$sql = "DELETE FROM buses
        WHERE bus_id = '$bus_id'
        AND company_id = '$company_id'";

if ($conn->query($sql) && $conn->affected_rows > 0) {
    header('Location: buses.php?success=deleted');
    exit;
}

header('Location: buses.php?error=delete_failed');
exit;
?>