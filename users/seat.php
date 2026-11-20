
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header('Location: index.php');
    exit;
    }
    if (!isset($_GET['schedule_id'])) {
    header('Location: home.php');
    exit;
}

$schedule_id = intval($_GET['schedule_id']);

$sql = "SELECT  
            schedules.schedule_id,
            schedules.departure_date,
            schedules.departure_time,
            schedules.arrival_time,
            schedules.ticket_price,
            buses.bus_id,
            buses.bus_name,
            buses.bus_type,
            buses.bus_number,
            buses.total_seats,
            routes.source,
            routes.destination
        FROM schedules
        INNER JOIN buses 
            ON schedules.bus_id = buses.bus_id
        INNER JOIN routes 
            ON schedules.route_id = routes.route_id
        WHERE schedules.schedule_id = $schedule_id";

$result = mysqli_query($conn, $sql);

$bus = mysqli_fetch_assoc($result);

if (!$bus) {
    die("Bus schedule not found!");
}

$price = (float)$bus['ticket_price'];
$bus_id = (int)$bus['bus_id'];

function getSeatRowLabel($row_index)
{
    $label = '';
    do {
        $label = chr(65 + ($row_index % 26)) . $label;
        $row_index = (int)floor($row_index / 26) - 1;
    } while ($row_index >= 0);

    return $label;
}

function loadBusSeats($conn, $bus_id)
{
    $statement = mysqli_prepare(
        $conn,
        "SELECT seat_id, seat_number FROM seats WHERE bus_id = ? ORDER BY seat_id ASC"
    );
    mysqli_stmt_bind_param($statement, 'i', $bus_id);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $seat_id, $seat_number);

    $seats = [];
    while (mysqli_stmt_fetch($statement)) {
        $seats[] = [
            'seat_id' => $seat_id,
            'seat_number' => $seat_number
        ];
    }

    mysqli_stmt_close($statement);
    return $seats;
}

$existing_seats = loadBusSeats($conn, $bus_id);
$all_seats_are_numeric = count($existing_seats) > 0;
foreach ($existing_seats as $seat) {
    if (!ctype_digit((string)$seat['seat_number'])) {
        $all_seats_are_numeric = false;
        break;
    }
}

if ((count($existing_seats) === 0 && (int)$bus['total_seats'] > 0) || $all_seats_are_numeric) {
    $transaction_started = false;

    try {
        mysqli_begin_transaction($conn);
        $transaction_started = true;

        $lock_stmt = mysqli_prepare($conn, "SELECT bus_id FROM buses WHERE bus_id = ? FOR UPDATE");
        mysqli_stmt_bind_param($lock_stmt, 'i', $bus_id);
        mysqli_stmt_execute($lock_stmt);
        mysqli_stmt_close($lock_stmt);

        $existing_seats = loadBusSeats($conn, $bus_id);
        $all_seats_are_numeric = count($existing_seats) > 0;
        foreach ($existing_seats as $seat) {
            if (!ctype_digit((string)$seat['seat_number'])) {
                $all_seats_are_numeric = false;
                break;
            }
        }

        if (count($existing_seats) === 0 && (int)$bus['total_seats'] > 0) {
            $insert_stmt = mysqli_prepare(
                $conn,
                "INSERT INTO seats (bus_id, seat_number, seat_type) VALUES (?, ?, ?)"
            );
            $seat_type = 'Standard';
            $seat_number = '';
            mysqli_stmt_bind_param($insert_stmt, 'iss', $bus_id, $seat_number, $seat_type);

            for ($index = 0; $index < (int)$bus['total_seats']; $index++) {
                $row_label = getSeatRowLabel((int)floor($index / 4));
                $seat_number = $row_label . (($index % 4) + 1);
                mysqli_stmt_execute($insert_stmt);
            }

            mysqli_stmt_close($insert_stmt);
        } elseif ($all_seats_are_numeric) {
            $update_stmt = mysqli_prepare(
                $conn,
                "UPDATE seats SET seat_number = ? WHERE seat_id = ?"
            );
            $seat_number = '';
            $seat_id = 0;
            mysqli_stmt_bind_param($update_stmt, 'si', $seat_number, $seat_id);

            foreach ($existing_seats as $index => $seat) {
                $row_label = getSeatRowLabel((int)floor($index / 4));
                $seat_number = $row_label . (($index % 4) + 1);
                $seat_id = (int)$seat['seat_id'];
                mysqli_stmt_execute($update_stmt);
            }

            mysqli_stmt_close($update_stmt);
        }

        mysqli_commit($conn);
        $transaction_started = false;
    } catch (mysqli_sql_exception $exception) {
        if ($transaction_started) {
            mysqli_rollback($conn);
        }
        error_log('Unable to initialize seats for bus ' . $bus_id . ': ' . $exception->getMessage());
        http_response_code(500);
        exit('Unable to load seats for this bus. Please try again later.');
    }
}

$booked_sql = "SELECT bs.seat_id
               FROM booking_seats bs
               INNER JOIN bookings b
               ON bs.booking_id = b.booking_id
               WHERE b.schedule_id = $schedule_id
               AND b.booking_status IN ('pending', 'confirmed')";

$booked_result = mysqli_query($conn, $booked_sql);

$booked_seats = [];

while ($row = mysqli_fetch_assoc($booked_result)) {
    $booked_seats[] = (int)$row['seat_id'];
}

$seats = loadBusSeats($conn, $bus_id);
?>
    
  


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Choose seats · Routeline Bus</title>
<link rel="stylesheet" href="../assets/css/seat.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@500;700;800&family=Instrument+Sans:wght@400;500;600&display=swap">

<body>
<header><div class="bar"><div class="brand"><i>🚌</i>Routeline</div><div class="steps">Step 1 of 3 · Choose seats</div></div></header>

<main class="wrap">
  <div class="head">
    <a class="back" href="home.html">← Back to results</a>
    <h1>Choose your seats</h1>
    <div class="muted">
         <?php echo htmlspecialchars($bus['bus_name']); ?>
    ·
    <?php echo htmlspecialchars($bus['bus_type']); ?>
    ·
    <?php echo date('D, d M', strtotime($bus['departure_time'])); ?>
    ·
    <?php echo date('h:i A', strtotime($bus['departure_time'])); ?>
    </div>
  </div>

  <section class="card" aria-label="Seat map">
    <div class="legend">
      <span><i class="sw"></i>Available</span><span><i class="sw s"></i>Your pick</span><span><i class="sw t"></i>Booked</span>
    </div>
    <div class="bus">
      <div class="front"><span>Front</span><span class="wheel" aria-hidden="true">☸</span></div>
      <div id="map"></div>
      <?php if (empty($seats)): ?>
        <p class="muted">No seats are configured for this bus. Please contact the bus operator.</p>
      <?php endif; ?>
    </div>
  </section>

  <aside class="card sum" aria-live="polite">
    <div id="form">
      <h2>Your trip</h2>
      <div class="rt">
         <?php echo htmlspecialchars($bus['source']); ?>
    →
    <?php echo htmlspecialchars($bus['destination']); ?>
      </div>
      <div class="muted">
         Departure:
    <?php echo date('h:i A', strtotime($bus['departure_time'])); ?>
      </div>
      <ul class="list" id="ls"></ul>
      <div class="tot"><span>Total</span><b id="tt">৳ 0</b></div>
      <p class="hint" id="ht">Select up to 4 seats.</p>
      <button class="go" id="go" type="button" disabled>Continue to passenger details</button>
    </div>
    <div class="done" id="dn"><div class="tick">✓</div><h2>Seats held for 10 minutes</h2><p class="muted" id="dm"></p></div>
  </aside>
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>


$(document).ready(function () {

    let PRICE = <?php echo $price; ?>;
    let MAX = 4;
    let picked = [];

    // Database থেকে Seats
    let seats = <?php echo json_encode($seats, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    // Database থেকে Booked Seats
    let bookedSeats = <?php echo json_encode($booked_seats); ?>;

    let html = '';

    // Generate Seats
    $.each(seats, function (index, seat) {

        if (index % 4 === 0) {
            html += '<div class="row">';
        }

        let isBooked = bookedSeats.includes(Number(seat.seat_id));
        let rowLabel = String(seat.seat_number).replace(/[0-9]+$/, '');

        html += `
            <button type="button"
                class="seat ${isBooked ? 'booked' : ''}"
                data-id="${seat.seat_id}"
                data-seat="${seat.seat_number}"
                ${isBooked ? 'disabled' : ''}>
                ${seat.seat_number}
            </button>
        `;

        if (index % 4 === 1) {
            html += `<span class="lbl">${rowLabel}</span>`;
        }

        if (index % 4 === 3 || index === seats.length - 1) {
            html += '</div>';
        }

    });

    $('#map').html(html);


    // Seat Select / Unselect
    $('#map').on('click', '.seat:not(:disabled)', function () {

        let seatID = String($(this).data('id'));
        let seatNumber = $(this).data('seat');

        let index = picked.findIndex(function (seat) {
            return seat.id === seatID;
        });

        if (index !== -1) {

            picked.splice(index, 1);
            $(this).removeClass('sel');

        } else {

            if (picked.length >= MAX) {
                $('#ht').text('You can select maximum 4 seats.');
                return;
            }

            picked.push({
                id: seatID,
                number: seatNumber
            });

            $(this).addClass('sel');

        }

        render();

    });


    // Update Booking Summary
    function render() {

        let list = '';

        $.each(picked, function (index, seat) {

            list += `
                <li>
                    <span>Seat ${seat.number}</span>
                    <span>৳ ${PRICE.toLocaleString()}</span>
                </li>
            `;

        });

        $('#ls').html(list);

        let total = picked.length * PRICE;

        $('#tt').text('৳ ' + total.toLocaleString());

        $('#go').prop('disabled', picked.length === 0);

        if (picked.length > 0) {

            $('#ht').text(
                picked.length + ' of ' + MAX + ' seats selected.'
            );

        } else {

            $('#ht').text('Select up to 4 seats.');

        }

    }

    render();


    // Continue Button
    $('#go').click(function () {

        if (picked.length === 0) {
            return;
        }

        let seatIDs = picked.map(function (seat) {
            return seat.id;
        });

        let form = $('<form>', {
            method: 'POST',
            action: 'passenger_details.php'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: 'schedule_id',
            value: <?php echo $schedule_id; ?>
        }));

        $.each(seatIDs, function (index, id) {

            form.append($('<input>', {
                type: 'hidden',
                name: 'seat_ids[]',
                value: id
            }));

        });

        $('body').append(form);

        form.submit();

    });

});


</script>
</body>
</html>
