<?php
session_start();
include "db.php";

// 1. Authorization Check
if (!isset($_SESSION['user']) || $_SESSION['role_id'] != 1) {
    die("Unauthorized access.");
}

// 2. Fetch Monthly Sales (Fixed for Strict SQL Mode)
$sales_query = $conn->query("
    SELECT 
        DATE_FORMAT(payment_date, '%M %Y') as month_name,
        SUM(amount) as total_revenue,
        COUNT(payment_id) as total_transactions
    FROM payments 
    GROUP BY month_name, DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY MIN(payment_date) DESC
");

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - CarRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: white; font-family: 'Montserrat', sans-serif; padding: 40px; }
        .report-header { border-bottom: 4px solid #f7a600; padding-bottom: 20px; margin-bottom: 40px; }
        .table thead { background-color: #212529; color: white; }
        .total-row { background-color: #f8f9fa; font-weight: bold; font-size: 1.1rem; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .container { max-width: 100%; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <!-- Header -->
        <div class="report-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-800 m-0">Car<span class="text-warning">Ride</span></h1>
                <h5 class="text-muted text-uppercase tracking-wider">Revenue Analysis Report</h5>
            </div>
            <div class="text-end">
                <p class="mb-0"><strong>Generated:</strong> <?= date('F d, Y') ?></p>
                <p class="mb-0"><strong>Admin:</strong> <?= htmlspecialchars($_SESSION['user']) ?></p>
            </div>
        </div>

        <!-- Instructions for user (Hidden when printing) -->
        <div class="no-print alert alert-info d-flex align-items-center shadow-sm border-0">
            <i class="bi bi-printer-fill fs-4 me-3"></i>
            <div>
                <strong>Print Preview Active:</strong> If the print dialog didn't appear, press <b>Ctrl + P</b>. 
                Choose <b>"Save as PDF"</b> as the destination to download the file.
                <br><a href="revenue.php" class="text-decoration-none fw-bold mt-1 d-inline-block">← Back to Revenue Dashboard</a>
            </div>
        </div>

        <!-- Sales Table -->
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th class="py-3 ps-3">Month / Year</th>
                    <th class="text-center py-3">Completed Bookings</th>
                    <th class="text-end py-3 pe-3">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sales_query && $sales_query->num_rows > 0): ?>
                    <?php while($sales = $sales_query->fetch_assoc()): 
                        $grand_total += $sales['total_revenue']; ?>
                    <tr>
                        <td class="ps-3 fw-600"><?= $sales['month_name'] ?></td>
                        <td class="text-center"><?= $sales['total_transactions'] ?></td>
                        <td class="text-end pe-3 fw-bold text-dark">$<?= number_format($sales['total_revenue'], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">No sales records available to generate report.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-end py-3">Grand Total Revenue:</td>
                    <td class="text-end pe-3 text-success">$<?= number_format($grand_total, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="mt-5 text-center pt-4 border-top">
            <p class="small text-muted mb-1">CarRide Fleet Management System &copy; <?= date('Y') ?></p>
            <p class="small text-muted font-monospace" style="font-size: 10px;">REF-ID: <?= md5(time()) ?></p>
        </div>
    </div>

</body>
</html>