<?php
include_once '../config/db.php';



if($_SERVER['REQUEST_METHOD'] === "POST"){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = sha1($_POST['password']);
    $phone = $_POST['phone'];

    $role = "customer";

    $cheakEmail = "SELECT * FROM users WHERE email = '$email'";

    $result = $conn->query($cheakEmail);

    if($result->num_rows> 0){
        header("location: ../login.php?error=email_exists");
        exit;
    }
    else{

    $sql = "INSERT INTO users(user_id, name, email, phone, password, role)
    values(NULL, '$name', '$email', '$phone', '$password', '$role')";

    $conn->query($sql);

    if($conn->affected_rows){
           header("Location: ../login.php?success=registered");
            exit;
    }
   

}

}




?>