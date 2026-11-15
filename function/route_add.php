<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD']=== "POST"){
    $source = $_POST['Source'];
    $destination = $_POST['Destination'];
    $distance = $_POST['Distance'];
    $status = $_POST['Status'];

   $sql = "INSERT INTO routes
(source, destination, distance, status)
VALUES ('$source', '$destination', '$distance', '$status')";
    
    $result = $conn->query($sql);

    if($result){    
         header('Location: ../admin/route.php?success=1');
        exit;

    } 
}



?>