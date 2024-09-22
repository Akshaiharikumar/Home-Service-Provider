<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

// Fetch the distinct service providers and their services for the user
$stmt = $conn->prepare("
    SELECT DISTINCT sp.provider_id, u.first_name, u.last_name, s.service_id, s.service_name 
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    JOIN service_providers sp ON s.provider_id = sp.provider_id
    JOIN users u ON sp.user_id = u.user_id
    WHERE b.user_id = :user_id
");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $provider_id = $_POST['provider_id'];
    $service_id = $_POST['service_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    $stmt = $conn->prepare("
        INSERT INTO reviews (user_id, provider_id, service_id, rating, review_text) 
        VALUES (:user_id, :provider_id, :service_id, :rating, :review_text)
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':provider_id', $provider_id);
    $stmt->bindParam(':service_id', $service_id);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':review_text', $review_text);
    $stmt->execute();

    echo "Your review has been submitted.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Submit Review</h2>
        <form action="submit_review.php" method="POST">
            <div class="mb-3">
                <label for="provider_id" class="form-label">Service Provider</label>
                <select id="provider_id" name="provider_id" class="form-control" required>
                    <option value="" disabled selected>Select a Service Provider</option>
                    <?php 
                    $seenProviders = [];
                    foreach ($providers as $provider): 
                        if (!in_array($provider['provider_id'], $seenProviders)): 
                            $seenProviders[] = $provider['provider_id'];
                    ?>
                        <option value="<?= htmlspecialchars($provider['provider_id']) ?>"><?= htmlspecialchars($provider['first_name'] . ' ' . $provider['last_name']) ?></option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="service_id" class="form-label">Service</label>
                <select id="service_id" name="service_id" class="form-control" required>
                    <option value="" disabled selected>Select a Service</option>
                    <?php foreach ($providers as $provider): ?>
                        <option value="<?= htmlspecialchars($provider['service_id']) ?>"><?= htmlspecialchars($provider['service_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select id="rating" name="rating" class="form-control" required>
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3">3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="review_text" class="form-label">Review</label>
                <textarea id="review_text" name="review_text" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
</body>
</html>
