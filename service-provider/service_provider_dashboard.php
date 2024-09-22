<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Service Provider') {
    header("Location: ../login.php");
    exit();
}

require '../config.php';

$provider_id = $_SESSION['user_id'];

// Fetch the service provider's name
$sql = "SELECT first_name, last_name FROM Users WHERE user_id = :provider_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$first_name = $user['first_name'];
$last_name = $user['last_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-top: 20px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-link {
            font-size: 1.1rem;
            font-weight: 500;
        }
        .dashboard-section {
            margin-top: 30px;
        }
        .card-title {
            font-weight: 600;
            font-size: 1.3rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Service Provider Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="manage_services.php">Manage Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_bookings.php">View Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_reviews_providers.php">View Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="submit_report.php">Submit Report</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center my-4">Welcome, <?= htmlspecialchars($first_name . ' ' . $last_name); ?>!</h1>

        <div class="row dashboard-section">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Services</h5>
                        <p class="card-text">View and manage your services.</p>
                        <a href="manage_services.php" class="btn btn-primary">Manage Services</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">View Bookings</h5>
                        <p class="card-text">View all your bookings.</p>
                        <a href="view_bookings.php" class="btn btn-primary">View Bookings</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">View Reviews</h5>
                        <p class="card-text">See reviews from your clients.</p>
                        <a href="view_reviews_provider.php" class="btn btn-primary">View Reviews</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Profile</h5>
                        <p class="card-text">Update your profile information.</p>
                        <a href="profile.php" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Submit Report</h5>
                        <p class="card-text">Submit your service reports.</p>
                        <a href="submit_report.php" class="btn btn-primary">Submit Report</a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Home Services. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
