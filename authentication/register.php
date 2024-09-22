<?php
include '../config.php'; // Adjust the path to your config.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profilePicture = $_FILES['profile_picture']['name'];

    // File upload path
    $targetDir = "../uploads/";
    $targetFile = $targetDir . basename($profilePicture);

    // Validate form data (example: check for required fields)
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $errorMessage = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $errorMessage = "Passwords do not match.";
    } elseif (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
        $errorMessage = "Sorry, there was an error uploading your profile picture.";
    } else {
        // Insert data into the Users table
        $sql = "INSERT INTO Users (first_name, last_name, email, phone_number, address, role, password, profile_picture) 
                VALUES (:first_name, :last_name, :email, :phone_number, :address, :role, :password, :profile_picture)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':profile_picture', $profilePicture);

        if ($stmt->execute()) {
            // Get the last inserted user ID
            $user_id = $conn->lastInsertId();

            // If the user is a service provider, insert into the Service_Providers table with Pending approval status
            if ($role == 'Service Provider') {
                $sql = "INSERT INTO Service_Providers (user_id, approval_status) 
                        VALUES (:user_id, 'Pending')";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
            }

            // If the user is an admin, insert into the Admins table
            if ($role == 'Admin') {
                $sql = "INSERT INTO Admins (user_id) VALUES (:user_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
            }

            // Redirect to the home page with a success message
            echo "<script>alert('Registration successful! Redirecting to the home page.'); window.location.href='../index.php';</script>";
            exit;
        } else {
            $errorMessage = "Registration failed. Please try again.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .registration-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="registration-form">
            <div class="form-title">Register</div>
            <!-- Display error messages, if any -->
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($phone_number ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($address ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="User" <?= isset($role) && $role == 'User' ? 'selected' : '' ?>>User</option>
                        <option value="Admin" <?= isset($role) && $role == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="Service Provider" <?= isset($role) && $role == 'Service Provider' ? 'selected' : '' ?>>Service Provider</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>

            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional if you need Bootstrap JS components like modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>