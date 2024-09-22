<?php
session_start();
require_once '../config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Handle user update
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number, address = :address, role = :role WHERE user_id = :user_id");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    // Optionally, add a success message or redirect
    header("Location: manage_users.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    // Optionally, add a success message or redirect
    header("Location: manage_users.php");
    exit();
}

// Fetch all users from the database
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, phone_number, address, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Users</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td><?= htmlspecialchars($user['address']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['user_id'] ?>">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['user_id'] ?>)">Delete</button>
                            </td>
                        </tr>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal<?= $user['user_id'] ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $user['user_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel<?= $user['user_id'] ?>">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="manage_users.php">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <div class="mb-3">
                                                <label for="first_name" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="last_name" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone_number" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <select class="form-select" id="role" name="role" required>
                                                    <option value="User" <?= $user['role'] === 'User' ? 'selected' : '' ?>>User</option>
                                                    <option value="Service Provider" <?= $user['role'] === 'Service Provider' ? 'selected' : '' ?>>Service Provider</option>
                                                    <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" name="update_user">Update User</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'manage_users.php?delete_user_id=' + userId;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
