<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Service Provider') {
    header("Location: ../login.php");
    exit();
}

// Fetch the current provider's information
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone_number, address, profile_picture FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$provider = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $address = htmlspecialchars($_POST['address']);
    
    // Handle profile picture upload
    $profile_picture = $provider['profile_picture']; // Default to existing picture
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file;
        }
    }

    // Update provider information in the database
    $stmt = $conn->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, phone_number = :phone_number, address = :address, profile_picture = :profile_picture WHERE user_id = :user_id");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':profile_picture', $profile_picture);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "An error occurred. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Provider Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($provider['first_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($provider['last_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($provider['phone_number']) ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($provider['address']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                <?php if (!empty($provider['profile_picture'])): ?>
                    <img src="<?= htmlspecialchars($provider['profile_picture']) ?>" alt="Profile Picture" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>
