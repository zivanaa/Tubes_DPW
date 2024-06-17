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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment_text = mysqli_real_escape_string($koneksi, $_POST['comment']);
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES ('$post_id', '$user_id', '$comment_text', NOW())";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Komentar berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Fetch comments
$post_id = $_GET['post_id'];
$query = "SELECT c.*, u.name, u.profile_image FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = $post_id ORDER BY c.created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <form method="post">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <div class="mb-3">
                    <label for="comment" class="form-label">Tambah Komentar</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </form>
            <hr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="comment" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white; margin-bottom: 15px;">
                    <div class="d-flex">
                        <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'assets/profile/none.png' ?>" alt="User Image" style="width: 50px; height: 50px;">
                        <div class="ms-3">
                            <strong><?= htmlspecialchars($row['name']) ?></strong> <small><?= time_ago($row['created_at']) ?></small>
                            <p><?= htmlspecialchars($row['comment']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
