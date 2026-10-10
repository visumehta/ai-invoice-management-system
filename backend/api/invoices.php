<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "../config/database.php";


$sql = "SELECT * FROM invoices ORDER BY id DESC";

$result = mysqli_query($conn, $sql);


if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

    exit;
}


$invoices = [];


while ($row = mysqli_fetch_assoc($result)) {

    $invoices[] = $row;
}


echo json_encode([
    "success" => true,
    "data" => $invoices
]);

?>