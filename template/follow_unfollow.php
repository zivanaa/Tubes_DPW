<?php
session_start(); // Start the session at the beginning of the file

// Database connection
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

if (mysqli_connect_errno()) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . mysqli_connect_error()]);
    exit();
}

// Check if the follow/unfollow request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['user_id']) && isset($_POST['follower_id'])) {
    $user_id = intval($_POST['user_id']);
    $follower_id = intval($_POST['follower_id']);
    $action = $_POST['action'];

    if ($action == 'follow') {
        $sql = "INSERT INTO followers (user_id, follower_id) VALUES ($user_id, $follower_id)";
    } elseif ($action == 'unfollow') {
        $sql = "DELETE FROM followers WHERE user_id = $user_id AND follower_id = $follower_id";
    } else {
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        exit();
    }

    if (mysqli_query($koneksi, $sql)) {
        echo json_encode(['success' => true, 'message' => $action == 'follow' ? 'Berhasil mengikuti.' : 'Berhasil berhenti mengikuti.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kesalahan query: ' . mysqli_error($koneksi)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
}
?>
