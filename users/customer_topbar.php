<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$customerName = (string)($_SESSION['name'] ?? 'Passenger');
$customerInitials = strtoupper(substr($customerName, 0, 2));
?>
<nav class="customer-topbar" aria-label="Customer navigation">
    <div class="customer-topbar-inner">
        <a class="customer-brand" href="home.php">
            <span class="customer-brand-icon" aria-hidden="true">B</span>
            <span>Routeline</span>
        </a>
        <div class="customer-nav-links">
            <a class="<?php echo $currentPage === 'home.php' ? 'active' : ''; ?>" href="home.php">Home</a>
            <a class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>" href="profile.php">Profile</a>
            <a class="<?php echo $currentPage === 'tickets.php' ? 'active' : ''; ?>" href="tickets.php">My Tickets</a>
        </div>
        <div class="customer-account">
            <span class="customer-avatar" aria-hidden="true"><?php echo htmlspecialchars($customerInitials, ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="customer-name"><?php echo htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8'); ?></span>
            <a class="customer-logout" href="../logout.php">Log out</a>
        </div>
    </div>
</nav>
