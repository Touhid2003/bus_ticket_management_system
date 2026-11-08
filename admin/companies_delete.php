<?php
include '../config/db.php';
$id = $_GET['company_id'];

$conn->query("DELETE FROM bus_companies WHERE company_id='$id'");

 if($conn->affected_rows){
            header("Location: companies.php");
           }


?>