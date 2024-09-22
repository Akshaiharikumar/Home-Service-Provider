 <?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate form inputs
    if (empty($email) || empty($password)) {
        $errorMessage = "Please fill in both email and password.";
    } else {
        // Check if user exists
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Check if service provider is approved
                if ($user['role'] === 'Service Provider') {
                    $sql = "SELECT approval_status FROM Service_Providers WHERE user_id = :user_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':user_id', $user['user_id']);
                    $stmt->execute();
                    $provider = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($provider['approval_status'] !== 'Approved') {
                        $errorMessage = "Your account is not approved by the admin yet.";
                    } else {
                        // Set session variables
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['first_name'] = $user['first_name'];
                        header('Location: ../service-provider/service_provider_dashboard.php');
                        exit;
                    }
                } else {
                    // Set session variables for other roles
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['first_name'] = $user['first_name'];

                    // Redirect based on role
                    if ($user['role'] === 'Admin') {
                        header('Location: ../admin/admin_dashboard.php');
                    } else {
                        header('Location: ../user/user_dashboard.php');
                    }
                    exit;
                }
            } else {
                $errorMessage = "Incorrect password. Please try again.";
            }
        } else {
            $errorMessage = "No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-form {
            width: 400px;
            padding: 30px;
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
    <div class="login-form">
        <div class="form-title">Login</div>
        
        <!-- Display error messages, if any -->
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

    <!-- Bootstrap JS (Optional if you need Bootstrap JS components like modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>