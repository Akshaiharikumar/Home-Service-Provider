<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

// Include the config file to connect to the database
include('../config.php');

$user_id = $_SESSION['user_id'];

// Fetch the user's bookings from the database
$stmt = $conn->prepare("SELECT 
                            bookings.booking_id, 
                            services.service_name, 
                            bookings.booking_date, 
                            bookings.booking_time,
                            bookings.location,
                            bookings.status, 
                            bookings.total_cost
                        FROM bookings
                        INNER JOIN services ON bookings.service_id = services.service_id
                        WHERE bookings.user_id = :user_id
                        ORDER BY bookings.booking_date DESC");
$stmt->execute(['user_id' => $user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-top: 20px;
        }
        .container {
            max-width: 900px;
        }
        .table {
            margin-top: 20px;
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
        <h1 class="text-center my-4">My Bookings</h1>

        <?php if (count($bookings) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Booking Date</th>
                        <th>Booking Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['service_name']); ?></td>
                            <td><?= htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?= htmlspecialchars($booking['booking_time']); ?></td>
                            <td><?= htmlspecialchars($booking['location']); ?></td>
                            <td><?= htmlspecialchars($booking['status']); ?></td>
                            <td><?= htmlspecialchars($booking['total_cost']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">You have no bookings yet.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Home Services. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
