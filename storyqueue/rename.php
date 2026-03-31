<?php
header("Content-Type: application/json");

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['oldName']) || !isset($data['newName'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing oldName or newName"]);
    exit;
}

$oldName = basename($data['oldName']); // sanitize
$newName = basename($data['newName']); // sanitize

$oldPath = __DIR__ . "/$oldName";
$newPath = __DIR__ . "/$newName";

if (!file_exists($oldPath)) {
    http_response_code(404);
    echo json_encode(["error" => "File not found"]);
    exit;
}

// Prevent overwriting
if (file_exists($newPath)) {
    http_response_code(409);
    echo json_encode(["error" => "Target filename already exists"]);
    exit;
}

if (!rename($oldPath, $newPath)) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to rename file"]);
    exit;
}

echo json_encode(["success" => true, "oldName" => $oldName, "newName" => $newName]);