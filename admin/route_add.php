<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_POST['submit'])) {

    $source = $_POST['Source'];
    $destination = $_POST['Destination'];
    $distance = $_POST['Distance'];
    $status = $_POST['Status'];

    $sql = "INSERT INTO routes (source, destination, distance, status)
            VALUES ('$source', '$destination', '$distance', '$status')";

    if ($conn->query($sql)) {
        header('Location: route.php?success=added');
        exit;
    }

    header('Location: route.php?error=add_failed');
    exit;
}

header('Location: route.php');
exit;
?>
