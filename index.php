




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Online Bus Ticket Booking</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
        }

        .navbar {
            background: #ffffff;
        }

        .navbar-brand {
            font-size: 25px;
            font-weight: bold;
            color: #0d6efd;
        }

        .hero {
            background: linear-gradient(120deg, #0d6efd, #003b95);
            color: white;
            padding: 100px 0;
        }

        .hero h1 {
            font-size: 45px;
            font-weight: bold;
        }

        .search-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            margin-top: 35px;
        }

        .search-box label {
            color: #333;
            font-weight: 600;
        }

        .section-title {
            font-weight: bold;
            color: #222;
        }

        .route-card {
            border: none;
            border-radius: 12px;
            transition: 0.3s;
        }

        .route-card:hover {
            transform: translateY(-5px);
        }

        .route-card .card-body {
            padding: 25px;
        }

        .feature-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            height: 100%;
        }

        footer {
            background: #111827;
            color: white;
            padding: 35px 0;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            🚌 BusTicket
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#routes">Routes</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="admin/index.php">Login</a>
                </li>

                <li class="nav-item ms-lg-2">
                    <a class="btn btn-primary px-4" href="register.php">
                        Register
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>


<!-- Hero Section -->
<section class="hero">
    <div class="container text-center">

        <h1>Travel Easy, Book Smart</h1>

        <p class="lead mt-3">
            Book your bus tickets online anytime, anywhere.
        </p>

        <!-- Bus Search Form -->
        <div class="search-box">

            <form action="search_bus.php" method="GET">

                <div class="row g-3 align-items-end">

                    <div class="col-md-3 text-start">
                        <label>From</label>
                        <select name="source" class="form-select" required>
                            <option value="">Select City</option>
                            <option>Dhaka</option>
                            <option>Chattogram</option>
                            <option>Bogura</option>
                            <option>Rajshahi</option>
                            <option>Sylhet</option>
                            <option>Khulna</option>
                        </select>
                    </div>

                    <div class="col-md-3 text-start">
                        <label>To</label>
                        <select name="destination" class="form-select" required>
                            <option value="">Select City</option>
                            <option>Dhaka</option>
                            <option>Chattogram</option>
                            <option>Bogura</option>
                            <option>Rajshahi</option>
                            <option>Sylhet</option>
                            <option>Khulna</option>
                        </select>
                    </div>

                    <div class="col-md-3 text-start">
                        <label>Journey Date</label>
                        <input type="date" name="journey_date"
                               class="form-control" required
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            Search Bus
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>
</section>


<!-- Popular Routes -->
<section class="py-5" id="routes">

    <div class="container">

        <div class="text-center mb-4">
            <h2 class="section-title">Popular Routes</h2>
            <p class="text-muted">Explore popular destinations</p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card route-card shadow-sm">
                    <div class="card-body">
                        <h5>🚌 Dhaka → Chattogram</h5>
                        <p class="text-muted">Comfortable journey</p>
                        <a href="#" class="btn btn-outline-primary">
                            View Buses
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card route-card shadow-sm">
                    <div class="card-body">
                        <h5>🚌 Dhaka → Bogura</h5>
                        <p class="text-muted">Easy online booking</p>
                        <a href="#" class="btn btn-outline-primary">
                            View Buses
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card route-card shadow-sm">
                    <div class="card-body">
                        <h5>🚌 Dhaka → Sylhet</h5>
                        <p class="text-muted">Travel with comfort</p>
                        <a href="#" class="btn btn-outline-primary">
                            View Buses
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>


<!-- Features -->
<section class="py-5 bg-light" id="features">

    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title">Why Choose Us?</h2>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <h1>🎫</h1>
                    <h5>Easy Booking</h5>
                    <p class="text-muted">
                        Book tickets easily from your home.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <h1>💳</h1>
                    <h5>Secure Payment</h5>
                    <p class="text-muted">
                        Convenient and secure payment system.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <h1>🚌</h1>
                    <h5>Multiple Routes</h5>
                    <p class="text-muted">
                        Find buses for different destinations.
                    </p>
                </div>
            </div>

        </div>

    </div>

</section>


<!-- Footer -->
<footer>
    <div class="container text-center">

        <h4>🚌 BusTicket</h4>

        <p class="text-secondary">
            Online Bus Ticket Booking Management System
        </p>

        <hr>

        <p class="mb-0">
            © 2026 BusTicket. All Rights Reserved.
        </p>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
