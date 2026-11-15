<?php
$sql = "SELECT users.*, 
                bus_companies.company_name,
                bus_companies.status AS company_status
        FROM users
        LEFT JOIN bus_companies
        ON users.company_id = bus_companies.company_id
        WHERE users.role IN ('super_admin', 'company_admin')";
$result = $conn->query($sql);

?>