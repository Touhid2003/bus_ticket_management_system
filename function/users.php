<?php

include '../config/db.php';

$sql = "SELECT * FROM users WHERE role = 'customer'";

$result = $conn->query($sql);

?>