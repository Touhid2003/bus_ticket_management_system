<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    $sql = "INSERT INTO bus_companies
    (company_name, email, phone, address, status)
    VALUES ('$name', '$email', '$phone', '$address', '$status')";

    $conn->query($sql);

    if ($conn->affected_rows) {
        header('Location: companies.php?success=added');
        exit;
    }
}

// Company List
$rawData = $conn->query("SELECT * FROM bus_companies");



?>


<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | BusGo</title>
   <?php
include '../includes/admin_css.php';
?>
</head>

<body>

<?php
include '../includes/topbar.php';
?>


    <div class="layout">

   <?php
include '../includes/admincmsidebar.php';
?>



        <!-- ========== MAIN ========== -->
      <div class="container-fluid py-4 px-4">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h3>Companies Management</h3>
            <p>Manage all bus companies from here.</p>
        </div>

        <button class="btn-add" id="openForm">
            <i class="bi bi-plus-lg"></i> Add Company
        </button>
    </div>

    <div id="companyMessage" class="alert d-none" role="status" aria-live="polite"></div>

    <!-- Search and Table -->
    <div class="company-card">

        <div class="table-header">
            <h5>All Companies</h5>

            <input type="text" id="searchInput"
                   class="form-control search-box"
                   placeholder="Search company...">
        </div>

        <div class="table-responsive">
            <table class="table company-table" id="companyTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

<?php while ($row = $rawData->fetch_assoc()) { ?>

<tr>
    
        <td><?php echo $row['company_id']; ?></td>
    

    <td>
        <div class="company-name">
            <?php echo $row['company_name']; ?>
        </div>
    </td>

    <td><?php echo $row['email']; ?></td>

    <td><?php echo $row['phone']; ?></td>

    <td><?php echo $row['address']; ?></td>
    <td>
    <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
</td>
    

    <td>
        <span class="status <?php echo $row['status']; ?>">
            <?php echo ucfirst($row['status']); ?>
        </span>
    </td>

    <td>
        <a class="action-btn edit" href="companies_edit.php?company_id<?php echo $row['company_id']; ?>" title="edit" type="button">
            Edit
        </a>
       
        <a
    class="action-btn delete"
    onclick="return confirm('Are you sure to delete?')"
    href="companies_delete.php?company_id=<?php echo $row['company_id']; ?>">
    Delete
       </a>

    </td>
</tr>

<?php } ?>

</tbody>
            </table>
        </div>

    </div>
</div>
<div class="form-overlay" id="formOverlay">

    <div class="form-popup">

        <div class="form-heading">
            <div>
                <h4>Add New Company</h4>
                <p>Enter company information below.</p>
            </div>

            <button class="close-btn" id="closeForm">&times;</button>
        </div>

        

        <form id="companyForm" method="post">

            <div class="mb-3">
                <label for="companyName">Company Name</label>
                <input type="text" class="form-control"
                   id="companyName" name="name" placeholder="Enter company name" required>
            </div>

            <div class="mb-3">
                <label for="companyEmail">Email Address</label>
                <input type="email" class="form-control"
                   id="companyEmail" name="email" placeholder="Enter email address" required>
            </div>

            <div class="mb-3">
                <label for="companyPhone">Phone Number</label>
                <input type="text" class="form-control"
                   id="companyPhone" name="phone" placeholder="Enter phone number" required>
            </div>

            <div class="mb-3">
                <label for="companyAddress">Company Address</label>
                <textarea class="form-control" rows="3"
                      id="companyAddress" name="address" placeholder="Enter company address"></textarea>
            </div>

            <div class="mb-4">
                <label for="companyStatus">Status</label>
                <select class="form-select" id="companyStatus" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="form-buttons">
                <button type="button" class="btn-cancel" id="cancelForm">
                    Cancel
                </button>

                <button type="submit" class="btn-save">
                    Save Company
                </button>
            </div>

        </form>

    </div>
</div>
<?php
include '../includes/admin_script.php';
?>
</body>

</html>