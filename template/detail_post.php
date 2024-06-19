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

$post_id = isset($_GET['post_id']) ? (int) $_GET['post_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = mysqli_real_escape_string($koneksi, $_POST['comment']);
    $user_id = (int) $_SESSION['user_id'];

    if (!empty($comment)) {
        $insert_comment_query = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($koneksi, $insert_comment_query);
        mysqli_stmt_bind_param($stmt, 'iis', $post_id, $user_id, $comment);
        mysqli_stmt_execute($stmt);
    }
}

// Fetch post details
$query = "SELECT p.*, u.name, u.profile_image, u.username,
                 (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'like') AS likes,
                 (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'dislike') AS dislikes,
                 (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'repost') AS reposts,
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments_count,
                 (SELECT action_type FROM post_actions WHERE post_id = p.id AND user_id = " . $_SESSION['user_id'] . " LIMIT 1) AS user_action
          FROM posts p
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = ?";
          
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<script>alert('Postingan tidak ditemukan'); window.location.href='home.php';</script>";
    exit();
}

// Fetch comments
$comments_query = "SELECT c.*, u.name, u.profile_image, u.username
                   FROM comments c
                   JOIN users u ON c.user_id = u.id
                   WHERE c.post_id = ?
                   ORDER BY c.created_at DESC";
$stmt = mysqli_prepare($koneksi, $comments_query);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$comments_result = mysqli_stmt_get_result($stmt);
?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


<div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        <div class="col-md-8 offset-md-2">
            <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white; margin-bottom: 15px;">
                <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                    <a href="?mod=profile" style="color: white; text-decoration: none;">
                <?php else: ?>
                    <a href="?mod=show_profile&user_id=<?= htmlspecialchars($post['user_id']) ?>" style="color: white; text-decoration: none;">
                <?php endif; ?>
                <div class="d-flex">
                        <img src="<?= !empty($post['profile_image']) ? htmlspecialchars($post['profile_image']) : 'assets/profile/none.png' ?>" class="rounded-circle" alt="User Image" style="width: 50px; height: 50px;">
                        <div class="ms-3">
                            <strong class="mb-0"><?= htmlspecialchars($post['name']) ?></strong>
                            <br>
                            <h7 style="color: #fff"><?= htmlspecialchars($post['username']) ?></h7>
                        </div>
                    </div>
                </a>
                <p class="mt-3"><?= htmlspecialchars($post['content']) ?></p>
                
                <?php if (!empty($post['image'])): ?>
                <div class="horizontal-scroll">
                    <?php foreach (explode(",", $post['image']) as $image): ?>
                        <a href="#" class="open-modal" data-toggle="modal" data-target="#imageModal<?= $post['id'] ?>">
                            <img src="assets/konten/<?= htmlspecialchars($image) ?>" alt="Post Image" class="horizontal-image">
                        </a>
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
                <?php endif; ?>
                
                <div class="d-flex justify-content-between" style="color: white;">
                    <div class="d-flex justify-content-between" style="color: white;">
                        <div class="post-actions">
                            <form method="post" style="display: inline; margin-left: 5px; margin-right: 5px">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="action" value="like">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">
                                    <?php if ($post['user_action'] == 'like'): ?>
                                        <i class="fas fa-thumbs-up"></i>
                                    <?php else: ?>
                                        <i class="far fa-thumbs-up"></i>
                                    <?php endif; ?>
                                    (<?= $post['likes'] ?>)
                                </button>
                            </form>
                            |
                            <form method="post" style="display: inline; margin-left: 5px; margin-right: 5px">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="action" value="dislike">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">
                                    <?php if ($post['user_action'] == 'dislike'): ?>
                                        <i class="fas fa-thumbs-down"></i>
                                    <?php else: ?>
                                        <i class="far fa-thumbs-down"></i>
                                    <?php endif; ?>
                                    (<?= $post['dislikes'] ?>)
                                </button>
                            </form>
                            |            
                            <form method="post" style="display: inline; margin-left: 5px; margin-right: 5px">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="action" value="repost">
                                <button type="submit" class="btn btn-link" style="color: white; text-decoration: none;">
                                    <?php if ($post['user_action'] == 'repost'): ?>
                                        <i class="fas fa-retweet"></i>
                                    <?php else: ?>
                                        <i class="fas fa-retweet"></i>
                                    <?php endif; ?>
                                    (<?= $post['reposts'] ?>)
                                </button>
                            </form>
                            |
                            <a style="display: inline; margin-left: 5px; margin-right: 5px" href="?mod=detail_post&post_id=<?= $post['id'] ?>"><i class="far fa-comments"></i> (<?= $post['comments_count'] ?>)</a>
                       
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="mt-5" style="color: black;">Comments</h4>
<?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
    <div class="comment mb-3" style="border: 1px solid #ddd; padding: 10px; border-radius: 5px; background-color: #11174F; color: white;">
        <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
            <a href="?mod=profile" style="color: white; text-decoration: none;">
        <?php else: ?>
            <a href="?mod=show_profile&user_id=<?= htmlspecialchars($comment['user_id']) ?>" style="color: white; text-decoration: none;">
        <?php endif; ?>
            <div class="d-flex">
                <img src="<?= !empty($comment['profile_image']) ? htmlspecialchars($comment['profile_image']) : 'assets/profile/none.png' ?>" class="rounded-circle" alt="User Image" style="width: 40px; height: 40px;">
                <div class="ms-3">
                    <strong class="mb-0"><?= htmlspecialchars($comment['name']) ?></strong>
                    <br>
                    <h7 style="color: #fff"><?= htmlspecialchars($comment['username']) ?></h7>
                </div>
            </div>
            <p class="mt-3"><?= htmlspecialchars($comment['comment']) ?></p>
            <span class="text-muted" style="font-size: 0.8em;"><?= htmlspecialchars($comment['created_at']) ?></span>
        </a>
    </div>
<?php endwhile; ?>

            <form method="post" class="mt-4">
                <div class="form-group">
                    <input name="comment" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Submit</button>
            </form>
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
