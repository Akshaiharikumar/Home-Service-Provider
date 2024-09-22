 <?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Correct path to config.php
include('../config.php');

// Delete a service provider
if (isset($_GET['delete_provider_id'])) {
    $delete_provider_id = $_GET['delete_provider_id'];
    $sql = "DELETE FROM Service_Providers WHERE provider_id = :provider_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':provider_id', $delete_provider_id);
    $stmt->execute();
    header("Location: manage_providers.php");
    exit();
}

// Approve or reject a service provider
if (isset($_POST['provider_id']) && isset($_POST['action'])) {
    $provider_id = $_POST['provider_id'];
    $action = $_POST['action'];

    $approval_status = ($action == 'Approve') ? 'Approved' : 'Rejected';

    $sql = "UPDATE Service_Providers SET approval_status = :approval_status WHERE provider_id = :provider_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':approval_status', $approval_status);
    $stmt->bindParam(':provider_id', $provider_id);
    $stmt->execute();
    header("Location: manage_providers.php");
    exit();
}

// Fetch all service providers
$sql = "SELECT sp.*, u.email FROM Service_Providers sp JOIN Users u ON sp.user_id = u.user_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Service Providers</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            margin: 0 5px;
            display: inline-block;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-reject {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Manage Service Providers</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Approval Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($providers as $provider): ?>
        <tr>
            <td><?php echo $provider['provider_id']; ?></td>
            <td><?php echo $provider['email']; ?></td>
            <td><?php echo $provider['approval_status']; ?></td>
            <td>
                <?php if ($provider['approval_status'] == 'Pending'): ?>
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                        <button type="submit" name="action" value="Approve" class="btn btn-approve">Approve</button>
                        <button type="submit" name="action" value="Reject" class="btn btn-reject">Reject</button>
                    </form>
                <?php endif; ?>
                <a href="manage_providers.php?delete_provider_id=<?php echo $provider['provider_id']; ?>" class="btn">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
