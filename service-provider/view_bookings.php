 <?php
session_start();

// Check if the user is logged in and is a Service Provider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Service Provider') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Retrieve the provider_id using the user_id
$query = "SELECT provider_id FROM service_providers WHERE user_id = :user_id LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && isset($result['provider_id'])) {
    $provider_id = $result['provider_id'];
} else {
    echo "Service Provider ID not found for this user.";
    exit();
}

// Fetch bookings related to the service provider
$query = "
SELECT 
    b.booking_id, 
    b.booking_date, 
    b.booking_time, 
    b.location, 
    b.phone_number, 
    b.payment_status, 
    b.total_cost, 
    b.status, 
    u.first_name, 
    u.last_name, 
    s.service_name
FROM 
    bookings b
JOIN 
    services s ON b.service_id = s.service_id
JOIN 
    users u ON b.user_id = u.user_id
WHERE 
    s.provider_id = :provider_id
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug output to check if the query returns data
// echo "<pre>";
// print_r($bookings);
// echo "</pre>";

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    
    $update_query = "UPDATE bookings SET status = :status WHERE booking_id = :booking_id AND provider_id = :provider_id";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bindParam(':status', $status);
    $update_stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $update_stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
    $update_stmt->execute();
    
    // Refresh the page to reflect changes
    header("Location: view_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .table {
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
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
                        <th>User Name</th>
                        <th>Booking Date</th>
                        <th>Booking Time</th>
                        <th>Location</th>
                        <th>Phone Number</th>
                        <th>Payment Status</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['location']); ?></td>
                            <td><?php echo htmlspecialchars($booking['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($booking['payment_status']); ?></td>
                            <td><?php echo htmlspecialchars($booking['total_cost']); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <select name="status" class="form-select form-select-sm mb-2" required>
                                        <option value="Pending" <?php echo $booking['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Confirmed" <?php echo $booking['status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Completed" <?php echo $booking['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo $booking['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Update Status</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                You have no bookings yet.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
