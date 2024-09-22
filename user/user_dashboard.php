 <?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

require '../config.php';

$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';
$last_name = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-top: 20px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-link {
            font-size: 1.1rem;
            font-weight: 500;
        }
        .service-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .service-card img {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            max-height: 200px;
            object-fit: cover;
        }
        .card-title {
            font-weight: 600;
            font-size: 1.3rem;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="view_my_bookings.php">View My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="send_messages.php">Contact Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="submit_review.php">Leave a Review</a>
                    </li>
                </ul>

                <!-- Search Bar in Navbar -->
                <form class="d-flex" id="searchForm">
                    <input class="form-control me-2" type="search" placeholder="Search services" aria-label="Search" id="searchInput">
                </form>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($first_name . ' ' . $last_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profile.php">View Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center my-4">Available Services</h1>

        <!-- Services will be dynamically updated here -->
        <div class="row" id="serviceResults"></div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Home Services. All Rights Reserved.</p>
    </footer>

    <script>
        $(document).ready(function() {
            function fetchServices(query) {
                $.ajax({
                    url: "fetch_services.php",
                    method: "GET",
                    data: { search: query },
                    success: function(data) {
                        $("#serviceResults").html(data);
                    }
                });
            }

            // Initial fetch without any search query
            fetchServices('');

            // Fetch services as user types
            $('#searchInput').on('keyup', function() {
                var query = $(this).val();
                fetchServices(query);
            });
        });
    </script>
</body>
</html>
