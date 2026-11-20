<?php

// Get Search Data
$source = $_GET['source'] ?? '';
$destination = $_GET['destination'] ?? '';
$journey_date = $_GET['journey_date'] ?? '';

$bus_result = null;
$error = '';

// Check Search Data
if ($source == '' || $destination == '' || $journey_date == '') {

    $error = "Please select Source, Destination and Date.";

} elseif ($source == $destination) {

    $error = "Source and Destination cannot be the same.";

} elseif ($journey_date < date('Y-m-d')) {

    $error = "Please select a valid journey date.";

} else {

    // Escape Search Data
    // $source = $conn->real_escape_string($source);
    // $destination = $conn->real_escape_string($destination);
    // $journey_date = $conn->real_escape_string($journey_date);

    // Search Bus SQL
    $sql = "SELECT
                schedules.schedule_id,
                schedules.departure_date,
                schedules.departure_time,
                schedules.arrival_time,
                schedules.ticket_price,
                buses.bus_name,
                buses.bus_number,
                buses.bus_type,
                buses.total_seats,
                routes.source,
                routes.destination

            FROM schedules

            INNER JOIN routes
            ON schedules.route_id = routes.route_id

            INNER JOIN buses
            ON schedules.bus_id = buses.bus_id

            WHERE routes.source = '$source'
            AND routes.destination = '$destination'
            AND schedules.departure_date = '$journey_date'
            AND schedules.status = 'scheduled'
            AND buses.status = 'active'

            ORDER BY schedules.departure_time ASC";

    $bus_result = $conn->query($sql);
}
?>