<?php
session_start();
include "db.php";

// 1. Authorization: Only Admins (role_id = 1) can access
if (!isset($_SESSION['user']) || $_SESSION['role_id'] != 1) {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

$message = "";

// 2. Handle Delete Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM cars WHERE car_id = $id");
    $message = "Vehicle removed from inventory.";
}

// 3. Handle Add New Car
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $brand = $_POST['brand'] ?? '';
    $model = $_POST['model'] ?? '';
    $year = intval($_POST['year'] ?? 2024);
    $plate = strtoupper(trim($_POST['plate_number'] ?? ''));
    $rate = $_POST['daily_rate'] ?? 0;
    $desc = $_POST['description'] ?? '';
    $cat_name = $_POST['category_name'] ?? 'Sedan'; 

    $apiKey = '13e86d8037mshb59a70bbbd2d013p1585a3jsnb40044863f21';
    $apiHost = 'cars-by-api-ninjas.p.rapidapi.com';
    $clean_brand = str_replace("-Benz", "", $brand);
    $apiUrl = "https://cars-by-api-ninjas.p.rapidapi.com/v1/cars?make=" . urlencode($clean_brand) . "&model=" . urlencode($model) . "&year=" . $year;

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-RapidAPI-Key: $apiKey", "X-RapidAPI-Host: $apiHost"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $apiData = json_decode($response, true);
    curl_close($ch);

    $fuel = "Petrol"; $drive = "FWD"; $trans = "Automatic"; $cyl = 4; $disp = 2.0; 

    if (!empty($apiData) && isset($apiData[0])) {
        $spec = $apiData[0];
        $fuel = ucfirst($spec['fuel_type'] ?? 'Petrol');
        $drive = strtoupper($spec['drive'] ?? 'FWD');
        $trans = (isset($spec['transmission']) && $spec['transmission'] == 'm') ? 'Manual' : 'Automatic';
        $cyl = $spec['cylinders'] ?? 4;
        $disp = $spec['displacement'] ?? 2.0;
    }

    try {
        $sql = "INSERT INTO cars (brand, model, year, plate_number, daily_rate, status, fuel_type, drive_type, transmission, engine_cylinders, displacement, description, category_name, created_at) 
                VALUES (?, ?, ?, ?, ?, 'Available', ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisdsssdiss", $brand, $model, $year, $plate, $rate, $fuel, $drive, $trans, $cyl, $disp, $desc, $cat_name);
        
        if ($stmt->execute()) { 
            $message = "Success! $brand $model registered.";
        }
    } catch (mysqli_sql_exception $e) {
        $message = "Database Error: " . $e->getMessage();
    }
}

// 4. Fetch Inventory List
$all_cars = $conn->query("SELECT * FROM cars ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CarRide Admin | Fleet Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #f7a600; --bg-light: #f4f7f6; }
        body { font-family: 'Montserrat', sans-serif; background-color: var(--bg-light); }
        .admin-card { background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 25px; border:none; margin-bottom: 30px; }
        .btn-yellow { background: var(--primary); color: white; font-weight: 700; border: none; padding: 12px; border-radius: 10px; width: 100%; }
        .btn-yellow:hover { background: #d68e00; color: white; }
        .status-badge { font-size: 0.7rem; font-weight: 800; padding: 5px 12px; border-radius: 20px; text-transform: uppercase; }
        .badge-avail { background: #e6fcf5; color: #0ca678; border: 1px solid #0ca678; }
        .badge-rented { background: #fff9db; color: #f59f00; border: 1px solid #f59f00; }
        .spec-icon { color: var(--primary); margin-right: 5px; }
        .loader { display: none; font-size: 0.8rem; color: var(--primary); font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar bg-white border-bottom py-3 mb-4 sticky-top">
    <div class="container">
        <a class="fw-800 fs-4 text-dark text-decoration-none" href="dashboard.php">Car<span class="text-warning">Ride</span> Admin</a>
        <div class="d-flex gap-2">
            <a href="revenue.php" class="btn btn-outline-success rounded-pill px-4 btn-sm">
                <i class="bi bi-graph-up-arrow"></i> Revenue Dashboard
            </a>
            <a href="dashboard.php" class="btn btn-dark rounded-pill px-4 btn-sm">Exit Panel</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row">
        <!-- FORM COLUMN -->
        <div class="col-lg-4">
            <div class="admin-card">
                <h5 class="fw-bold mb-4">Register New Car</h5>
                <?php if ($message): ?> <div class="alert alert-info py-2 small fw-bold shadow-sm border-0"><?= $message ?></div> <?php endif; ?>

                <form method="POST">
                    <div class="mb-2">
                        <label class="small fw-bold">Year</label>
                        <select name="year" id="carYear" class="form-select form-select-sm">
                            <?php for($y=date('Y'); $y>=2015; $y--) echo "<option value='$y'>$y</option>"; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Brand</label>
                        <select name="brand" id="carBrand" class="form-select form-select-sm" required><option value="">Loading...</option></select>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Model <span id="loader" class="loader">Loading...</span></label>
                        <select name="model" id="carModel" class="form-select form-select-sm" required disabled><option value="">Select Brand</option></select>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Body Type</label>
                        <select name="category_name" class="form-select form-select-sm" required>
                            <option value="Sedan">Sedan</option>
                            <option value="SUV">SUV</option>
                            <option value="Hatchback">Hatchback</option>
                            <option value="Luxury">Luxury</option>
                            <option value="Sports Car">Sports Car</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Plate Number</label>
                        <input type="text" name="plate_number" class="form-control form-control-sm" placeholder="ABC 1234" required>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Price Per Day ($)</label>
                        <input type="number" name="daily_rate" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" name="add_car" class="btn-yellow">Verify & Save Vehicle</button>
                </form>
            </div>
        </div>

        <!-- TABLE COLUMN -->
        <div class="col-lg-8">
            <div class="admin-card p-0 overflow-hidden">
                <div class="p-4 border-bottom bg-light">
                    <h5 class="fw-bold m-0">Fleet Inventory (<?= $all_cars->num_rows ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 align-middle">
                        <thead class="table-dark small">
                            <tr>
                                <th class="ps-4">Vehicle Details</th>
                                <th>Technical Specs</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php while($row = $all_cars->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= htmlspecialchars($row['brand']) ?> <?= htmlspecialchars($row['model']) ?> (<?= $row['year'] ?>)</div>
                                    <div class="text-muted small">Plate: <?= $row['plate_number'] ?></div>
                                    <div class="fw-bold text-success">$<?= number_format($row['daily_rate'], 2) ?>/day</div>
                                    <span class="badge bg-light text-dark border mt-1"><?= $row['category_name'] ?></span>
                                </td>
                                <td>
                                    <span class="d-block small"><i class="bi bi-fuel-pump spec-icon"></i><?= $row['fuel_type'] ?></span>
                                    <span class="d-block small"><i class="bi bi-gear spec-icon"></i><?= $row['transmission'] ?></span>
                                    <span class="d-block small"><i class="bi bi-cpu spec-icon"></i><?= $row['drive_type'] ?></span>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'Available'): ?>
                                        <span class="status-badge badge-avail">Available</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-rented">Rented</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="admin.php?delete=<?= $row['car_id'] ?>" class="text-danger p-2" onclick="return confirm('Delete this vehicle?')">
                                        <i class="bi bi-trash fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// NHTSA API Logic for Brands and Models
const brandSelect = document.getElementById('carBrand');
const modelSelect = document.getElementById('carModel');
const yearSelect = document.getElementById('carYear');
const loader = document.getElementById('loader');

async function getBrands() {
    const res = await fetch('https://vpic.nhtsa.dot.gov/api/vehicles/GetMakesForVehicleType/car?format=json');
    const data = await res.json();
    const sorted = data.Results.sort((a,b) => a.MakeName.localeCompare(b.MakeName));
    brandSelect.innerHTML = '<option value="">Choose Brand...</option>';
    sorted.forEach(b => {
        let opt = document.createElement('option');
        opt.value = b.MakeName; opt.textContent = b.MakeName;
        brandSelect.appendChild(opt);
    });
}

brandSelect.addEventListener('change', async function() {
    const brand = this.value; const year = yearSelect.value;
    if(!brand) return;
    modelSelect.disabled = true; loader.style.display = 'inline';
    const res = await fetch(`https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMakeYear/make/${brand}/modelYear/${year}/vehicleType/car?format=json`);
    const data = await res.json();
    modelSelect.innerHTML = '<option value="">Choose Model...</option>';
    data.Results.forEach(m => {
        let opt = document.createElement('option');
        opt.value = m.Model_Name; opt.textContent = m.Model_Name;
        modelSelect.appendChild(opt);
    });
    modelSelect.disabled = false; loader.style.display = 'none';
});

getBrands();
</script>
</body>
</html>