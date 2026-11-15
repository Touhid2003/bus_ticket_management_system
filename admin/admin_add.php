<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password =sha1($_POST['password']) ;
    $role = $_POST['role'];
    $company_id = $_POST['company_id'];
}

$sql = "INSERT INTO users(name,	email, phone, password,role,company_id)
values('$name', '$email', '$phone', '$password', '$role','$company_id')";

$raw = $conn->query($sql);

if ($conn->affected_rows > 0) {
    header('Location: admin.php?success=added');
    exit;
} else {
    echo "Admin not added!";
}


?>