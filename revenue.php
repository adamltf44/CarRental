<?php
session_start();
include "db.php";

// 1. Authorization
if (!isset($_SESSION['user']) || $_SESSION['role_id'] != 1) {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

// 2. Fetch Monthly Sales (Fixed for Strict Mode)
$sales_query = $conn->query("
    SELECT 
        DATE_FORMAT(payment_date, '%M %Y') as month_name,
        SUM(amount) as total_revenue,
        COUNT(payment_id) as total_transactions
    FROM payments 
    GROUP BY month_name, DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY MIN(payment_date) DESC
");

// 3. Overall Stats
$overall = $conn->query("SELECT SUM(amount) as total_rev, COUNT(payment_id) as total_count FROM payments")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CarRide | Revenue Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f4f7f6; }
        .stat-card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .revenue-table { background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
    </style>
</head>
<body>

<nav class="navbar bg-white border-bottom py-3 mb-4 sticky-top">
    <div class="container">
        <a class="fw-800 fs-4 text-dark text-decoration-none" href="admin.php">Car<span class="text-warning">Ride</span> Revenue</a>
        <div class="d-flex gap-2">
            <a href="admin.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm">Back to Fleet</a>
            <a href="generate_report.php" target="_blank" class="btn btn-danger rounded-pill px-4 btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF Report
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <!-- STATS CARDS -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card stat-card bg-dark text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold opacity-75">Lifetime Revenue</h6>
                        <h2 class="fw-800 mb-0 text-warning">$<?= number_format($overall['total_rev'], 2) ?></h2>
                    </div>
                    <i class="bi bi-cash-coin fs-1 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card bg-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold text-muted">Total Bookings</h6>
                        <h2 class="fw-800 mb-0"><?= $overall['total_count'] ?> Rentals</h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- MONTHLY TABLE -->
    <div class="revenue-table">
        <div class="p-4 border-bottom bg-light">
            <h5 class="fw-bold m-0">Monthly Revenue Analysis</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover m-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">Month / Year</th>
                        <th class="text-center">Total Bookings</th>
                        <th class="text-end pe-4">Monthly Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sales_query->num_rows > 0): ?>
                        <?php while($row = $sales_query->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= $row['month_name'] ?></td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border px-3"><?= $row['total_transactions'] ?> Rentals</span>
                            </td>
                            <td class="text-end pe-4 fw-bold text-success">
                                $<?= number_format($row['total_revenue'], 2) ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center py-5 text-muted">No financial records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>