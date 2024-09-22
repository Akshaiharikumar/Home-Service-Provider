 <?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch the admin_id
$stmt = $conn->prepare("SELECT admin_id FROM admins WHERE user_id = :user_id LIMIT 1");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
$admin_id = $admin['admin_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    // Process the reply to the user's message
    $reply = $_POST['reply'];
    $message_id = $_POST['message_id'];

    $stmt = $conn->prepare("
        UPDATE messages 
        SET reply = :reply, status = 'Read'
        WHERE message_id = :message_id AND admin_id = :admin_id
    ");
    $stmt->bindParam(':reply', $reply);
    $stmt->bindParam(':message_id', $message_id);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();

    echo "Reply has been sent.";
}

// Fetch messages from users
$stmt = $conn->prepare("
    SELECT m.message_id, m.subject, m.message, m.status, m.created_at, m.reply, u.first_name, u.last_name 
    FROM messages m
    JOIN users u ON m.user_id = u.user_id
    WHERE m.admin_id = :admin_id
    ORDER BY m.created_at DESC
");
$stmt->bindParam(':admin_id', $admin_id);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Messages from Users</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Reply</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?></td>
                        <td><?= htmlspecialchars($message['subject']) ?></td>
                        <td><?= htmlspecialchars($message['message']) ?></td>
                        <td><?= htmlspecialchars($message['reply']) ?></td>
                        <td><?= htmlspecialchars($message['status']) ?></td>
                        <td><?= htmlspecialchars($message['created_at']) ?></td>
                        <td>
                            <?php if (!$message['reply']): ?>
                                <!-- Reply Form -->
                                <form action="view_messages.php" method="POST">
                                    <input type="hidden" name="message_id" value="<?= $message['message_id'] ?>">
                                    <div class="mb-3">
                                        <textarea name="reply" class="form-control" rows="2" placeholder="Enter your reply here"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send Reply</button>
                                </form>
                            <?php else: ?>
                                <!-- Reply already sent -->
                                <span class="text-success">Replied</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
