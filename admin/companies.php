<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: index.php');
    exit;
}
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
       <div class="container-fluid p-4">

    <!-- Page Title -->
    <div class="mb-4">
        <h2 class="page-title">
            <i class="bi bi-building text-primary"></i>
            Add Company
        </h2>
        <p class="text-muted">
            Register a new bus company.
        </p>
    </div>

    <!-- Company Form -->
    <div class="form-card">

        <div class="card-header-custom">
            <i class="bi bi-building me-2"></i>
            Company Information
        </div>

        <div class="form-body">

            <form action="" method="POST">

                <div class="row g-4">

                    <!-- Company Name -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Company Name <span class="required">*</span>
                        </label>
                        <input type="text"
                               name="company_name"
                               class="form-control"
                               placeholder="Enter company name"
                               maxlength="100"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Company Email
                        </label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="company@example.com"
                               maxlength="100">
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Company Phone
                        </label>
                        <input type="tel"
                               name="phone"
                               class="form-control"
                               placeholder="01XXXXXXXXX"
                               maxlength="20">
                    </div>

                    <!-- Address -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Company Address
                        </label>
                        <textarea name="address"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Enter company address"></textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Company Status <span class="required">*</span>
                        </label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="mt-4 d-flex gap-2 flex-wrap">

                    <button type="submit"
                            name="add_company"
                            class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add Company
                    </button>

                    <button type="reset"
                            class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Reset
                    </button>

                    <a href="companies.php"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancel
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>
<?php
include '../includes/admin_script.php';
?>
</body>

</html>