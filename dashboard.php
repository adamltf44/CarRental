<?php
session_start();
include "db.php"; 

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// --- SEARCH LOGIC ---
$search_brand = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : '';

$query = "SELECT * FROM cars WHERE status = 'Available'";
if (!empty($search_brand)) {
    $query .= " AND brand LIKE '%$search_brand%'";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarRide | Adventure Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #f7a600;
            --primary-hover: #d68e00;
            --bg-dark: #111;
            --text-muted: #888;
            --input-bg: #f8f9fa;
        }

        body { 
            font-family: 'Montserrat', sans-serif; 
            background-color: #fff; 
            color: #111;
            scroll-behavior: smooth;
        }

        .navbar { background: #fff; padding: 20px 0; border-bottom: 1px solid #eee; }
        .brand-name { font-weight: 800; font-size: 1.5rem; color: #111; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .brand-name span { color: var(--primary); }
        .nav-link { font-weight: 700; color: #333 !important; font-size: 0.9rem; margin: 0 15px; transition: 0.3s; }
        .nav-link:hover { color: var(--primary) !important; }

        .hero-section { position: relative; background: #fff; height: 75vh; display: flex; align-items: center; overflow: hidden; }
        .hero-section::after { content: ''; position: absolute; top: 0; right: 0; width: 50%; height: 100%; background: var(--primary); clip-path: polygon(25% 0, 100% 0, 100% 100%, 0% 100%); z-index: 1; }
        .hero-content { position: relative; z-index: 3; }
        .hero-title { font-size: 4rem; font-weight: 800; line-height: 1.1; margin-bottom: 25px; }
        .hero-title span { color: var(--primary); }
        .hero-car { position: absolute; right: -5%; width: 55%; z-index: 2; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.15)); }

        .search-container { margin-top: -60px; position: relative; z-index: 10; }
        .search-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 15px 45px rgba(0,0,0,0.1); border: 1px solid #eee; }
        .filter-label { display: block; font-size: 0.75rem; font-weight: 800; margin-bottom: 8px; color: #111; text-transform: uppercase; }
        .filter-input { width: 100%; padding: 12px 15px; background: var(--input-bg); border: 1px solid #eee; border-radius: 12px; font-weight: 600; outline: none; font-size: 0.9rem; }

        .btn-auth { background: var(--primary); color: white; padding: 14px 30px; border: none; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block; transition: 0.3s; box-shadow: 0 10px 20px rgba(247, 166, 0, 0.2); cursor: pointer; }
        .btn-auth:hover { background: var(--primary-hover); transform: translateY(-2px); color: #fff; }

        .section-title { font-weight: 800; font-size: 1.8rem; margin-bottom: 30px; }
        .brand-item { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 25px; text-align: center; transition: 0.3s; text-decoration: none; display: block; color: #111; }
        .brand-item:hover { border-color: var(--primary); transform: translateY(-5px); }
        .brand-item img { height: 35px; margin-bottom: 12px; }

        .dark-section { background: var(--bg-dark); padding: 100px 0; color: #fff; margin-top: 80px; }
        .dark-section .section-title { color: #fff; }
        .car-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; overflow: hidden; transition: 0.3s; height: 100%; display: flex; flex-direction: column; }
        .car-card:hover { border-color: var(--primary); }
        
        /* IMAGE STYLING */
        .car-card img { 
            width: 100%; 
            height: 200px; 
            object-fit: contain; /* Changed to contain to show full car render */
            padding: 20px;
            background: rgba(255,255,255,0.02);
        }

        .car-card-body { padding: 25px; flex-grow: 1; }
        .car-specs { display: flex; justify-content: space-between; color: var(--text-muted); font-size: 0.8rem; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 15px; padding-top: 15px; }
        .car-specs i { color: var(--primary); }
        .price-tag { color: var(--primary); font-weight: 800; font-size: 1.1rem; }

        @media (max-width: 991px) {
            .hero-section::after { display: none; }
            .hero-title { font-size: 2.8rem; text-align: center; }
            .hero-car { position: relative; width: 100%; right: 0; margin-top: 30px; }
            .hero-section { height: auto; padding: 60px 0; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="brand-name" href="dashboard.php">
            <i class="fas fa-car-side" style="color: var(--primary);"></i> Car<span>Ride</span>
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="rental.php">Rent a Car</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php" style="color: #dc3545 !important;">Admin Panel</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <span class="fw-bold me-3 text-dark">Hi, <?= htmlspecialchars($_SESSION['user']) ?>!</span>
                <a href="logout.php" class="btn btn-outline-dark rounded-pill px-4">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">UNLOCK YOUR <br><span>ADVENTURE,</span> <br>DRIVE YOUR DREAMS</h1>
                <a href="#available-cars" class="btn-auth">Explore Fleet <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
    <img src="https://www.pngplay.com/wp-content/uploads/13/BMW-X5-Transparent-File.png" alt="Hero Car" class="hero-car">
</section>

<!-- Filter Bar -->
<div class="container search-container">
    <div class="search-card">
        <form action="dashboard.php#available-cars" method="GET">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="filter-label">Search by Brand</label>
                    <input type="text" name="brand" class="filter-input" placeholder="e.g. Toyota, BMW, Tesla..." value="<?= htmlspecialchars($search_brand) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-auth w-100"><i class="bi bi-search"></i> Find Car</button>
                    <?php if(!empty($search_brand)): ?>
                        <a href="dashboard.php" class="btn btn-dark rounded-3 px-3 d-flex align-items-center text-white"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Brands -->
<section class="container mt-5 pt-5">
    <h2 class="section-title">Rent by Brands</h2>
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
        <?php
        $brands = ['Tesla', 'BMW', 'Mercedes', 'Toyota', 'Audi', 'Ford'];
        foreach ($brands as $brand) {
            $domain = strtolower($brand) . ".com";
            echo "
            <div class='col'>
                <a href='dashboard.php?brand=$brand#available-cars' class='brand-item'>
                    <img src='https://logo.clearbit.com/$domain' alt='$brand'>
                    <p>$brand</p>
                </a>
            </div>";
        }
        ?>
    </div>
</section>

<!-- Dark Featured Section -->
<section class="dark-section" id="available-cars">
    <div class="container text-center mb-5">
        <h2 class="section-title">
            <?php echo (!empty($search_brand)) ? "Results for '$search_brand'" : "Available Vehicles"; ?>
        </h2>
    </div>
    <div class="container">
        <div class="row g-4">
            
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($car = mysqli_fetch_assoc($result)): ?>
                <?php 
                    // SMART IMAGE LOGIC: 
                    // 1. If image_path exists in DB, use it.
                    // 2. Otherwise, fetch a perfect 3D render from Imagin.studio API
                    $carImage = !empty($car['image_path']) ? $car['image_path'] : "https://cdn.imagin.studio/getimage?customer=img&make=".urlencode($car['brand'])."&modelFamily=".urlencode($car['model'])."&zoomType=fullscreen&modelYear=".$car['year'];
                ?>
                <div class="col-md-4">
                    <div class="car-card">
                        <!-- THE ACCURATE IMAGE -->
                        <img src="<?= $carImage ?>" alt="<?= htmlspecialchars($car['brand']) ?>">
                        
                        <div class="car-card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="fw-bold mb-1 text-white"><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h5>
                                    <p class="text-secondary small mb-0"><?= htmlspecialchars($car['category_name'] ?? 'Luxury') ?> (<?= $car['year'] ?>)</p>
                                </div>
                                <div class="price-tag">$<?= number_format($car['daily_rate'], 0) ?></div>
                            </div>
                            
                            <div class="car-specs">
                                <span><i class="bi bi-fuel-pump"></i> <?= htmlspecialchars($car['fuel_type'] ?? 'Petrol') ?></span>
                                <span><i class="bi bi-gear"></i> <?= htmlspecialchars($car['transmission'] ?? 'Auto') ?></span>
                                <span><i class="bi bi-cpu"></i> <?= htmlspecialchars($car['displacement'] ?? '2.0') ?>L</span>
                            </div>
                            <div class="mt-4">
                                <a href="rental.php?car_id=<?= $car['car_id'] ?>" class="btn-auth w-100 text-center py-2">Rent This Car</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-white">No matching cars found.</h4>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<footer class="py-5 text-center text-muted border-top border-secondary mt-5">
    <small>&copy; 2025 CarRide Adventure. All Rights Reserved.</small>
</footer>

</body>
</html>