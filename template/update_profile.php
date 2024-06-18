<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: template/login.php');
    exit();
}

$currentUsername = $_SESSION['username'];

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "db_itsave");

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Update data profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['profileName'];
    $bio = $_POST['profileBio'];
    $dashboard = $_POST['profileDashboard'];
    $newUsername = $_POST['profileUsername'];

    // Periksa apakah username baru sudah digunakan
    $checkUsernameSql = "SELECT username FROM users WHERE username = ? AND username != ?";
    $checkUsernameStmt = $koneksi->prepare($checkUsernameSql);
    $checkUsernameStmt->bind_param("ss", $newUsername, $currentUsername);
    $checkUsernameStmt->execute();
    $checkUsernameResult = $checkUsernameStmt->get_result();

    if ($checkUsernameResult->num_rows > 0) {
        die("Username sudah digunakan.");
    }

    // Handle upload gambar profil
    $relativeImagePath = ''; // Inisialisasi path gambar relatif
    $updateImage = false; // Flag untuk menentukan apakah gambar diupdate

    if (!empty($_FILES['profileImageUpload']['name'])) {
        $uploadsDirectory = __DIR__ . '/../assets/profile/';
        $filename = basename($_FILES['profileImageUpload']['name']);
        $profileImage = $uploadsDirectory . $filename;
        $relativeImagePath = 'assets/profile/' . $filename;

        // Validasi tipe file dan ukuran (misalnya, hanya izinkan gambar dan maksimal 2MB)
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profileImageUpload']['tmp_name']);
        $fileSize = $_FILES['profileImageUpload']['size'];

        if (!in_array($fileType, $allowedMimeTypes) || $fileSize > 2 * 1024 * 1024) {
            die("File tidak valid. Hanya gambar dengan ukuran maksimal 2MB yang diperbolehkan.");
        }

        if (!move_uploaded_file($_FILES['profileImageUpload']['tmp_name'], $profileImage)) {
            die("Gagal mengunggah file.");
        }

        $updateImage = true; // Set flag bahwa gambar diupdate
    }

    // Ambil path gambar profil saat ini dari database
    $getProfileImageSql = "SELECT profile_image FROM users WHERE username = ?";
    $getProfileImageStmt = $koneksi->prepare($getProfileImageSql);
    $getProfileImageStmt->bind_param("s", $currentUsername);
    $getProfileImageStmt->execute();
    $getProfileImageResult = $getProfileImageStmt->get_result();

    if ($getProfileImageResult->num_rows > 0) {
        $row = $getProfileImageResult->fetch_assoc();
        $currentProfileImage = $row['profile_image'];

        // Jika tidak ada perubahan gambar, gunakan gambar profil saat ini
        if (!$updateImage) {
            $relativeImagePath = $currentProfileImage;
        }
    }

    // Update profil pengguna
    $updateSql = "UPDATE users SET name = ?, bio = ?, dashboard = ?, profile_image = ?, username = ? WHERE username = ?";
    $updateStmt = $koneksi->prepare($updateSql);

    if ($updateStmt === false) {
        die("Error preparing update statement: " . $koneksi->error);
    }

    $updateStmt->bind_param("ssssss", $name, $bio, $dashboard, $relativeImagePath, $newUsername, $currentUsername);

    if ($updateStmt->execute()) {
        // Perbarui data sesi dan array pengguna
        $_SESSION['username'] = $newUsername;

        // Set pesan sukses
        $_SESSION['update_message'] = "Profil berhasil diperbarui.";

        // Redirect ke halaman profil pengguna
        header("Location: ?mod=profile");
        exit();
    } else {
        die("Error updating profile: " . $updateStmt->error);
    }

    $updateStmt->close();
}
?>
