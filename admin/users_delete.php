<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['user_id'];

// Check Customer Bookings
$sql = "SELECT * FROM bookings WHERE user_id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header('Location: users.php?error=customer_has_bookings');
    exit;
}


$sql = "DELETE FROM users WHERE user_id = '$id' AND role = 'customer'";

if ($conn->query($sql) && $conn->affected_rows > 0) {
    header('Location: users.php?success=deleted');
    exit;
}

header('Location: users.php?error=delete_failed');
exit;
?>