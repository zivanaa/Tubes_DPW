<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

if (mysqli_connect_errno()) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $post_id = $_POST['post_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already performed the action
    $check_query = "SELECT * FROM post_actions WHERE post_id = $post_id AND user_id = $user_id";
    $result = mysqli_query($koneksi, $check_query);
    $existing_action = mysqli_fetch_assoc($result);

    if ($action == 'like' || $action == 'dislike') {
        if ($existing_action) {
            if ($existing_action['action_type'] == $action) {
                // Remove like or dislike
                $delete_query = "DELETE FROM post_actions WHERE id = " . $existing_action['id'];
                mysqli_query($koneksi, $delete_query);
                $update_query = $action == 'like' ? "UPDATE posts SET likes = likes - 1 WHERE id = $post_id" : "UPDATE posts SET dislikes = dislikes - 1 WHERE id = $post_id";
                mysqli_query($koneksi, $update_query);
            } else {
                // Switch from like to dislike or vice versa
                $update_action_query = "UPDATE post_actions SET action_type = '$action' WHERE id = " . $existing_action['id'];
                mysqli_query($koneksi, $update_action_query);
                $update_query = $action == 'like' ? "UPDATE posts SET likes = likes + 1, dislikes = dislikes - 1 WHERE id = $post_id" : "UPDATE posts SET dislikes = dislikes + 1, likes = likes - 1 WHERE id = $post_id";
                mysqli_query($koneksi, $update_query);
            }
        } else {
            // Add new like or dislike
            $insert_query = "INSERT INTO post_actions (post_id, user_id, action_type) VALUES ($post_id, $user_id, '$action')";
            mysqli_query($koneksi, $insert_query);
            $update_query = $action == 'like' ? "UPDATE posts SET likes = likes + 1 WHERE id = $post_id" : "UPDATE posts SET dislikes = dislikes + 1 WHERE id = $post_id";
            mysqli_query($koneksi, $update_query);
        }

        // Fetch updated counts
        $count_query = "SELECT 
                            (SELECT COUNT(*) FROM post_actions WHERE post_id = $post_id AND action_type = 'like') AS likes,
                            (SELECT COUNT(*) FROM post_actions WHERE post_id = $post_id AND action_type = 'dislike') AS dislikes
                        FROM posts LIMIT 1";
        $count_result = mysqli_query($koneksi, $count_query);
        $counts = mysqli_fetch_assoc($count_result);

        echo json_encode(['status' => 'success', 'likes' => $counts['likes'], 'dislikes' => $counts['dislikes']]);
    } elseif ($action == 'repost') {
        if ($existing_action && $existing_action['action_type'] == 'repost') {
            // Remove repost
            $delete_query = "DELETE FROM post_actions WHERE id = " . $existing_action['id'];
            mysqli_query($koneksi, $delete_query);
            $delete_post_query = "DELETE FROM posts WHERE original_post_id = $post_id AND user_id = $user_id";
            mysqli_query($koneksi, $delete_post_query);
        } else {
            // Add new repost
            $insert_query = "INSERT INTO post_actions (post_id, user_id, action_type) VALUES ($post_id, $user_id, 'repost')";
            mysqli_query($koneksi, $insert_query);
            $repost_query = "INSERT INTO posts (user_id, content, image, likes, dislikes, comments_count, created_at, original_post_id) 
                             SELECT $user_id, content, image, 0, 0, 0, NOW(), id FROM posts WHERE id = $post_id";
            mysqli_query($koneksi, $repost_query);
        }

        // Fetch updated repost count
        $repost_count_query = "SELECT COUNT(*) AS reposts FROM post_actions WHERE post_id = $post_id AND action_type = 'repost'";
        $repost_count_result = mysqli_query($koneksi, $repost_count_query);
        $repost_count = mysqli_fetch_assoc($repost_count_result);

        echo json_encode(['status' => 'success', 'reposts' => $repost_count['reposts']]);
    }
}
?>
