<?php
session_start();
include 'config/db.php';

$routeOptions = $conn->query(
    "SELECT source, destination
     FROM routes
     WHERE status = 'active'
     ORDER BY source, destination"
);
$cities = [];
while ($route = $routeOptions->fetch_assoc()) {
    $cities[] = $route['source'];
    $cities[] = $route['destination'];
}
$cities = array_unique($cities);
sort($cities, SORT_NATURAL | SORT_FLAG_CASE);

$source = isset($_GET['source']) && is_string($_GET['source']) ? trim($_GET['source']) : '';
$destination = isset($_GET['destination']) && is_string($_GET['destination']) ? trim($_GET['destination']) : '';
$journeyDate = isset($_GET['journey_date']) && is_string($_GET['journey_date']) ? trim($_GET['journey_date']) : '';
$searchSubmitted = isset($_GET['source']) || isset($_GET['destination']) || isset($_GET['journey_date']);
$searchError = '';
$searchBuses = [];

if ($searchSubmitted) {
    $date = DateTime::createFromFormat('!Y-m-d', $journeyDate);
    $validDate = $date && $date->format('Y-m-d') === $journeyDate;

    if ($source === '' || $destination === '' || $journeyDate === '') {
        $searchError = 'Please select a source, destination, and journey date.';
    } elseif ($source === $destination) {
        $searchError = 'Source and destination cannot be the same.';
    } elseif (!$validDate || $journeyDate < date('Y-m-d')) {
        $searchError = 'Please select a valid journey date.';
    } else {
        $searchStatement = $conn->prepare(
            "SELECT schedules.schedule_id, schedules.departure_time,
                    schedules.arrival_time, schedules.ticket_price,
                    buses.bus_name, buses.bus_number, buses.bus_type,
                    buses.total_seats, routes.source, routes.destination
             FROM schedules
             INNER JOIN routes ON schedules.route_id = routes.route_id
             INNER JOIN buses ON schedules.bus_id = buses.bus_id
             WHERE routes.source = ?
               AND routes.destination = ?
               AND schedules.departure_date = ?
               AND schedules.status = 'scheduled'
               AND routes.status = 'active'
               AND buses.status = 'active'
             ORDER BY schedules.departure_time ASC"
        );
        if (!$searchStatement) {
            $searchError = 'Bus search is temporarily unavailable.';
        } else {
            $searchStatement->bind_param('sss', $source, $destination, $journeyDate);
            $searchStatement->execute();
            $searchResult = $searchStatement->get_result();
            while ($bus = $searchResult->fetch_assoc()) {
                $searchBuses[] = $bus;
            }
        }
    }
}

$sql = "SELECT * FROM routes WHERE is_popular = 1";

$result = mysqli_query($conn, $sql);



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Online Bus Ticket Booking</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <script src="index.js"></script>

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
                <form action="index.php#search-results" method="GET">
                    <div class="row g-3 align-items-end">
                        
                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold">From</label>
                            <select name="source" class="form-select" required>
                                <option value="">Select City</option>
                                <?php foreach ($cities as $city) { ?>
                                    <option value="<?php echo $city; ?>"
                                        <?php echo $source === $city ? 'selected' : ''; ?>>
                                        <?php echo $city; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold">To</label>
                            <select name="destination" class="form-select" required>
                                <option value="">Select City</option>
                                <?php foreach ($cities as $city) { ?>
                                    <option value="<?php echo $city; ?>"
                                        <?php echo $destination === $city ? 'selected' : ''; ?>>
                                        <?php echo $city; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold">Journey Date</label>
                            <input type="date" name="journey_date" class="form-control" required
                                   min="<?php echo date('Y-m-d'); ?>"
                                   value="<?php echo $journeyDate; ?>">
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Search Bus</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>


</section>

<?php if ($searchSubmitted) { ?>
    <section class="container bus-results py-4" id="search-results">
        <h2 class="bus-results-title mb-3">Available Buses</h2>
        <?php if ($searchError !== '') { ?>
            <div class="alert alert-warning" role="alert">
                <?php echo $searchError; ?>
            </div>
        <?php } elseif (!$searchBuses) { ?>
            <div class="alert alert-info" role="status">No buses found for this route and date.</div>
        <?php } else { ?>
            <div class="bus-list">
                <?php foreach ($searchBuses as $bus) { ?>
                    <article class="bus-card">
                        <div class="bus-card-info">
                            <h3><?php echo $bus['bus_name']; ?></h3>
                            <p>Bus Number: <?php echo $bus['bus_number']; ?></p>
                            <p><?php echo $bus['bus_type']; ?></p>
                        </div>
                        <div class="bus-card-route">
                            <h3>
                                <?php echo $bus['source']; ?>
                                → <?php echo $bus['destination']; ?>
                            </h3>
                            <p>
                                <?php echo date('h:i A', strtotime($bus['departure_time'])); ?>
                                to <?php echo date('h:i A', strtotime($bus['arrival_time'])); ?>
                            </p>
                        </div>
                        <div class="bus-card-action">
                            <div class="bus-price">৳ <?php echo number_format((float)$bus['ticket_price'], 2); ?></div>
                            <p>Total Seats: <?php echo $bus['total_seats']; ?></p>
                            <a class="btn btn-primary" href="login.php">Book Seat</a>
                        </div>
                    </article>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
<?php } ?>



<!-- Popular Routes -->
<section class="py-5" id="routes">

    <div class="container">

        <div class="text-center mb-4">
            <h2 class="section-title">Popular Routes</h2>
            <p class="text-muted">Explore popular destinations</p>
        </div>

        <div class="row g-4">

        <?php 
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

          
        
        
        ?>

            <div class="col-md-4">
                <div class="card route-card shadow-sm">
                    <div class="card-body">
                        <h5>🚌 
                        <?php echo $row['source']; ?>
                                    →
                                    <?php echo$row['destination']; ?>

                        </h5>
                        <p class="text-muted">
                            Distance:
                                    <?php echo $row['distance']; ?> KM
                        </p>
                        <a href="login.php" class="btn btn-outline-primary">
                            View Buses
                        </a>
                    </div>
                </div>

               
            </div>
 <?php   }}; ?>
          

            

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
