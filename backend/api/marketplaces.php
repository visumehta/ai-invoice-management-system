
<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $sql = "SELECT DISTINCT normalized_marketplace
            FROM invoices
            WHERE normalized_marketplace IS NOT NULL
              AND TRIM(normalized_marketplace) <> ''
            ORDER BY normalized_marketplace ASC";

    $result = $conn->query($sql);
    $marketplaces = [];

    while ($row = $result->fetch_assoc()) {
        $marketplaces[] = $row['normalized_marketplace'];
    }

    echo json_encode([
        'success' => true,
        'data' => $marketplaces
    ]);
} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Failed to load marketplaces'
    ]);
}
