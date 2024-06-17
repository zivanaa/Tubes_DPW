<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

include "db_connect.php";

$user_id = $_SESSION['user_id'];
$contact_id = isset($_GET['contact_id']) ? (int)$_GET['contact_id'] : null;

if ($contact_id) {
    $messages_query = "SELECT sender_id, content, timestamp FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
    $stmt = $conn->prepare($messages_query);
    // Bind parameters to the query
    $stmt->bind_param("iiii", $user_id, $contact_id, $contact_id, $user_id);
    // Execute the query
    if ($stmt->execute()) {
        // Get the result set
        $messages_result = $stmt->get_result();

        $messages = [];
        while ($row = $messages_result->fetch_assoc()) {
            $messages[] = $row;
        }
        // Encode messages array to JSON and send it as response
        echo json_encode(['messages' => $messages]);
    } else {
        // Handle execution failure
        echo json_encode(['error' => 'Failed to execute query']);
    }
} else {
    echo json_encode(['error' => 'No contact selected']);
}
?>
