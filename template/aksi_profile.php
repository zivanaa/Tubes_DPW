<?php


// Koneksi ke database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_itsave';

$koneksi = mysqli_connect($host, $username, $password, $database);

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Mendapatkan informasi pengguna dari database
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$row = mysqli_fetch_assoc($result);

// Ambil informasi profil pengguna
$user_name = $row['username'];
$user_profile_image = $row['profile_image']; // Kolom di tabel yang menyimpan path gambar profil

// Fungsi untuk mendapatkan gambar profil dari database atau penyimpanan lainnya
function getUserProfileImage($user_id, $koneksi) {
    $query = "SELECT profile_image FROM users WHERE id = $user_id";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        return "default_profile_image.jpg"; // Default jika tidak ada gambar profil
    }

    $row = mysqli_fetch_assoc($result);
    return $row['profile_image'];
}

// Mendapatkan gambar profil pengguna dari fungsi
$user_profile_image = getUserProfileImage($user_id, $koneksi);

// Tutup koneksi database
mysqli_close($koneksi);
?>
