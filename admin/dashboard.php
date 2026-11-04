<?php
session_start();

include '../config/db.php';


if ($_SESSION['role'] !== "super_admin") {
    header("Location: ../login.php");
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
include '../includes/sidebar.php';
?>



        <!-- ========== MAIN ========== -->
        <main class="content">
            <h1 class="h3 fw-bold mb-1">Dashboard</h1>
            <p class="text-secondary mb-4">Companies, fleet, bookings ও revenue-এর সারসংক্ষেপ।</p>

            <!-- Statistics Cards -->
            <section class="row g-3 mb-4">
                <div class="col-sm-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex gap-3">
                            <div class="icon-box bg-primary-subtle text-primary"><i class="bi bi-building"></i></div>
                            <div>
                                <div class="text-secondary small fw-medium">Total Companies</div>
                                <div class="fs-3 fw-bold" id="statCompanies">24</div>
                                <div class="text-success small fw-semibold">+2 this month</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex gap-3">
                            <div class="icon-box bg-purple-subtle"><i class="bi bi-bus-front"></i></div>
                            <div>
                                <div class="text-secondary small fw-medium">Total Buses</div>
                                <div class="fs-3 fw-bold" id="statBuses">186</div>
                                <div class="text-success small fw-semibold">+11 this month</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex gap-3">
                            <div class="icon-box bg-info-subtle text-info-emphasis"><i class="bi bi-people"></i></div>
                            <div>
                                <div class="text-secondary small fw-medium">Total Users</div>
                                <div class="fs-3 fw-bold" id="statUsers">12,480</div>
                                <div class="text-success small fw-semibold">+4.2% vs last month</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex gap-3">
                            <div class="icon-box bg-warning-subtle text-warning-emphasis"><i class="bi bi-ticket-perforated"></i></div>
                            <div>
                                <div class="text-secondary small fw-medium">Total Bookings</div>
                                <div class="fs-3 fw-bold" id="statBookings">9,342</div>
                                <div class="text-success small fw-semibold">+8.1% vs last month</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex gap-3">
                            <div class="icon-box bg-success-subtle text-success-emphasis"><i class="bi bi-cash-stack"></i></div>
                            <div>
                                <div class="text-secondary small fw-medium">Total Revenue</div>
                                <div class="fs-3 fw-bold" id="statRevenue">৳ 58.7 L</div>
                                <div class="text-success small fw-semibold">+12.4% vs last month</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Charts -->
            <section class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h6 fw-bold m-0">Monthly Bookings</h2>
                                <select class="form-select form-select-sm w-auto" aria-label="Year">
                                    <option>2026</option>
                                    <option>2025</option>
                                </select>
                            </div>
                            <canvas id="bookingsChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h6 fw-bold m-0">Revenue Overview (৳ Lakh)</h2>
                                <select class="form-select form-select-sm w-auto" aria-label="Year">
                                    <option>2026</option>
                                    <option>2025</option>
                                </select>
                            </div>
                            <canvas id="revenueChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Bookings -->
            <section class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-bold m-0">Recent Bookings</h2>
                        <a href="bookings.html" class="small fw-semibold text-decoration-none">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0" id="bookingsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Passenger</th>
                                    <th>Company</th>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Seats</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">BT20261031-A7F2</td>
                                    <td>Rahim Uddin</td>
                                    <td>Hanif Enterprise</td>
                                    <td>Dhaka → Chattogram</td>
                                    <td>31 Oct 2026</td>
                                    <td>A1, A2</td>
                                    <td>৳ 1,700</td>
                                    <td><span class="badge text-bg-success">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">BT20261031-C91B</td>
                                    <td>Nusrat Jahan</td>
                                    <td>Shyamoli Paribahan</td>
                                    <td>Dhaka → Sylhet</td>
                                    <td>31 Oct 2026</td>
                                    <td>B3</td>
                                    <td>৳ 900</td>
                                    <td><span class="badge text-bg-success">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">BT20261030-4D0E</td>
                                    <td>Tanvir Ahmed</td>
                                    <td>Ena Transport</td>
                                    <td>Dhaka → Khulna</td>
                                    <td>30 Oct 2026</td>
                                    <td>C2, C3, C4</td>
                                    <td>৳ 3,300</td>
                                    <td><span class="badge text-bg-warning">Pending</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">BT20261030-9E13</td>
                                    <td>Sumaiya Akter</td>
                                    <td>Green Line</td>
                                    <td>Dhaka → Cox's Bazar</td>
                                    <td>30 Oct 2026</td>
                                    <td>A5</td>
                                    <td>৳ 1,500</td>
                                    <td><span class="badge text-bg-success">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">BT20261029-71AB</td>
                                    <td>Mahmudul Hasan</td>
                                    <td>Soudia Coach</td>
                                    <td>Dhaka → Rajshahi</td>
                                    <td>29 Oct 2026</td>
                                    <td>D1, D2</td>
                                    <td>৳ 1,800</td>
                                    <td><span class="badge text-bg-danger">Cancelled</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">BT20261029-3C58</td>
                                    <td>Farhana Islam</td>
                                    <td>Hanif Enterprise</td>
                                    <td>Dhaka → Barishal</td>
                                    <td>29 Oct 2026</td>
                                    <td>B6</td>
                                    <td>৳ 650</td>
                                    <td><span class="badge text-bg-success">Confirmed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

<?php
include '../includes/admin_script.php';
?>
</body>

</html>