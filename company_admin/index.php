<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../login.php');
    exit;
}

if(isset($_SESSION['role']) && $_SESSION['role'] === 'company_admin') {
    header('Location: dashboard.php');
    exit;
}
header('Location: ../login.php');
exit;



?>