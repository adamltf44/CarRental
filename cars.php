<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$search_brand = isset($_GET['search_brand']) ? $_GET['search_brand'] : '';
$sql = "SELECT * FROM cars WHERE status = 'Available'";
if (!empty($search_brand)) {
    $sql .= " AND brand LIKE '%" . $conn->real_escape_string($search_brand) . "%'";
}
$cars_list = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CarRide | Explore Fleet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #f7a600; --bg-dark: #111; }
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f9fa; }
        .navbar { background: #fff; border-bottom: 1px solid #eee; padding: 15px 0; }
        .car-card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); transition: 0.3s; background: #fff; }
        .car-card:hover { transform: translateY(-5px); }
        .btn-yellow { background: var(--primary); color: white; font-weight: 700; border-radius: 10px; border: none; }
        .spec-badge { background: #f1f1f1; color: #555; font-size: 0.7rem; padding: 4px 10px; border-radius: 5px; font-weight: 700; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="fw-800 fs-4 text-decoration-none text-dark" href="dashboard.php">Car<span style="color:var(--primary)">Ride</span></a>
        <div class="ms-auto">
            <a href="dashboard.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">Back to Home</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-800">Available <span style="color:var(--primary)">Fleet</span></h2>
        <form class="d-flex gap-2">
            <input type="text" name="search_brand" class="form-control rounded-pill px-3" placeholder="Search Brand..." value="<?= $search_brand ?>">
            <button class="btn btn-dark rounded-pill px-4">Search</button>
        </form>
    </div>

    <div class="row g-4">
        <?php while($car = $cars_list->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="car-card p-3">
                    <img src="https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&q=80&w=400" class="w-100 rounded-4 mb-3" alt="Car">
                    <h5 class="fw-bold mb-1"><?= $car['brand'] ?> <?= $car['model'] ?></h5>
                    <p class="text-primary fw-bold mb-3">$<?= number_format($car['daily_rate'], 0) ?> / day</p>
                    
                    <div id="api-specs-<?= $car['car_id'] ?>" class="mb-3 d-flex flex-wrap gap-1">
                        <!-- API DATA WILL LOAD HERE -->
                        <span class="spec-badge">Loading Specs...</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="rental.php?car_id=<?= $car['car_id'] ?>" class="btn btn-yellow py-2">Book This Car</a>
                    </div>
                </div>
            </div>

            <!-- JAVASCRIPT TO FETCH API DATA FOR EACH CAR -->
            <script>
                fetch(`https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMakeYear/make/<?= $car['brand'] ?>/modelYear/2023?format=json`)
                .then(response => response.json())
                .then(data => {
                    const specBox = document.getElementById('api-specs-<?= $car['car_id'] ?>');
                    // We simulate fetching specific engine/type details for the example
                    specBox.innerHTML = `
                        <span class="spec-badge"><i class="bi bi-fuel-pump"></i> Gasoline</span>
                        <span class="spec-badge"><i class="bi bi-gear"></i> Automatic</span>
                        <span class="spec-badge"><i class="bi bi-lightning"></i> 250 HP</span>
                    `;
                })
                .catch(err => {
                    document.getElementById('api-specs-<?= $car['car_id'] ?>').innerHTML = '<span class="spec-badge">Standard Model</span>';
                });
            </script>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>