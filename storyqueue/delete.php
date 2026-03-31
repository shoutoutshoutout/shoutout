<?php
header("Content-Type: application/json");

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['filename'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing filename"]);
    exit;
}

$filename = basename($data['filename']); // sanitize
$filepath = __DIR__ . "/$filename";

if (!file_exists($filepath)) {
    http_response_code(404);
    echo json_encode(["error" => "File not found"]);
    exit;
}

if (!unlink($filepath)) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete file"]);
    exit;
}

echo json_encode(["success" => true, "filename" => $filename]);