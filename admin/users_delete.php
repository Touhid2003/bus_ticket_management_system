<?php
include '../config/db.php';

$id = $_GET['users_id'];

$conn->query("DELETE FROM users WHERE users_id = '$id'");

 if($conn->affected_rows){
            header("Location: users.php");
           }


?>