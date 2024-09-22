
<?php
require '../config.php';

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
                    <a href="book_service.php?provider_id=' . urlencode($service['provider_id']) . '&service_id=' . urlencode($service['service_id']) . '" class="btn btn-primary">Book Now</a>
                </div>
            </div>
        </div>';
    }
}
?>
