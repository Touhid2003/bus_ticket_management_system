
<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = (int)($_SESSION['company_id'] ?? 0);
if ($company_id < 1) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../company_admin/schedules.php');
    exit;
}

$bus_id = (int)($_POST['bus_id'] ?? 0);
$route_id = (int)($_POST['route_id'] ?? 0);
$departure_date = $_POST['departure_date'] ?? '';
$departure_time = $_POST['departure_time'] ?? '';
$arrival_time = $_POST['arrival_time'] ?? '';
$ticket_price = (float)($_POST['ticket_price'] ?? 0);
$status = $_POST['status'] ?? '';

$statement = $conn->prepare("SELECT bus_id FROM buses WHERE bus_id = ? AND company_id = ? AND status = 'active'");
$statement->bind_param('ii', $bus_id, $company_id);
$statement->execute();
$bus = $statement->get_result()->fetch_assoc();
$statement->close();
if (!$bus) {
    header('Location: ../company_admin/schedules.php?error=invalid_bus');
    exit;
}

$statement = $conn->prepare("SELECT route_id FROM routes WHERE route_id = ? AND status = 'active'");
$statement->bind_param('i', $route_id);
$statement->execute();
$route = $statement->get_result()->fetch_assoc();
$statement->close();
if (!$route) {
    header('Location: ../company_admin/schedules.php?error=invalid_route');
    exit;
}

$sql = 'INSERT INTO schedules (bus_id, route_id, departure_date, departure_time, arrival_time, ticket_price, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)';
$statement = $conn->prepare($sql);
$statement->bind_param('iisssds', $bus_id, $route_id, $departure_date, $departure_time, $arrival_time, $ticket_price, $status);

if ($statement->execute()) {
    $statement->close();
    header('Location: ../company_admin/schedules.php?success=added');
    exit;
}

$statement->close();
header('Location: ../company_admin/schedules.php?error=add_failed');
exit;
?>
