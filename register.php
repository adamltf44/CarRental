<?php
session_start();
include "db.php";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');
    $password_input = $_POST['password'] ?? '';

    // Check if details already exist
    $check_sql = "SELECT full_name FROM users WHERE full_name = ? OR email = ? OR phone_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $username, $email, $phone);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Username, Email, or Phone already registered.";
    } else {
        $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, phone_number, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $phone, $password_hashed);

        if ($stmt->execute()) {
            header("Location: Index.php?msg=Account created! Please login.");
            exit();
        } else {
            $message = "Error creating account.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarRide - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            /* Same Background as Login */
            background: linear-gradient(rgba(10, 10, 10, 0.7), rgba(10, 10, 10, 0.7)), 
                        url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=1920');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            padding: 20px 0;
        }

        .auth-card { 
            background: #fff; 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.3); 
            border: none; 
            max-width: 950px; 
            width: 100%; 
            margin: auto; 
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-info { 
            background: linear-gradient(135deg, #0a0a0a 60%, #f7a600 60%); 
            color: #fff; 
            padding: 40px; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            position: relative; 
        }

        .text-yellow { color: #f7a600; }
        .auth-form { padding: 40px 50px; background: #fff; }
        
        .form-control { 
            border-radius: 10px; 
            padding: 10px 15px; 
            border: 1px solid #eee; 
            background-color: #fcfcfc; 
            margin-bottom: 12px; 
        }
        
        .form-control:focus { 
            box-shadow: 0 0 0 3px rgba(247, 166, 0, 0.2); 
            border-color: #f7a600; 
        }
        
        .btn-yellow { 
            background-color: #f7a600; 
            color: #fff; 
            border: none; 
            border-radius: 10px; 
            padding: 12px; 
            font-weight: 600; 
            width: 100%; 
            transition: 0.3s; 
        }
        
        .btn-yellow:hover { 
            background-color: #d68e00; 
            color: #fff; 
            transform: translateY(-2px);
        }
        
        .brand-logo { 
            font-size: 1.5rem; 
            font-weight: 700; 
            margin-bottom: 20px; 
            display: block; 
            text-decoration: none; 
            color: #111; 
        }

        label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: #444;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card auth-card">
        <div class="row g-0">
            <!-- Left Side (Visuals) -->
            <div class="col-lg-5 d-none d-lg-flex auth-info">
                <i class="bi bi-car-front-fill fs-1 text-yellow mb-4"></i>
                <h2 class="fw-bold">JOIN THE <br><span class="text-yellow">ADVENTURE.</span></h2>
                <p class="text-secondary">Create an account to unlock exclusive deals and manage your bookings easily.</p>
                <!-- Decorative Car Image -->
                <img src="https://www.pngplay.com/wp-content/uploads/13/BMW-X5-Transparent-File.png" 
                     style="width: 130%; position: absolute; bottom: -20px; left: -15%; filter: drop-shadow(0 20px 20px rgba(0,0,0,0.5));" alt="car">
            </div>

            <!-- Right Side (Form) -->
            <div class="col-lg-7 auth-form">
                <a href="#" class="brand-logo"><i class="bi bi-car-front-fill text-yellow"></i> CarRide</a>
                <h4 class="fw-bold">Create Account</h4>
                <p class="text-muted small mb-4">Please fill in your details to get started.</p>

                <?php if ($message): ?>
                    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" placeholder="example" required>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="example@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label>Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="+601..." required>
                        </div>
                    </div>

                    <label>Create Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>

                    <button type="submit" class="btn btn-yellow mt-2">Sign Up Now</button>
                </form>

                <p class="text-center small mt-4">Already have an account? <a href="index.php" class="text-yellow fw-bold text-decoration-none">Login here</a></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>