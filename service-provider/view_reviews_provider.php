<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Service Provider') {
    header("Location: ../login.php");
    exit();
}

// Fetch the provider_id using the user_id from the session
$stmt = $conn->prepare("SELECT provider_id FROM service_providers WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$provider = $stmt->fetch(PDO::FETCH_ASSOC);

if ($provider) {
    $provider_id = $provider['provider_id'];
    
    // Fetch service provider's reviews
    $stmt = $conn->prepare("
        SELECT r.review_id, r.rating, r.review_text, r.created_at, u.first_name, u.last_name
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.provider_id = :provider_id
        ORDER BY r.created_at DESC
    ");
    $stmt->bindParam(':provider_id', $provider_id);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Handle the case where no provider_id is found
    $reviews = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>My Reviews</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></td>
                            <td><?= htmlspecialchars($review['rating']) ?></td>
                            <td><?= htmlspecialchars($review['review_text']) ?></td>
                            <td><?= htmlspecialchars($review['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No reviews found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
