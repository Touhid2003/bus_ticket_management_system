<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  
    $company_id = $_POST['company_id'];
    $bus_name = $_POST['bus_name'];
    $bus_type = $_POST['bus_type'];
    $total_seats = $_POST['total_seats'];
   $status = $_POST['status'];
    $bus_number  = $_POST['bus_number'];

$sql = "INSERT INTO buses(company_id, bus_name,bus_type,total_seats,status,bus_number)
VALUES('1','$bus_name','$bus_type','$total_seats','$status','$bus_number')";

$result = $conn->query($sql);

 if($result){    
         header('Location: ../company_admin/buses.php?success=1');
        exit;

    }


}


?>