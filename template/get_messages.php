<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

include "db_connect.php"; // File untuk koneksi ke database

$user_id = $_SESSION['user_id'];
$contact_id = isset($_POST['contact_id']) ? (int)$_POST['contact_id'] : null;

if (!$contact_id) {
    echo json_encode(['success' => false, 'message' => 'Contact ID missing']);
    exit();
}

$messages_query = "SELECT sender_id, content, timestamp FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
$stmt = $conn->prepare($messages_query);
$stmt->bind_param("iiii", $user_id, $contact_id, $contact_id, $user_id);
$stmt->execute();
$messages_result = $stmt->get_result();

$messages = [];
while ($row = $messages_result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode(['success' => true, 'messages' => $messages]);
?>
