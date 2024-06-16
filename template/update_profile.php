<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: template/login.php');
    exit();
}

$currentUsername = $_SESSION['username'];

// Database connection
$koneksi = new mysqli("localhost", "root", "", "db_itsave");

// Check connection
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Update profile data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['profileName'];
    $bio = $_POST['profileBio'];
    $dashboard = $_POST['profileDashboard'];
    $newUsername = $_POST['profileUsername'];

    // Check if new username is already taken
    $checkUsernameSql = "SELECT username FROM users WHERE username = ? AND username != ?";
    $checkUsernameStmt = $koneksi->prepare($checkUsernameSql);
    $checkUsernameStmt->bind_param("ss", $newUsername, $currentUsername);
    $checkUsernameStmt->execute();
    $checkUsernameResult = $checkUsernameStmt->get_result();

    if ($checkUsernameResult->num_rows > 0) {
        die("Username sudah digunakan.");
    }

    // Handle profile image upload
    $relativeImagePath = ''; // Initialize relative image path
    if (!empty($_FILES['profileImageUpload']['name'])) {
        $uploadsDirectory = __DIR__ . '/../assets/profile/';
        $filename = basename($_FILES['profileImageUpload']['name']);
        $profileImage = $uploadsDirectory . $filename;
        $relativeImagePath = 'assets/profile/' . $filename;

        // Validate file type and size (e.g., only allow images and max 2MB)
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profileImageUpload']['tmp_name']);
        $fileSize = $_FILES['profileImageUpload']['size'];
        if (!in_array($fileType, $allowedMimeTypes) || $fileSize > 2 * 1024 * 1024) {
            die("File tidak valid. Hanya gambar dengan ukuran maksimal 2MB yang diperbolehkan.");
        }

        if (!move_uploaded_file($_FILES['profileImageUpload']['tmp_name'], $profileImage)) {
            die("Gagal mengunggah file.");
        }
    }

    // Update user profile
    $updateSql = "UPDATE users SET name = ?, bio = ?, dashboard = ?, profile_image = ?, username = ? WHERE username = ?";
    $updateStmt = $koneksi->prepare($updateSql);

    if ($updateStmt === false) {
        die("Error preparing update statement: " . $koneksi->error);
    }

    $updateStmt->bind_param("ssssss", $name, $bio, $dashboard, $relativeImagePath, $newUsername, $currentUsername);

    if ($updateStmt->execute()) {
        // Update session data and user array
        $_SESSION['username'] = $newUsername;

        // Set success message
        $_SESSION['update_message'] = "Profil berhasil diperbarui.";

        // Redirect to user profile page
        header("Location: ?mod=profile");
        exit();
    } else {
        die("Error updating profile: " . $updateStmt->error);
    }

    $updateStmt->close();
}
?>
