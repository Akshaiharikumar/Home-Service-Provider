 <?php
// Start session but do not require authentication for guests
require 'config.php';

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch available services based on the search query
$sql = "
    SELECT 
        sp.provider_id, 
        u.first_name AS provider_first_name, 
        u.last_name AS provider_last_name,
        s.service_id,
        s.service_name,
        s.image_path,
        IFNULL(AVG(r.rating), 0) as average_rating,
        COUNT(r.review_id) as review_count
    FROM 
        Service_Providers sp
    JOIN 
        Services s ON sp.provider_id = s.provider_id
    JOIN 
        Users u ON sp.user_id = u.user_id
    LEFT JOIN 
        Reviews r ON s.service_id = r.service_id
    WHERE 
        sp.approval_status = 'Approved'
        AND s.service_name LIKE :search_query
    GROUP BY 
        s.service_id, sp.provider_id, u.first_name, u.last_name, s.service_name, s.image_path
    ORDER BY 
        average_rating DESC, review_count DESC
";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">Available Services</h1>

        <!-- Services will be dynamically updated here -->
        <div class="row" id="serviceResults">
            <?php
            if (empty($services)) {
                echo '<p class="text-center">No services found.</p>';
            } else {
                foreach ($services as $service) {
                    echo '
                    <div class="col-md-4">
                        <div class="card service-card">
                            <img src="' . htmlspecialchars($service['image_path']) . '" class="card-img-top" alt="Service Image">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($service['service_name']) . '</h5>
                                <p class="card-text">Provided by: ' . htmlspecialchars($service['provider_first_name'] . ' ' . $service['provider_last_name']) . '</p>
                                <p class="card-text">
                                    <strong>Rating:</strong> ' . number_format($service['average_rating'], 1) . ' / 5 
                                    (' . htmlspecialchars($service['review_count']) . ' Reviews)
                                </p>
                                <a href="authentication/login.php" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
