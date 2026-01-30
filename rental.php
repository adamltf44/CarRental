<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$user_name = $_SESSION['user'];
$show_receipt = false; // Toggle variable

// 1. Get the current user's ID
$user_query = $conn->prepare("SELECT user_id FROM users WHERE full_name = ?");
$user_query->bind_param("s", $user_name);
$user_query->execute();
$user_data = $user_query->get_result()->fetch_assoc();
$user_id = $user_data['user_id'];

// --- STEP 1: Handling "Review Rental" (Calculate and show receipt) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_rental'])) {
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $pay_method = $_POST['payment_method'];

    // Fetch car details for the receipt
    $car_query = $conn->prepare("SELECT brand, model, daily_rate FROM cars WHERE car_id = ?");
    $car_query->bind_param("i", $car_id);
    $car_query->execute();
    $car_info = $car_query->get_result()->fetch_assoc();

    $date1 = new DateTime($start_date);
    $date2 = new DateTime($end_date);
    $interval = $date1->diff($date2);
    $total_days = $interval->days;

    if ($total_days <= 0) {
        $message = "Error: End date must be after start date.";
    } else {
        $total_price = $total_days * $car_info['daily_rate'];
        $show_receipt = true; // This triggers the receipt view in HTML
    }
}

// --- STEP 2: Handling "Final Confirmation" (Insert into DB) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $total_days = $_POST['total_days'];
    $total_price = $_POST['total_price'];
    $pay_method = $_POST['payment_method'];
    
    // 1. Insert into RENTALS
    $rental_sql = "INSERT INTO rentals (user_id, car_id, start_date, end_date, total_days, total_price, rental_status, created_at, modified, status) 
                   VALUES (?, ?, ?, ?, ?, ?, 'Active', NOW(), NOW(), 1)";
    $stmt = $conn->prepare($rental_sql);
    $stmt->bind_param("iissid", $user_id, $car_id, $start_date, $end_date, $total_days, $total_price);

    if ($stmt->execute()) {
        $rental_id = $stmt->insert_id;

        // 2. Insert into PAYMENTS (Using your exact column names)
        $pay_sql = "INSERT INTO payments (rental_id, user_id, amount, payment_methods, payment_status, payment_date, created_at, modified, status) 
                    VALUES (?, ?, ?, ?, 'Completed', NOW(), NOW(), NOW(), 1)";
        $pay_stmt = $conn->prepare($pay_sql);
        $pay_stmt->bind_param("iids", $rental_id, $user_id, $total_price, $pay_method);
        $pay_stmt->execute();

        // 3. Update car status
        $update_sql = "UPDATE cars SET status = 'Rented' WHERE car_id = ?";
        $upd_stmt = $conn->prepare($update_sql);
        $upd_stmt->bind_param("i", $car_id);
        $upd_stmt->execute();

        $message = "Rental Successful! Your booking is confirmed.";
        $show_receipt = false;
    } else {
        $message = "Database Error: " . $conn->error;
    }
}

// Fetch Available cars for the initial form
$cars_list = $conn->query("SELECT * FROM cars WHERE status = 'Available'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CarRide - Rental Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .main-card { border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .receipt-header { border-bottom: 2px dashed #dee2e6; padding-bottom: 15px; }
        .btn-yellow { background: #f7a600; color: #fff; font-weight: 600; }
        .btn-yellow:hover { background: #d68e00; color: #fff; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <?php if ($message): ?>
                <div class="alert alert-success text-center mb-4 border-0 shadow-sm">
                    <i class="bi bi-check-circle-fill"></i> <?= $message ?>
                    <br><a href="dashboard.php" class="btn btn-sm btn-dark mt-2">Go to Dashboard</a>
                </div>
            <?php endif; ?>

            <!-- VIEW 1: RENTAL FORM -->
            <?php if (!$show_receipt && !$message): ?>
            <div class="card main-card p-4">
                <h4 class="fw-bold mb-3">Book a Rental</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Car</label>
                        <select name="car_id" class="form-select" required>
                            <option value="">Choose a vehicle...</option>
                            <?php while($car = $cars_list->fetch_assoc()): ?>
                                <option value="<?= $car['car_id'] ?>"><?= $car['brand'] ?> <?= $car['model'] ?> ($<?= $car['daily_rate'] ?>/day)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="small fw-bold">Pick-up</label><input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>"></div>
                        <div class="col-6 mb-3"><label class="small fw-bold">Return</label><input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>"></div>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Cash">Cash at Counter</option>
                        </select>
                    </div>
                    <button type="submit" name="review_rental" class="btn btn-yellow w-100 py-2">Review Summary</button>
                </form>
            </div>
            <?php endif; ?>

            <!-- VIEW 2: RECEIPT SUMMARY -->
            <?php if ($show_receipt): ?>
            <div class="card main-card p-4 shadow-lg">
                <div class="receipt-header text-center mb-4">
                    <h3 class="fw-bold"><i class="bi bi-receipt"></i> Rental Receipt</h3>
                    <p class="text-muted small">Please confirm your booking details</p>
                </div>
                
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">Vehicle:</span>
                    <span class="fw-bold"><?= $car_info['brand'] ?> <?= $car_info['model'] ?></span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">Duration:</span>
                    <span><?= $total_days ?> Days</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">Dates:</span>
                    <span class="small"><?= $start_date ?> to <?= $end_date ?></span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">Method:</span>
                    <span class="badge bg-light text-dark border"><?= $pay_method ?></span>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold">Total Amount:</h5>
                    <h4 class="mb-0 text-success fw-bold">$<?= number_format($total_price, 2) ?></h4>
                </div>

                <form method="POST">
                    <!-- Hidden inputs to pass data to the confirmation step -->
                    <input type="hidden" name="car_id" value="<?= $car_id ?>">
                    <input type="hidden" name="start_date" value="<?= $start_date ?>">
                    <input type="hidden" name="end_date" value="<?= $end_date ?>">
                    <input type="hidden" name="total_days" value="<?= $total_days ?>">
                    <input type="hidden" name="total_price" value="<?= $total_price ?>">
                    <input type="hidden" name="payment_method" value="<?= $pay_method ?>">

                    <button type="submit" name="confirm_payment" class="btn btn-success w-100 py-2 mb-2">
                        <i class="bi bi-check-lg"></i> Confirm & Pay Now
                    </button>
                    <a href="rental.php" class="btn btn-outline-secondary w-100 btn-sm">Cancel</a>
                </form>
            </div>
            <?php endif; ?>

            <p class="text-center mt-4 small text-muted">Logged in as <?= htmlspecialchars($user_name) ?></p>
        </div>
    </div>
</div>

</body>
</html>