<?php
// Start session
session_start();

// Check if user is logged in
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

// Fetch posts from the database
$query = "SELECT p.*, u.name, u.profile_image, u.username,
                 (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'like') AS likes,
                 (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'dislike') AS dislikes,
                 (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'repost') AS reposts,
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments_count
          FROM posts p 
          JOIN users u ON p.user_id = u.id 
          ORDER BY p.created_at DESC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo "Error: " . mysqli_error($koneksi);
    exit();
}
?>


<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white; margin-bottom: 15px;">
                    <a href="?mod=show_profile&user_id=<?= htmlspecialchars($row['user_id']) ?>" style="color: white; text-decoration: none;">
                        <div class="d-flex">
                            <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'assets/profile/none.png' ?>" class="rounded-circle" alt="User Image" style="width: 50px; height: 50px;">
                            <div class="ms-3">
                                <strong class="mb-0"><?= htmlspecialchars($row['name']) ?></strong>
                                <br>
                                <h7 style="color: #fff"><?= htmlspecialchars($row['username']) ?></h7>
                            </div>
                        </div>
                    </a>
                    <p class="mt-3"><?= htmlspecialchars($row['content']) ?></p>
                    <div class="horizontal-scroll">
                        <?php foreach (explode(",", $row['image']) as $image): ?>
                            <!-- Tambahkan link untuk membuka modal -->
                            <a href="#" class="open-modal" data-toggle="modal" data-target="#imageModal<?= $row['id'] ?>">
                                <img src="assets/konten/<?= htmlspecialchars($image) ?>" alt="Post Image" class="horizontal-image">
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="imageModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="imageModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel<?= $row['id'] ?>">Gambar Postingan</h5>
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
                                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="action" value="like">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">Like (<?= $row['likes'] ?>)</button>
                            </form>
                            |
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="action" value="dislike">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">Dislike (<?= $row['dislikes'] ?>)</button>
                            </form>
                            |
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="action" value="repost">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">Repost (<?= $row['reposts'] ?>)</button>
                            </form>
                            |
                            <a href="?mod=detail_post&post_id=<?= $row['id'] ?>">Comments (<?= $row['comments_count'] ?>)</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

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
