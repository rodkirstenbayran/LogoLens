<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Logo Metadata</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h3>Upload Logo Metadata</h3>
    <form action="upload_logo.php" method="POST" enctype="multipart/form-data">
        <label for="description">Logo Description:</label>
        <textarea id="description" name="description" placeholder="Enter a description for the logo" required></textarea>

        <label for="source_file">Upload a Logo Image (JPG, PNG, max 2MB):</label>
        <input id="source_file" type="file" name="source_file" accept=".jpg,.jpeg,.png" required>

        <button type="submit" class="btn">Upload</button>
    </form>
</div>
</body>
</html>