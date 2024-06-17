<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ?mod=login');
    exit();
}

include "db_connect.php"; // File untuk koneksi ke database

$user_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : null;
$message_content = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($receiver_id && !empty($message_content)) {
    $insert_query = "INSERT INTO messages (sender_id, receiver_id, content, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_query);
    
    if ($stmt) {
        $stmt->bind_param("iis", $user_id, $receiver_id, $message_content);
        if ($stmt->execute()) {
            // Message sent successfully
            $stmt->close();
            $conn->close();
            echo json_encode(['success' => true]);
            exit();
        } else {
            // Handle error during execution
            error_log("Error during statement execution: " . $stmt->error);
            $stmt->close();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            exit();
        }
    } else {
        // Handle error during statement preparation
        error_log("Error during statement preparation: " . $conn->error);
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Statement preparation error']);
        exit();
    }
} else {
    // Handle missing receiver_id or message content
    echo json_encode(['success' => false, 'message' => 'Receiver ID or message content missing']);
    exit();
}
?>
