<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$schedule_id = $_GET['schedule_id'];

// Check Schedule
$sql = "SELECT schedules.schedule_id
        FROM schedules
        INNER JOIN buses ON schedules.bus_id = buses.bus_id
        WHERE schedules.schedule_id = '$schedule_id'
        AND buses.company_id = '$company_id'";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: schedules.php?error=schedule_not_found');
    exit;
}

// Check Bookings
$sql = "SELECT * FROM bookings WHERE schedule_id = '$schedule_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header('Location: schedules.php?error=schedule_in_use');
    exit;
}

// Delete Schedule
$sql = "DELETE FROM schedules
        WHERE schedule_id = '$schedule_id'
        AND bus_id IN (
            SELECT bus_id FROM buses
            WHERE company_id = '$company_id'
        )";

if ($conn->query($sql) && $conn->affected_rows > 0) {
    header('Location: schedules.php?success=deleted');
    exit;
}

header('Location: schedules.php?error=delete_failed');
exit;
?>