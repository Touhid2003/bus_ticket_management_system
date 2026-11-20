<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}
if (isset($_POST['submit'])) {

    $bus_id = $_POST['bus_id'];
    $route_id = $_POST['route_id'];
    $departure_date = $_POST['departure_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $ticket_price = $_POST['ticket_price'];
    $status = $_POST['status'];

    $sql = "INSERT INTO schedules
            (bus_id, route_id, departure_date, departure_time, arrival_time, ticket_price, status)
            VALUES ('$bus_id', '$route_id', '$departure_date', '$departure_time', '$arrival_time', '$ticket_price', '$status')";

    if ($conn->query($sql)) {
        header('Location: schedules.php?success=added');
        exit;
    }

    header('Location: schedules.php?error=add_failed');
    exit;
}

header('Location: schedules.php');
exit;

?>
