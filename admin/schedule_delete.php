<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$schedule_id = $_GET['schedule_id'];

// Check Bookings
$sql = "SELECT * FROM bookings WHERE schedule_id = '$schedule_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header('Location: schedules.php?error=schedule_in_use');
    exit;
}

// Delete Schedule
$sql = "DELETE FROM schedules WHERE schedule_id = '$schedule_id'";

if ($conn->query($sql)) {
    header('Location: schedules.php?success=deleted');
    exit;
}

header('Location: schedules.php?error=delete_failed');
exit;
?>
