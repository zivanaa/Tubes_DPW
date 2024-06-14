<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

// Periksa koneksi
if (mysqli_connect_errno()) {
    http_response_code(500);
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}

// Periksa apakah user telah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Anda harus login terlebih dahulu.";
    exit();
}

// Ambil user_id dari sesi
$user_id = $_SESSION['user_id'];

// Periksa apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tweet_text = mysqli_real_escape_string($koneksi, $_POST['tweet-text']);
    $image_name = "";

    // Proses upload gambar jika ada
    if (isset($_FILES['upload-image']) && $_FILES['upload-image']['error'] == 0) {
        $target_dir = "../assets/konten/"; // Direktori untuk menyimpan gambar yang diunggah
        $image_name = basename($_FILES["upload-image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Periksa apakah file adalah gambar
        $check = getimagesize($_FILES["upload-image"]["tmp_name"]);
        if ($check === false) {
            http_response_code(400);
            echo "File bukan gambar.";
            exit();
        }

        // Periksa apakah file sudah ada
        if (file_exists($target_file)) {
            http_response_code(400);
            echo "File sudah ada.";
            exit();
        }

        // Batasi ukuran file
        if ($_FILES["upload-image"]["size"] > 5000000) {
            http_response_code(400);
            echo "Ukuran file terlalu besar.";
            exit();
        }

        // Hanya izinkan format file tertentu
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            http_response_code(400);
            echo "Hanya file JPG, JPEG, PNG, & GIF yang diperbolehkan.";
            exit();
        }

        // Jika semua pemeriksaan berhasil, pindahkan file ke direktori tujuan
        if (!move_uploaded_file($_FILES["upload-image"]["tmp_name"], $target_file)) {
            http_response_code(500);
            echo "Ada kesalahan saat mengunggah file.";
            exit();
        }
    }

    // Simpan data ke database
    $query = "INSERT INTO posts (user_id, content, image, likes, dislikes, comments_count, created_at) VALUES ('$user_id', '$tweet_text', '$image_name', 0, 0, 0, NOW())";

    if (mysqli_query($koneksi, $query)) {
        http_response_code(200);
        echo "Konten berhasil diunggah!";
    } else {
        http_response_code(500);
        echo "Error: " . mysqli_error($koneksi);
    }

    // Tutup koneksi database
    mysqli_close($koneksi);
}
?>
