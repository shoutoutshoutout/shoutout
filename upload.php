<?php
header("Content-Type: application/json");

// Show errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Max file size 10 MB
$maxFileSize = 10 * 1024 * 1024;

// Allowed mime types
$allowedTypes = ["image/jpeg", "image/png"];

// Upload folder
$uploadDir = __DIR__ . "/storyqueue/";
if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

// Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// Get raw POST body
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['base64']) || !isset($data['mimeType'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$mimeType = $data['mimeType'];
if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(["error" => "Only PNG and JPG allowed"]);
    exit;
}

// Decode image
$imageData = base64_decode($data['base64']);
if (!$imageData) {
    http_response_code(400);
    echo json_encode(["error" => "Base64 decoding failed"]);
    exit;
}

if (strlen($imageData) > $maxFileSize) {
    http_response_code(400);
    echo json_encode(["error" => "File too large"]);
    exit;
}

// ------------------
// Determine next number
// ------------------
$files = scandir($uploadDir);
$maxNum = 0;
foreach ($files as $file) {
    if (preg_match('/^(\d+)_/', $file, $m)) {
        $num = intval($m[1]);
        if ($num > $maxNum) $maxNum = $num;
    }
}
$nextNum = $maxNum + 1;

// ------------------
// Generate timestamp
// ------------------
$timestamp = date("Hisdmy");

// ------------------
// Determine extension
// ------------------
$ext = $mimeType === "image/png" ? "png" : "jpg";
$filename = $nextNum . "_" . $timestamp . "." . $ext;
$filepath = $uploadDir . $filename;

// ------------------
// Save file
// ------------------
if (file_put_contents($filepath, $imageData) === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to save file"]);
    exit;
}

// ------------------
// Return success
// ------------------
echo json_encode([
    "filename" => $filename,
    "url" => "/storyqueue/" . $filename
]);