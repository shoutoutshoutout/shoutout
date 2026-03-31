<?php
header('Content-Type: application/json');

// Path to images (same folder)
$uploadDir = __DIR__ . '/';

// Scan folder
$files = array_values(array_filter(scandir($uploadDir), function($f) {
    return preg_match('/\.(jpe?g|png)$/i', $f);
}));

echo json_encode(['files' => $files]);