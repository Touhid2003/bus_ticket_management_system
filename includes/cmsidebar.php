<?php $active_page = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar" id="sidebar">
    <nav class="nav flex-column gap-1">
        <a class="nav-link <?php echo $active_page === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php" aria-label="Dashboard" title="Dashboard"><i class="bi bi-speedometer2"></i><span class="nav-text">Dashboard</span></a>
        <a class="nav-link <?php echo $active_page === 'buses.php' ? 'active' : ''; ?>" href="buses.php" aria-label="Buses" title="Buses"><i class="bi bi-bus-front"></i><span class="nav-text">Buses</span></a>
        <a class="nav-link <?php echo $active_page === 'routes.php' ? 'active' : ''; ?>" href="routes.php" aria-label="Routes" title="Routes"><i class="bi bi-signpost-split"></i><span class="nav-text">Routes</span></a>
        <a class="nav-link <?php echo $active_page === 'schedules.php' ? 'active' : ''; ?>" href="schedules.php" aria-label="Schedules" title="Schedules"><i class="bi bi-clock-history"></i><span class="nav-text">Schedules</span></a>
        <a class="nav-link <?php echo $active_page === 'bookings.php' ? 'active' : ''; ?>" href="bookings.php" aria-label="Bookings" title="Bookings"><i class="bi bi-ticket-perforated"></i><span class="nav-text">Bookings</span></a>
        <a class="nav-link <?php echo $active_page === 'passengers.php' ? 'active' : ''; ?>" href="passengers.php" aria-label="Passengers" title="Passengers"><i class="bi bi-people"></i><span class="nav-text">Passengers</span></a>
        <a class="nav-link <?php echo $active_page === 'payments.php' ? 'active' : ''; ?>" href="payments.php" aria-label="Payments" title="Payments"><i class="bi bi-credit-card"></i><span class="nav-text">Payments</span></a>
        <a class="nav-link <?php echo $active_page === 'company_profile.php' ? 'active' : ''; ?>" href="company_profile.php" aria-label="Company Profile" title="Company Profile"><i class="bi bi-building"></i><span class="nav-text">Company Profile</span></a>
    </nav>
   
</aside>