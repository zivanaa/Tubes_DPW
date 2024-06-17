<?php
// Koneksi ke database (pastikan Anda sudah terhubung seperti yang telah Anda lakukan di header.php)
include 'header.php';

// Cek apakah query parameter sort tersedia dan sesuai
if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
    if ($sort === 'terbaru') {
        $query = "SELECT * FROM posts ORDER BY created_at DESC";
    } elseif ($sort === 'teratas') {
        // Misalnya, menghitung jumlah likes dalam 24 jam terakhir
        $query = "SELECT posts.*, COUNT(likes.post_id) AS total_likes 
                  FROM posts 
                  LEFT JOIN likes ON posts.id = likes.post_id 
                  WHERE likes.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) 
                  GROUP BY posts.id 
                  ORDER BY total_likes DESC";
    } else {
        // Default jika tidak sesuai
        $query = "SELECT * FROM posts";
    }
} else {
    // Default jika tidak ada parameter sort
    $query = "SELECT * FROM posts";
}

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Proses hasil query sesuai kebutuhan Anda
while ($row = mysqli_fetch_assoc($result)) {
    // Tampilkan postingan sesuai dengan pengurutan yang telah diatur
    // Misalnya, tampilkan judul, konten, dll.
    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
    echo "<p>" . htmlspecialchars($row['content']) . "</p>";
    // Tambahan sesuai kebutuhan
}

// Tutup koneksi database
mysqli_close($koneksi);
?>
