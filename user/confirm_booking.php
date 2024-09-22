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
$user_id = $_SESSION['user_id'];

// Fetch the selected service details
$sql = "
    SELECT 
        s.service_name, 
        s.description, 
        s.base_price,
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

// Handle the booking confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user inputs for booking date and time
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $phone_number = $_POST['phone_number'];
    $location = $_POST['location'];
    $total_cost = $service['base_price']; // Calculate or modify the cost if needed

    // Insert the booking into the Bookings table
    $insert_sql = "
        INSERT INTO Bookings (user_id, provider_id, service_id, booking_date, booking_time, total_cost, phone_number, location, status, payment_status, created_at, updated_at)
        VALUES (:user_id, :provider_id, :service_id, :booking_date, :booking_time, :total_cost, :phone_number, :location, 'Pending', 'Pending', NOW(), NOW())
    ";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $insert_stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
    $insert_stmt->bindParam(':service_id', $service_id, PDO::PARAM_INT);
    $insert_stmt->bindParam(':booking_date', $booking_date, PDO::PARAM_STR);
    $insert_stmt->bindParam(':booking_time', $booking_time, PDO::PARAM_STR);
    $insert_stmt->bindParam(':total_cost', $total_cost, PDO::PARAM_STR);
    $insert_stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
    $insert_stmt->bindParam(':location', $location, PDO::PARAM_STR);

    if ($insert_stmt->execute()) {
        // Get the last inserted booking ID
        $booking_id = $conn->lastInsertId();

        // Store booking information in the session
        $_SESSION['booking_id'] = $booking_id;
        $_SESSION['service_name'] = $service['service_name'];
        $_SESSION['service_price'] = $total_cost;

        // Redirect to the payment process page
        header("Location: payment.php");
        exit();
    } else {
        $error_message = "Failed to confirm the booking. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
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
            margin-bottom: 20px;
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
    <h1 class="text-center my-4">Confirm Booking</h1>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($service['service_name']); ?></h5>
                    <p class="card-text">Provided by: <?= htmlspecialchars($service['provider_first_name'] . ' ' . $service['provider_last_name']); ?></p>
                    <p class="card-text"><?= htmlspecialchars($service['description']); ?></p>
                    <p class="card-text"><strong>Base Price:</strong> $<?= number_format($service['base_price'], 2); ?></p>

                    <!-- Display an error message if booking fails -->
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <!-- Booking form -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Booking Date</label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_time" class="form-label">Booking Time</label>
                            <input type="time" class="form-control" id="booking_time" name="booking_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <button type="submit" class="btn btn-success">Confirm Booking</button>
                    </form>
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
