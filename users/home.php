<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
  header('Location: index.php');
  exit;
}

$source_sql = "SELECT DISTINCT source FROM routes ORDER BY source ASC";
$source_result = mysqli_query($conn, $source_sql);



$destination_sql = "SELECT DISTINCT destination FROM routes ORDER BY destination ASC";
$destination_result = mysqli_query($conn, $destination_sql);

$popular_sql = "SELECT route_id, source, destination, distance
                FROM routes
                WHERE status = 'active'
                AND is_popular = 1
                ORDER BY route_id ASC";

$popular_result = mysqli_query($conn, $popular_sql);

include 'search_bus.php';


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Home · Routeline Bus</title>
  <?php include '../includes/admin_css.php' ?>
  <link rel="stylesheet" href="../assets/css/user_home.css">
  <link rel="stylesheet" href="../assets/css/customer_topbar.css">

</head>

<body>
  <header>
    <?php include 'customer_topbar.php'; ?>
    <div class="hero">
      <h1>Where to next, <?php echo strtoupper($_SESSION['name']); ?>?</h1>
      <p>Pick a route and date to see buses with open seats.</p>
    </div>
  </header>

  <main>
    <form class="card search" id="s" action="" method="get">
      <div>
        <label for="from">From</label>
        <select id="from" name="source" required>
          <option value="">Select Source</option>
          <?php
          while ($row = mysqli_fetch_assoc($source_result)) { ?>

            <option value="<?php echo $row['source']; ?>">
              <?php echo $row['source']; ?>
            </option>
          <?php }; ?>
        </select>
      </div>
      <button class="swap" type="button" id="sw" aria-label="Swap From and To">⇄</button>
      <div>
        <label for="to">To</label>
        <select name="destination" id="to" required>
          <option value="">Select Destination</option>
          <?php while ($row = mysqli_fetch_assoc($destination_result)) { ?>
            <option value="<?php echo $row['destination']; ?>">
              <?php echo $row['destination']; ?>
            </option>
          <?php }; ?>
        </select>
      </div>
      <div>
        <label for="d">Date</label><input type="date" id="d" name="journey_date" required>
      </div>
      <button class="go" type="submit">Find buses</button>
      <div class="err" id="er" role="alert" style="grid-column:1/-1"></div>
    </form>

    <div id="res" aria-live="polite">

      <?php if ($bus_result !== null) { ?>

        <h2 class="mt-4 mb-3">Available Buses</h2>

        <?php if (mysqli_num_rows($bus_result) > 0) { ?>

          <?php while ($bus = mysqli_fetch_assoc($bus_result)) { ?>

            <div class="card bus">
              <div class="bus-info">
                <h2><?php echo ($bus['bus_name']); ?></h2>
                <h6>
                  Bus Number:
                  <?php echo ($bus['bus_number']); ?>
                </h6>
                <h6><?php echo ($bus['bus_type']); ?></h6>
              </div>

              <div class="bus-route">
                <h3>
                  <?php echo ($bus['source']); ?>
                  →
                  <?php echo ($bus['destination']); ?>
                </h3>
                <h5>

                  <?php echo date('h:i A', strtotime($bus['departure_time'])); ?> to <?php echo date('h:i A', strtotime($bus['arrival_time'])); ?>
                </h5>
                <p>

                </p>
              </div>

              <div class="bus-action">
                <h3>৳ <?php echo number_format($bus['ticket_price'], 2); ?></h3>
                <p>
                  Total Seats:
                  <?php echo $bus['total_seats']; ?>
                </p>
                <a href="seat.php?schedule_id=<?php echo $bus['schedule_id']; ?>"
                  class="btn btn-primary">
                  Book Seat
                </a>
              </div>
            </div>

          <?php } ?>

        <?php } else { ?>

          <div class="alert alert-warning">
            No buses found for your selected route and date.
          </div>

        <?php } ?>

      <?php } ?>

    </div>

    </div>

    <section class="py-5" id="routes">

      <div class="container">

        <div class="text-center mb-4">
          <h2 class="section-title">Popular Routes</h2>
          <p class="text-muted">Explore popular destinations</p>
        </div>

        <div class="row g-4">


          <?php while ($route = mysqli_fetch_assoc($popular_result)) { ?>




            <div class="col-md-4">
              <div class="card route-card shadow-sm">
                <div class="card-body">
                  <h5>
                    <?php echo ($route['source']); ?>
                    →
                    <?php echo ($route['destination']); ?>
                  </h5>
                  <p class="text-muted">
                    Distance:
                    <?php echo $route['distance']; ?> KM
                  </p>

                </div>
              </div>


            </div>
          <?php   }; ?>




        </div>

      </div>

    </section>









    <?php
    include '../includes/admin_script.php';
    ?>
    <script>
      $(document).ready(function() {

        let today = new Date();

        let year = today.getFullYear();
        let month = String(today.getMonth() + 1).padStart(2, '0');
        let day = String(today.getDate()).padStart(2, '0');

        let todayDate = `${year}-${month}-${day}`;

        $('#d').attr('min', todayDate);
        $('#d').val(todayDate);

      });
    </script>
</body>

</html>