<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

// Periksa koneksi
if (mysqli_connect_errno()) {
    http_response_code(500);
    exit("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil data dari permintaan POST
$postId = $_POST['postId'];
$reactionType = $_POST['reactionType']; // 'like', 'dislike', 'repost', dll.

// Query untuk update jumlah reaction
$query = "";
switch ($reactionType) {
    case 'like':
        $query = "UPDATE posts SET likes = likes + 1 WHERE id = $postId";
        break;
    case 'dislike':
        $query = "UPDATE posts SET dislikes = dislikes + 1 WHERE id = $postId";
        break;
    case 'repost':
        $query = "UPDATE posts SET reposts = reposts + 1 WHERE id = $postId";
        break;
    default:
        http_response_code(400);
        exit("Tipe reaction tidak valid");
}

// Eksekusi query
if (mysqli_query($koneksi, $query)) {
    // Response sukses (status 200)
    http_response_code(200);
    // Tidak perlu mengirim kembali pesan sukses, cukup status 200
} else {
    // Response error
    http_response_code(500);
    exit("Gagal memperbarui data: " . mysqli_error($koneksi));
}

// Tutup koneksi
mysqli_close($koneksi);
?>
