 <?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Service Provider') {
    header("Location: ../login.php");
    exit();
}

// Fetch the provider_id using the user_id from the session
$stmt = $conn->prepare("SELECT provider_id FROM service_providers WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$provider = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$provider) {
    echo "Error: Service provider not found.";
    exit();
}

$provider_id = $provider['provider_id'];

// Fetch services provided by the provider
$stmt = $conn->prepare("SELECT service_id, service_name FROM services WHERE provider_id = :provider_id");
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch bookings for a selected service
$bookings = [];
if (isset($_POST['service_id'])) {
    $service_id = $_POST['service_id'];
    $stmt = $conn->prepare("
        SELECT b.booking_id, u.first_name, u.last_name, b.booking_date, b.booking_time, b.total_cost
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        WHERE b.provider_id = :provider_id AND b.service_id = :service_id
    ");
    $stmt->bindParam(':provider_id', $provider_id);
    $stmt->bindParam(':service_id', $service_id);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Submit report to the admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_report'])) {
    $service_id = $_POST['service_id'];
    foreach ($bookings as $booking) {
        $report_text = "Booking for " . $booking['first_name'] . " " . $booking['last_name'] . " on " . $booking['booking_date'] . " at " . $booking['booking_time'] . ", Total: $" . $booking['total_cost'];
        
        $stmt = $conn->prepare("INSERT INTO reports (provider_id, service_id, booking_id, report_text) 
                                VALUES (:provider_id, :service_id, :booking_id, :report_text)");
        $stmt->bindParam(':provider_id', $provider_id);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->bindParam(':booking_id', $booking['booking_id']);
        $stmt->bindParam(':report_text', $report_text);
        $stmt->execute();
    }
    echo "Report submitted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Booking Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Submit Booking Report</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="service_id" class="form-label">Select a Service</label>
                <select name="service_id" id="service_id" class="form-select" required>
                    <option value="">-- Select Service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= htmlspecialchars($service['service_id']) ?>"><?= htmlspecialchars($service['service_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Display bookings if a service is selected -->
            <?php if (!empty($bookings)): ?>
                <h3>Bookings for Selected Service</h3>
                <ul class="list-group mb-3">
                    <?php foreach ($bookings as $booking): ?>
                        <li class="list-group-item">
                            Booking for <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?> on <?= htmlspecialchars($booking['booking_date']) ?> at <?= htmlspecialchars($booking['booking_time']) ?>, Total: $<?= htmlspecialchars($booking['total_cost']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <button type="submit" name="submit_report" class="btn btn-primary">Submit Report</button>
        </form>
    </div>
</body>
</html>
