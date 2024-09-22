<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Services Platform</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .jumbotron {
            background-image: url('images/home-background.jpg');
            background-size: cover;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            padding: 100px 25px;
        }
        .jumbotron h1 {
            font-size: 4em;
        }
        .jumbotron p {
            font-size: 1.5em;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Home Services Platform</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="authentication/register.php">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="authentication/login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="guest.php">View Services</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Jumbotron -->
    <div class="jumbotron text-center">
        <h1>Welcome to Home Services Platform</h1>
        <p>Your one-stop solution for all household needs</p>
        <a href="guest.php" class="btn btn-primary btn-lg">Browse Services</a>
    </div>

    <!-- About Us Section -->
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Why Choose Us?</h5>
                        <p class="card-text">We connect you with skilled professionals for all your household needs, ensuring quality and reliability every time.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Our Services</h5>
                        <p class="card-text">From carpentry to cleaning, and appliance repairs, we cover a wide range of services to meet your household demands.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Get Started</h5>
                        <p class="card-text">Register today and find the right service provider for your home maintenance needs!</p>
                        <a href="authentication/register.php" class="btn btn-success">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Home Services Platform. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
