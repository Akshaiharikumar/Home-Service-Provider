<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->query("SELECT admin_id FROM admins LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
$admin_id = $admin['admin_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("
        INSERT INTO messages (user_id, admin_id, subject, message) 
        VALUES (:user_id, :admin_id, :subject, :message)
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);
    $stmt->execute();

    echo "Your message has been sent to the admin.";
}

// Fetch user's messages and admin replies
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT subject, message, reply, status, created_at
    FROM messages
    WHERE user_id = :user_id
    ORDER BY created_at DESC
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Contact Admin</h2>
        <form action="send_messages.php" method="POST">
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>

        <h2 class="mt-5">Your Messages and Admin Replies</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Reply</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['subject']) ?></td>
                        <td><?= htmlspecialchars($message['message']) ?></td>
                        <td><?= htmlspecialchars($message['reply']) ?></td>
                        <td><?= htmlspecialchars($message['status']) ?></td>
                        <td><?= htmlspecialchars($message['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
