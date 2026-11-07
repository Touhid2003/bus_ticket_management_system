<nav class="navbar topbar bg-white border-bottom sticky-top px-3">
  <div class="d-flex align-items-center gap-2">
   <a class="navbar-brand d-flex align-items-center gap-2 fw-bold m-0 text-decoration-none"
   href="dashboard.php">

    <img src="../assets/images/logo" alt="BusGo Logo" width="30" height="30" class="d-inline-block align-text-top">

    <span class="logo-text">
       
        <small>ONLINE TICKETING</small>
    </span>

</a>
  </div>
  <div class="d-flex align-items-center gap-3">
    <div class="dropdown">
      <button class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
        <i class="bi bi-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">5</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li>
          <h6 class="dropdown-header">Notifications</h6>
        </li>
        <li><a class="dropdown-item" href="#">নতুন বুকিং BT20261031-A7F2</a></li>
        <li><a class="dropdown-item" href="#">একটি বুকিং বাতিল হয়েছে</a></li>
        <li><a class="dropdown-item" href="#">নতুন কোম্পানি আবেদন করেছে</a></li>
      </ul>
    </div>
    <div class="dropdown">
      <button class="btn d-flex align-items-center gap-2 p-0 border-0" data-bs-toggle="dropdown">
        <span class="avatar">    <?php echo strtoupper(substr($_SESSION['name'], 0, 2)); ?>
</span>
        <span class="d-none d-md-block text-start lh-sm">
          <strong class="d-block small"><?php echo $_SESSION['name']; ?></strong>
          <small class="text-secondary"><?php echo $_SESSION['email']; ?> </small>
        </span>
        <i class="bi bi-chevron-down small"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
        
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item text-danger" 
        href="index.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
