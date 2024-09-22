<?php
// Start session to get booking details
session_start();

// Check if the booking ID is set in the session
if (!isset($_SESSION['booking_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

// Get booking ID and other details from the session
$booking_id = $_SESSION['booking_id'];
$service_name = $_SESSION['service_name'];
$service_price = $_SESSION['service_price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .payment-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: 600;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .payment-details {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <h2 class="text-center mb-4">Payment for Service</h2>
            <div class="payment-details">
                <p><strong>Service:</strong> <?php echo $service_name; ?></p>
                <p><strong>Amount:</strong> $<?php echo $service_price; ?></p>
            </div>

            <form action="payment_process.php" method="POST">
                <div class="mb-3">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" id="card_number" name="card_number" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="card_expiry" class="form-label">Expiry Date (MM/YY)</label>
                    <input type="text" id="card_expiry" name="card_expiry" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="card_cvc" class="form-label">CVC</label>
                    <input type="text" id="card_cvc" name="card_cvc" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Pay Now</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
