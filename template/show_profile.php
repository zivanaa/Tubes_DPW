<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_user_id = $_SESSION['user_id']; // Define the logged in user ID

include "header.php";

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

if (mysqli_connect_errno()) {
    echo "<script>alert('Koneksi database gagal: " . mysqli_connect_error() . "');</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['follow_action'])) {
    $action_user_id = $_POST['action_user_id'];
    $follow_action = $_POST['follow_action'];

    if ($follow_action == 'follow') {
        $query = "INSERT INTO followers (follower_id, user_id) VALUES ($logged_in_user_id, $action_user_id)";
        mysqli_query($koneksi, $query);
    } elseif ($follow_action == 'unfollow') {
        $query = "DELETE FROM followers WHERE follower_id = $logged_in_user_id AND user_id = $action_user_id";
        mysqli_query($koneksi, $query);
    }
}

// Fetch all users except the current user
$query = "SELECT u.id, u.name, u.username, u.profile_image,
                 (SELECT COUNT(*) FROM followers WHERE follower_id = $logged_in_user_id AND user_id = u.id) AS is_following
          FROM users u
          WHERE u.id != $logged_in_user_id";
$result = mysqli_query($koneksi, $query);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $post_id = $_POST['post_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already performed the action
    $check_query = "SELECT * FROM post_actions WHERE post_id = $post_id AND user_id = $user_id AND action_type != 'repost'";
    $result = mysqli_query($koneksi, $check_query);
    $existing_action = mysqli_fetch_assoc($result);

    if ($action == 'like' || $action == 'dislike') {
        if ($existing_action) {
            // User has already performed an action
            if ($existing_action['action_type'] == $action) {
                // If user clicks the same action again, remove it (unlike or undislike)
                $delete_query = "DELETE FROM post_actions WHERE id = " . $existing_action['id'];
                mysqli_query($koneksi, $delete_query);
            } else {
                // If user switches from like to dislike or vice versa, update action type
                $update_action_query = "UPDATE post_actions SET action_type = '$action' WHERE id = " . $existing_action['id'];
                mysqli_query($koneksi, $update_action_query);
            }
        } else {
            // User performs a new like or dislike action
            $insert_query = "INSERT INTO post_actions (post_id, user_id, action_type) VALUES ($post_id, $user_id, '$action')";
            mysqli_query($koneksi, $insert_query);
        }
    } 
    // Separate logic for repost
    elseif ($action == 'repost') {
        // Check if the user has already reposted
        $check_repost_query = "SELECT * FROM post_actions WHERE post_id = $post_id AND user_id = $user_id AND action_type = 'repost'";
        $result_repost = mysqli_query($koneksi, $check_repost_query);
        $existing_repost = mysqli_fetch_assoc($result_repost);

        if ($existing_repost) {
            // User has already reposted, remove repost action (unrepost)
            $delete_query = "DELETE FROM post_actions WHERE id = " . $existing_repost['id'];
            mysqli_query($koneksi, $delete_query);
        } else {
            // User reposts the post
            $insert_query = "INSERT INTO post_actions (post_id, user_id, action_type) VALUES ($post_id, $user_id, 'repost')";
            mysqli_query($koneksi, $insert_query);
        }
    }
}

if (isset($_GET['user_id'])) {
    $profile_user_id = $_GET['user_id'];

    // Fetch user details
    $query = "SELECT * FROM users WHERE id = $profile_user_id";
    $result = mysqli_query($koneksi, $query);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo "<script>alert('User tidak ditemukan'); window.location.href='home.php';</script>";
        exit();
    }

    // Fetch user's posts
    $posts_query = "SELECT p.*, u.name, u.profile_image, u.username,
                       (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'like') AS likes,
                       (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'dislike') AS dislikes,
                       (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'repost') AS reposts,
                       (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments_count
                   FROM posts p 
                   JOIN users u ON p.user_id = u.id 
                   WHERE p.user_id = $profile_user_id
                   ORDER BY p.created_at DESC";

    $posts_result = mysqli_query($koneksi, $posts_query);
    $posts_count = mysqli_num_rows($posts_result); // Jumlah postingan


    // Fetch counts
    $posts_count_query = "SELECT COUNT(*) as post_count FROM posts WHERE user_id = $profile_user_id";
    $posts_count_result = mysqli_query($koneksi, $posts_count_query);
    $posts_count = mysqli_fetch_assoc($posts_count_result)['post_count'];

    $followers_count_query = "SELECT COUNT(*) as followers_count FROM followers WHERE user_id = $profile_user_id";
    $followers_count_result = mysqli_query($koneksi, $followers_count_query);
    $followers_count = mysqli_fetch_assoc($followers_count_result)['followers_count'];

    $following_count_query = "SELECT COUNT(*) as following_count FROM followers WHERE follower_id = $profile_user_id";
    $following_count_result = mysqli_query($koneksi, $following_count_query);
    $following_count = mysqli_fetch_assoc($following_count_result)['following_count'];
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
        <p style="text-align: center; font-size: 25px; background-color: #BBD4E0 ; color : #0C0C0C ; border-radius: 5px; padding: 5px; margin-bottom: 20px; "><?= htmlspecialchars($user['username']) ?></p>
        <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'assets/profile/none.png' ?>" alt="Avatar" class="avatar">
        <div>
            <div class="profile-stats">
                <div>
                    <span><?= $posts_count ?></span>
                    Posts
                </div>
                <div>
                    <span><?= $followers_count ?></span>
                    Followers
                </div>
                <div>
                    <span><?= $following_count ?></span>
                    Following
                </div>
            </div>
            <?php
            $is_following_query = "SELECT * FROM followers WHERE follower_id = $logged_in_user_id AND user_id = $profile_user_id";
            $is_following_result = mysqli_query($koneksi, $is_following_query);

            // Check if the query executed successfully
            if (!$is_following_result) {
                die('Query failed: ' . mysqli_error($koneksi));
            }

            $is_following = mysqli_num_rows($is_following_result) > 0;
            ?>

            <form method="post" style="display:inline;">
                <input type="hidden" name="action_user_id" value="<?= $profile_user_id ?>">
                <input type="hidden" name="follow_action" value="<?= $is_following ? 'unfollow' : 'follow' ?>">
                <button type="submit" style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left: 110px;">
                    <?= $is_following ? 'Unfollow' : 'Follow' ?>
                </button>
            </form>
            
                <a href="?mod=chat&user_id=<?= $user_id ?>">
                    <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left: 100px">
                        Message
                    </button>
                </a>
            <h4><?= htmlspecialchars($user['name']) ?></h4>
            <div class="profile-bio">
                <p><?= htmlspecialchars($user['bio']) ?></p>
            </div>
            <div class="dashboard">
                <p style="color: #0C0C0C ; font-size : 20px">Professional dashboard :</p>
                <div class="profile-links">
                    <p style="color: #0C0C0C "><?= htmlspecialchars($user['dashboard']); ?></p>
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
                    <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'assets/profile/none.png' ?>" alt="Avatar" class="avatar" class="rounded-circle" alt="User Image" style="width: 50px; height: 50px;">
                    <div class="ms-3">
                        <h5 class="mb-0"><?= htmlspecialchars($user['name']) ?></h5>
                                                <small style="color : #fff"><?= htmlspecialchars($user['username']) ?></small>
                    </div>
                </div>
                <p class="mt-3"><?= htmlspecialchars($post['content']) ?></p>
                    <div class="horizontal-scroll">
                        <?php foreach (explode(",", $post['image']) as $image): ?>
                            <!-- Tambahkan link untuk membuka modal -->
                            <a href="#" class="open-modal" data-toggle="modal" data-target="#imageModal<?= $post['id'] ?>">
                                <img src="assets/konten/<?= htmlspecialchars($image) ?>" alt="Post Image" class="horizontal-image">
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="imageModal<?= $post['id'] ?>" tabindex="-1" aria-labelledby="imageModalLabel<?= $post['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel<?= $post['id'] ?>">Gambar Postingan</h5>
                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="assets/konten/<?= htmlspecialchars($image) ?>" alt="Full Image" style="max-width: 100%; max-height: 80vh;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
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

<style>
.horizontal-scroll {
    overflow-x: auto;
    white-space: nowrap;
    margin-top: 10px;
    padding-bottom: 10px;
}

.horizontal-scroll::-webkit-scrollbar {
    display: none;
}

.horizontal-image {
    max-height: 250px;
    max-width: 75%;
    border-radius: 5px;
    margin-right: 10px;
    display: inline-block;
}
</style>

<?php include "footer.php"; ?>
