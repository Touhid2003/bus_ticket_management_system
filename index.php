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
        
/* BusGo Carousel */

#busGoCarousel {
    border-radius: 16px;
    overflow: hidden;
}

#busGoCarousel .carousel-item img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    filter: brightness(65%);
}

#busGoCarousel .carousel-caption {
    bottom: 45px;
    left: 7%;
    right: 7%;
    text-align: left;
}

#busGoCarousel .carousel-caption h2 {
    font-size: 32px;
    font-weight: 700;
    color: white;
}

#busGoCarousel .carousel-caption p {
    font-size: 15px;
    color: #f1f5f9;
}

#busGoCarousel .carousel-indicators {
    margin-bottom: 12px;
}

@media (max-width: 768px) {
    #busGoCarousel .carousel-item img {
        height: 210px;
    }

    #busGoCarousel .carousel-caption {
        bottom: 25px;
    }

    #busGoCarousel .carousel-caption h2 {
        font-size: 20px;
    }

    #busGoCarousel .carousel-caption p {
        font-size: 12px;
    }
}

       

        .search-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            
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
<nav class="navbar navbar-expand-lg shadow-sm fixed-top mb-5 bg-white">
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
                    <a class="nav-link" href="login.php">Login</a>
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


<!-- Hero Section with Floating Search Box -->
<section class="hero mt-5 pt-4">  
        <!-- Carousel Background Banner -->
        <div id="busGoCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner rounded-4 shadow" style="height: 480px;">
                <div class="carousel-item active h-100">
                    <img src="assets/images/4.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Bus Banner" style="filter: brightness(60%);">
                </div>
                <div class="carousel-item h-100">
                    <img src="assets/images/6.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Bus Travel" style="filter: brightness(60%);">
                </div>
                <div class="carousel-item h-100">
                    <img src="assets/images/7.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Bus Booking" style="filter: brightness(60%);">
                </div>
            </div>

            <!-- Carousel Controls (Optional) -->
            <button class="carousel-control-prev" type="button" data-bs-target="#busGoCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#busGoCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Search Box (Floating on top of carousel) -->
        <div class="position-absolute  start-50 translate-middle w-90" style="max-width: 1050px; z-index: 10; top: 75%;">
            <div class="search-box bg-white p-4 rounded-4 shadow-lg">
                <form action="search_bus.php" method="GET">
                    <div class="row g-3 align-items-end">
                        
                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold">From</label>
                            <select name="source" class="form-select" required>
                                <option value="">Select City</option>
                                <option value="Dhaka">Dhaka</option>
                                <option value="Chattogram">Chattogram</option>
                                <option value="Bogura">Bogura</option>
                                <option value="Rajshahi">Rajshahi</option>
                                <option value="Sylhet">Sylhet</option>
                                <option value="Khulna">Khulna</option>
                            </select>
                        </div>

                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold">To</label>
                            <select name="destination" class="form-select" required>
                                <option value="">Select City</option>
                                <option value="Dhaka">Dhaka</option>
                                <option value="Chattogram">Chattogram</option>
                                <option value="Bogura">Bogura</option>
                                <option value="Rajshahi">Rajshahi</option>
                                <option value="Sylhet">Sylhet</option>
                                <option value="Khulna">Khulna</option>
                            </select>
                        </div>

                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold">Journey Date</label>
                            <input type="date" name="journey_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Search Bus</button>
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
<script>
    
document.addEventListener('DOMContentLoaded', function () {

    const carouselElement = document.getElementById('busGoCarousel');

    if (carouselElement) {
        new bootstrap.Carousel(carouselElement, {
            interval: 4000,
            ride: 'carousel',
            pause: false,
            wrap: true,
            touch: true
        });
    }

});

</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
