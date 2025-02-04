<?php
require_once 'db.php';
session_start();

define('UPLOAD_DIR', 'uploads/'); // Ensure this folder exists
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];

        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            die("File is too large. Maximum size allowed is 2MB.");
        }

        // Validate file type
        if (!in_array($file['type'], ALLOWED_TYPES)) {
            die("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
        }

        // Ensure upload directory exists
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }

        // Generate a unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = UPLOAD_DIR . $filename;
        // Check if the file exists before moving it
if (file_exists($file['tmp_name'])) {
    echo "File exists in temp directory: " . $file['tmp_name'];
} else {
    die("File doesn't exist in the temp directory.");
}
// Check if the destination folder is writable
if (is_writable(UPLOAD_DIR)) {
    echo "Uploads folder is writable.";
} else {
    die("Uploads folder is not writable.");
}


        // Move the file to the uploads directory
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Update the database
            try {
                $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->execute([$filename, $_SESSION['user_id']]); // Save only filename, not full path
                header('Location: dashboard.php');
                exit();
            } catch (PDOException $e) {
                die("Error updating profile picture: " . $e->getMessage());
            }
        } else {
            die("Error moving uploaded file.");
        }
    } else {
        die("No file uploaded or there was an error: " . $_FILES['profile_picture']['error']);
    }
}
?>