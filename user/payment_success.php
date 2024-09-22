 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .success-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .success-container h2 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .success-container p {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            text-transform: uppercase;
        }
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h2>Payment Successful!</h2>
        <p>Thank you for your payment. Your booking has been confirmed.</p>
        <a href="user_dashboard.php" class="btn btn-success">Go to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
