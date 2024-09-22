<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

require '../config.php';

// Check if provider_id and service_id are provided in the URL
if (!isset($_GET['provider_id']) || !isset($_GET['service_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$provider_id = $_GET['provider_id'];
$service_id = $_GET['service_id'];

// Fetch the selected service details offered by the provider
$sql = "
    SELECT 
        s.service_id, 
        s.service_name, 
        s.description, 
        s.base_price, 
        s.image_path,
        sp.provider_id,
        u.first_name AS provider_first_name, 
        u.last_name AS provider_last_name
    FROM 
        Services s
    JOIN 
        Service_Providers sp ON s.provider_id = sp.provider_id
    JOIN 
        Users u ON sp.user_id = u.user_id
    WHERE 
        s.provider_id = :provider_id
        AND s.service_id = :service_id
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
$stmt->bindParam(':service_id', $service_id, PDO::PARAM_INT);
$stmt->execute();
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header("Location: user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-top: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card img {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            max-height: 400px;
            object-fit: cover;
        }
        .card-title {
            font-weight: 600;
            font-size: 1.8rem;
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

<div class="container">
    <h1 class="text-center my-4">Book Service</h1>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <img src="<?= htmlspecialchars($service['image_path']); ?>" class="card-img-top" alt="Service Image">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($service['service_name']); ?></h5>
                    <p class="card-text">Provided by: <?= htmlspecialchars($service['provider_first_name'] . ' ' . $service['provider_last_name']); ?></p>
                    <p class="card-text"><?= htmlspecialchars($service['description']); ?></p>
                    <p class="card-text"><strong>Base Price:</strong> $<?= number_format($service['base_price'], 2); ?></p>
                    <!-- Booking form or button goes here -->
                    <a href="confirm_booking.php?provider_id=<?= urlencode($provider_id); ?>&service_id=<?= urlencode($service_id); ?>" class="btn btn-success">Proceed to Book</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Home Services. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
