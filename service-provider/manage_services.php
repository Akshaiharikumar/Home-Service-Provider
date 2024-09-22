 <?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Service Provider') {
    header("Location: ../login.php");
    exit();
}

require '../config.php';

$provider_id = $_SESSION['user_id']; // This gets the user ID from the session
$success = '';

try {
    // Check if the service provider exists in the Service_Providers table
    $providerCheckStmt = $conn->prepare("SELECT provider_id FROM Service_Providers WHERE user_id = :user_id");
    $providerCheckStmt->bindParam(':user_id', $provider_id);
    $providerCheckStmt->execute();

    if ($providerCheckStmt->rowCount() > 0) {
        $providerData = $providerCheckStmt->fetch(PDO::FETCH_ASSOC);
        $provider_id = $providerData['provider_id']; // Correctly set the provider ID for later use

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $service_name = $_POST['service_name'];
            $description = $_POST['description'];
            $base_price = $_POST['base_price'];

            // Handle the image upload
            $image = $_FILES['service_image'];
            $image_name = $image['name'];
            $image_tmp_name = $image['tmp_name'];
            $image_folder = "../uploads/services/";

            if (!file_exists($image_folder)) {
                mkdir($image_folder, 0777, true);
            }

            $image_path = $image_folder . basename($image_name);
            move_uploaded_file($image_tmp_name, $image_path);

            $sql = "INSERT INTO Services (service_name, description, base_price, provider_id, image_path)
                    VALUES (:service_name, :description, :base_price, :provider_id, :image_path)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':service_name', $service_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':base_price', $base_price);
            $stmt->bindParam(':provider_id', $provider_id); // Correct provider ID is now used here
            $stmt->bindParam(':image_path', $image_path);

            if ($stmt->execute()) {
                $success = "Service added successfully!";
            } else {
                $success = "Error adding service.";
            }
        }
    } else {
        $success = "Error: Service provider with user ID $provider_id not found in the Service_Providers table.";
    }
} catch (PDOException $e) {
    $success = "Database error: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Add New Service</h2>

        <?php if ($success): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="manage_services.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="service_name">Service Name</label>
                <input type="text" class="form-control" id="service_name" name="service_name" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="base_price">Base Price</label>
                <input type="number" step="0.01" class="form-control" id="base_price" name="base_price" required>
            </div>

            <div class="form-group">
                <label for="service_image">Service Image</label>
                <input type="file" class="form-control" id="service_image" name="service_image" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Add Service</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
