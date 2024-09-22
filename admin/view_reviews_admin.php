<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

try {
    // Fetch all reviews with user and provider details
    $stmt = $conn->prepare("
    SELECT r.review_id, r.rating, r.review_text, r.created_at, 
           u.first_name AS user_first_name, u.last_name AS user_last_name, 
           u2.first_name AS provider_first_name, u2.last_name AS provider_last_name
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN service_providers sp ON r.provider_id = sp.provider_id
    JOIN users u2 ON sp.user_id = u2.user_id
    ORDER BY r.created_at DESC
");

    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>All Reviews</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Service Provider</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?= htmlspecialchars($review['user_first_name'] . ' ' . $review['user_last_name']) ?></td>
                            <td><?= htmlspecialchars($review['provider_first_name'] . ' ' . $review['provider_last_name']) ?></td>
                            <td><?= htmlspecialchars($review['rating']) ?></td>
                            <td><?= htmlspecialchars($review['review_text']) ?></td>
                            <td><?= htmlspecialchars($review['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No reviews found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
