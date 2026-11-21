<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company_admin') {
    header('Location: ../login.php');
    exit;
}

$company_id = (int)$_SESSION['company_id'];
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$bus_id = isset($_GET['bus_id']) ? (int)$_GET['bus_id'] : 0;

$company_result = $conn->query("SELECT company_name FROM bus_companies WHERE company_id = $company_id");
$company_name = $company_result ? ($company_result->fetch_assoc()['company_name'] ?? '') : '';

$bus_result = $conn->query("SELECT bus_id, bus_name, bus_number FROM buses WHERE company_id = $company_id ORDER BY bus_name, bus_number");

$conditions = [
    "buses.company_id = $company_id",
    "payments.payment_status = 'paid'"
];

if ($bus_id > 0) {
    $conditions[] = "buses.bus_id = $bus_id";
}
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) {
    $conditions[] = "DATE(payments.payment_date) >= '" . $conn->real_escape_string($date_from) . "'";
}
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
    $conditions[] = "DATE(payments.payment_date) <= '" . $conn->real_escape_string($date_to) . "'";
}

$where = implode(' AND ', $conditions);
$sql = "
    SELECT buses.bus_id,
           buses.bus_name,
           buses.bus_number,
           buses.bus_type,
           COUNT(payments.payment_id) AS paid_payments,
           COALESCE(SUM(payments.amount), 0) AS total_income
    FROM buses
    LEFT JOIN schedules ON schedules.bus_id = buses.bus_id
    LEFT JOIN bookings ON bookings.schedule_id = schedules.schedule_id
    LEFT JOIN payments ON payments.booking_id = bookings.booking_id
        AND payments.payment_status = 'paid'
        AND bookings.booking_status != 'cancelled'
    WHERE $where
    GROUP BY buses.bus_id, buses.bus_name, buses.bus_number, buses.bus_type
    ORDER BY total_income DESC, buses.bus_name, buses.bus_number
";

$report = $conn->query($sql);
$grand_total = 0.0;
$rows = [];
if ($report) {
    while ($row = $report->fetch_assoc()) {
        $rows[] = $row;
        $grand_total += (float)$row['total_income'];
    }
}

$cancellation_conditions = ["buses.company_id = $company_id"];
if ($bus_id > 0) {
    $cancellation_conditions[] = "buses.bus_id = $bus_id";
}
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) {
    $cancellation_conditions[] = "DATE(cancellations.requested_at) >= '" . $conn->real_escape_string($date_from) . "'";
}
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
    $cancellation_conditions[] = "DATE(cancellations.requested_at) <= '" . $conn->real_escape_string($date_to) . "'";
}
$cancellation_where = implode(' AND ', $cancellation_conditions);

$cancellation_sql = "
    SELECT cancellations.cancellation_id,
           cancellations.booking_id,
           users.name AS customer_name,
           buses.bus_name,
           buses.bus_number,
           cancellations.reason,
           cancellations.cancellation_charge,
           cancellations.refund_amount,
           cancellations.cancellation_status,
           cancellations.requested_at
    FROM cancellations
    INNER JOIN bookings ON cancellations.booking_id = bookings.booking_id
    INNER JOIN users ON cancellations.user_id = users.user_id
    INNER JOIN schedules ON bookings.schedule_id = schedules.schedule_id
    INNER JOIN buses ON schedules.bus_id = buses.bus_id
    WHERE $cancellation_where
    ORDER BY cancellations.requested_at DESC
";
$cancellation_result = $conn->query($cancellation_sql);
$cancellation_rows = [];
$total_cancellation_charge = 0.0;
$total_refund = 0.0;
if ($cancellation_result) {
    while ($row = $cancellation_result->fetch_assoc()) {
        $cancellation_rows[] = $row;
        $total_cancellation_charge += (float)$row['cancellation_charge'];
        $total_refund += (float)$row['refund_amount'];
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bus Income Report | BusGo</title>
    <?php include '../includes/admin_css.php'; ?>
    <style>
        .report-filter { display: flex; align-items: end; gap: 12px; flex-wrap: wrap; }
        .report-filter .form-control, .report-filter .form-select { min-width: 170px; }
        .report-total { font-size: 1.25rem; font-weight: 700; color: #198754; }
        .print-header { display: none; }
        @media print {
            body { background: #fff; }
            .topbar, .sidebar, .no-print, .page-header { display: none !important; }
            .layout { margin: 0; min-height: auto; }
            .layout > * { margin: 0 !important; }
            main { width: 100%; padding: 0 !important; }
            .company-card { box-shadow: none; border: 1px solid #ddd; }
            .print-header { display: block; text-align: center; margin-bottom: 18px; }
            .print-header h2, .print-header p { margin: 2px 0; }
        }
    </style>
</head>
<body>
<?php include '../includes/topbar.php'; ?>
<div class="layout">
    <?php include '../includes/cmsidebar.php'; ?>
    <main class="container-fluid py-4 px-4">
        <div class="page-header">
            <div><h3>Bus Income Report</h3><p>Paid income shown separately for each bus.</p></div>
        </div>

        <div class="print-header">
            <h2><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><strong>Bus Income Report</strong></p>
            <?php if ($date_from || $date_to) { ?>
                <p>Period: <?php echo htmlspecialchars($date_from ?: 'Start', ENT_QUOTES, 'UTF-8'); ?> to <?php echo htmlspecialchars($date_to ?: 'Today', ENT_QUOTES, 'UTF-8'); ?></p>
            <?php } ?>
        </div>

        <div class="company-card p-3 mb-3 no-print">
            <form method="get" class="report-filter">
                <div>
                    <label class="form-label" for="bus_id">Bus</label>
                    <select class="form-select" id="bus_id" name="bus_id">
                        <option value="0">All buses</option>
                        <?php while ($bus = $bus_result->fetch_assoc()) { ?>
                            <option value="<?php echo (int)$bus['bus_id']; ?>" <?php echo $bus_id === (int)$bus['bus_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bus['bus_name'] . ' (' . $bus['bus_number'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="date_from">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label class="form-label" for="date_to">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a class="btn btn-outline-secondary" href="reports.php">Reset</a>
                <button type="button" class="btn btn-success" onclick="window.print()"><i class="bi bi-printer"></i> Print Report</button>
            </form>
        </div>

        <div class="company-card">
            <div class="table-header">
                <h5>Income by Bus</h5>
                <span class="report-total">Total: ৳ <?php echo number_format($grand_total, 2); ?></span>
            </div>
            <div class="table-responsive">
                <table class="table company-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bus Name</th>
                            <th>Bus Number</th>
                            <th>Bus Type</th>
                            <th>Paid Payments</th>
                            <th>Income</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($rows) > 0) { ?>
                            <?php foreach ($rows as $index => $row) { ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo ($row['bus_name']); ?></td>
                                    <td><?php echo ($row['bus_number']); ?></td>
                                    <td><?php echo ($row['bus_type']); ?></td>
                                    <td><?php echo $row['paid_payments']; ?></td>
                                    <td><strong>৳ <?php echo number_format((float)$row['total_income'], 2); ?></strong></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="6" class="text-center">No paid income found.</td></tr>
                        <?php } ?>
                    </tbody>
                    <?php if (count($rows) > 0) { ?>
                        <tfoot>
                            <tr><th colspan="5" class="text-end">Grand Total</th><th>৳ <?php echo number_format($grand_total, 2); ?></th></tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>

        <div class="row g-3 my-1">
            <div class="col-md-6">
                <div class="company-card p-3">
                    <h6>Cancellation Charge</h6>
                    <strong class="text-danger">৳ <?php echo number_format($total_cancellation_charge, 2); ?></strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="company-card p-3">
                    <h6>Total Refund</h6>
                    <strong class="text-warning">৳ <?php echo number_format($total_refund, 2); ?></strong>
                </div>
            </div>
        </div>

        <div class="company-card mt-3">
            <div class="table-header">
                <h5>Passenger Cancellations</h5>
                <span><?php echo count($cancellation_rows); ?> cancellation(s)</span>
            </div>
            <div class="table-responsive">
                <table class="table company-table">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th>Bus</th>
                            <th>Reason</th>
                            <th>Charge</th>
                            <th>Refund</th>
                            <th>Status</th>
                            <th>Cancelled At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($cancellation_rows) > 0) { ?>
                            <?php foreach ($cancellation_rows as $cancellation) { ?>
                                <tr>
                                    <td>#<?php echo (int)$cancellation['booking_id']; ?></td>
                                    <td><?php echo ($cancellation['customer_name']); ?></td>
                                    <td><?php echo ($cancellation['bus_name'] . ' (' . $cancellation['bus_number'] . ')'); ?></td>
                                    <td><?php echo ($cancellation['reason'] ?: 'No reason', ); ?></td>
                                    <td>৳ <?php echo number_format((float)$cancellation['cancellation_charge'], 2); ?></td>
                                    <td>৳ <?php echo number_format((float)$cancellation['refund_amount'], 2); ?></td>
                                    <td><?php echo ($cancellation['cancellation_status']); ?></td>
                                    <td><?php echo ($cancellation['requested_at']); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="8" class="text-center">No cancellations found.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/admin_script.php'; ?>
</body>
</html>
