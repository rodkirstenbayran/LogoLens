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
<div class="navbar">
            <a href="view_logos.php">
                <img src="cover/logo.png" alt="Logo">
            </a>
                <div class="dropdown">
                    <button>User</button>
                    <div class="dropdown-content">
                        <a href="change_password.php">Change Password</a>
                         <a href="logout.php">Logout</a>
                    </div>
                </div>
        </div>
            <a href="upload_logo_form.php">
            <img src="cover/upload.png" alt="Upload" class="upload_btn">
            </a>
<div class="container">
    <h3>Upload Logo Metadata</h3>
    <form action="upload_logo.php" method="POST" enctype="multipart/form-data">
        <label for="description">Logo Description:</label><br>
        <textarea id="description" name="description" placeholder="Enter a description for the logo" required></textarea><br><br>

        <label for="source_file">Upload a Logo Image (JPG, PNG, max 2MB):</label>
        <input id="source_file" type="file" name="source_file" accept=".jpg,.jpeg,.png" required><br>

        <button type="submit" class="btn_submit">Upload</button>
    </form>
</div>
</body>
</html>