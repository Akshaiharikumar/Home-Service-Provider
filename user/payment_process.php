<?php
session_start();

// Simulate payment process
$card_number = $_POST['card_number'];
$card_expiry = $_POST['card_expiry'];
$card_cvc = $_POST['card_cvc'];

// You can add more validation or logging here as needed for the demo

// Simulate payment success
$payment_success = true;

if ($payment_success) {
    // Redirect to a success page or update the booking status in the database
    // Corrected path to config.php
    require_once '../config.php';

    $booking_id = $_SESSION['booking_id'];

    try {
        $stmt = $conn->prepare("UPDATE Bookings SET status = 'Paid', payment_status = 'Completed' WHERE booking_id = :booking_id");
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();

        // Optionally clear the session or specific session variables
        unset($_SESSION['booking_id'], $_SESSION['service_name'], $_SESSION['service_price']);

        // Redirect to a success page
        header("Location: payment_success.php");
        exit();
    } catch (PDOException $e) {
        echo "Error updating booking status: " . $e->getMessage();
    }
} else {
    // Handle payment failure
    header("Location: payment_failure.php");
    exit();
}
?>

