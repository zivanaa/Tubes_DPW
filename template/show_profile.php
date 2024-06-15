<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "header.php";

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

if (mysqli_connect_errno()) {
    echo "<script>alert('Koneksi database gagal: " . mysqli_connect_error() . "');</script>";
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
    }
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch user details
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($koneksi, $query);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo "<script>alert('User tidak ditemukan'); window.location.href='home.php';</script>";
        exit();
    }

    // Fetch user's posts
    $posts_query = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
    $posts_result = mysqli_query($koneksi, $posts_query);
} else {
    echo "<script>alert('User ID tidak ditemukan'); window.location.href='home.php';</script>";
    exit();
}
?>

<style>
  .dashboard {
            background-color: #BBD4E0;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            text-align: left;
  }
  .profile-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .profile-stats div {
            text-align: center;
        }
        .profile-stats div span {
            display: block;
            font-size: 18px;
            font-weight: bold;
        }
  .avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
  }
</style>

<div class="profile">
    <div class="user-info">
        <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'assets/profile/none.png' ?>" alt="Avatar" class="avatar">
        <div>
            <div class="profile-stats">
                <div>
                    <span>45</span>
                    Posts
                </div>
                <div>
                    <span>668</span>
                    Followers
                </div>
                <div>
                    <span>408</span>
                    Following
                </div>
            </div>
            <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left : 110px" >Follow</button>
            <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left : 100px">Message</button>
            <h4><?= htmlspecialchars($user['name']) ?></h4>
            <p><?= htmlspecialchars($user['username']) ?></p>
            <div class="profile-bio">
                <p><?= htmlspecialchars($user['bio']) ?></p>
                <a href="#">See Translation</a>
            </div>
            <div class="dashboard">
                <p style="color: #0C0C0C">Professional dashboard</p>
                <div class="profile-links">
                    <a href="#">instagram.com/o8.25am?igshid=MzRlODBiN...</a>
                </div>
            </div>
        </div>
        <br>
    </div>
    <div style="clear: both;"></div>
    <h3>Posts:</h3>        
</div>

<div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
            <div class="post mb-3" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white;">
                <div class="d-flex">
                    <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                    <div class="ms-3">
                        <h5 class="mb-0"><?= htmlspecialchars($user['name']) ?></h5>
                        <small style="color : #fff"><?= htmlspecialchars($user['username']) ?></small>
                    </div>
                </div>
                <p class="mt-3"><?= htmlspecialchars($post['content']) ?></p>
                <?php foreach (explode(",", $post['image']) as $image): ?>
                    <img src="assets/konten/<?= htmlspecialchars($image) ?>" alt="Post Image" class="img-fluid">
                <?php endforeach; ?>
            
                    <div class="d-flex justify-content-between" style="color: white;">
                        <div class="post-actions">
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="action" value="like">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">Like (<?= $post['likes'] ?>)</button>
                            </form>
                            |
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="action" value="dislike">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">Dislike (<?= $post['dislikes'] ?>)</button>
                            </form>
                            |
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="action" value="repost">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">Repost (<?= $post['reposts'] ?>)</button>
                            </form>
                            |
                            <a href="?mod=detail_post&post_id=<?= $post['id'] ?>">Comments (<?= $post['comments_count'] ?>)</a>
                            </form>
                        </div>
                    </div>
                </div>
                

            <?php endwhile; ?>
        </div>
    </div>
</div>
<br>

<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
      <a class="page-link">Previous</a>
    </li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item">
      <a class="page-link" href="#">Next</a>
    </li>
  </ul>
</nav> 

<?php include "footer.php"; ?>
