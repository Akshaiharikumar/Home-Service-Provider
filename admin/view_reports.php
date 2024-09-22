 <?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch reports (booking details)
$stmt = $conn->prepare("
    SELECT r.report_id, sp.provider_id, s.service_name, r.report_text, r.submitted_at, u.first_name, u.last_name
    FROM reports r 
    JOIN service_providers sp ON r.provider_id = sp.provider_id 
    JOIN bookings b ON r.booking_id = b.booking_id
    JOIN users u ON b.user_id = u.user_id
    JOIN services s ON b.service_id = s.service_id
    ORDER BY r.submitted_at DESC
");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Submitted Booking Reports</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Provider ID</th>
                    <th>User Name</th>
                    <th>Service Name</th>
                    <th>Booking Details</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports)): ?>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['provider_id']) ?></td>
                            <td><?= htmlspecialchars($report['first_name'] . ' ' . $report['last_name']) ?></td>
                            <td><?= htmlspecialchars($report['service_name']) ?></td>
                            <td><?= htmlspecialchars($report['report_text']) ?></td>
                            <td><?= htmlspecialchars($report['submitted_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No reports found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
