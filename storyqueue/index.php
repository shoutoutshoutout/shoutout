<?php
$dir = __DIR__;
$files = array_diff(scandir($dir), array('.', '..', 'index.php'));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>StoryQueue Uploads</title>
</head>
<body>
<h2>Uploaded Images</h2>
<ul>
<?php
foreach ($files as $file) {
    // Only show image files
    if (preg_match('/\.(jpe?g|png)$/i', $file)) {
        echo '<li><a href="' . htmlspecialchars($file) . '" target="_blank">' . htmlspecialchars($file) . '</a></li>';
    }
}
?>
</ul>
</body>
</html>