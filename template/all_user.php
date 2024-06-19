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

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['follow_action'])) {
    $action_user_id = $_POST['action_user_id'];
    $follow_action = $_POST['follow_action'];

    if ($follow_action == 'follow') {
        $query = "INSERT INTO followers (follower_id, user_id) VALUES ($user_id, $action_user_id)";
        mysqli_query($koneksi, $query);
    } elseif ($follow_action == 'unfollow') {
        $query = "DELETE FROM followers WHERE follower_id = $user_id AND user_id = $action_user_id";
        mysqli_query($koneksi, $query);
    }
}

// Fetch all users except the current user
$query = "SELECT u.id, u.name, u.username, u.profile_image,
                 (SELECT COUNT(*) FROM followers WHERE follower_id = $user_id AND user_id = u.id) AS is_following
          FROM users u
          WHERE u.id != $user_id";
$result = mysqli_query($koneksi, $query);
?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<style>
    .user-card {
        transition: transform 0.2s;
        margin-bottom: 20px;
    }
    .user-card:hover {
        transform: scale(1.05);
    }
    .profile-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
    .user-link {
        text-decoration: none;
        color: inherit;
    }
    /* Centering the container */
    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 20px;
    }

    
</style>

<div class="container">
    <div class="row mb-4 col-10 text-center"  >
        <h7 class="mb-2">Search for ItSafe User Here:</h7>
        <div class="input-group">
            <input type="text" id="search" class="form-control" placeholder="Search users...">
        </div>
    </div>

    <!-- <form method="get" action="page.php" class="mb-3">
                <input type="hidden" name="mod" value="home">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search posts" value="<?= htmlspecialchars($search_keyword) ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form> -->

    <div class="row col-12">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-12">
                <div class="card user-card">
                    <div class="card-body d-flex align-items-center">
                        <a href="?mod=show_profile&user_id=<?= htmlspecialchars($row['id']) ?>" class="user-link">
                            <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'assets/profile/none.png' ?>" class="rounded-circle mr-3 profile-image" alt="Profile Image">
                        </a>
                        <div>
                            <a href="?mod=show_profile&user_id=<?= htmlspecialchars($row['id']) ?>" class="user-link">
                                <h5 class="card-title mb-1"><?= htmlspecialchars($row['name']) ?></h5>
                                <h6 class="card-subtitle text-muted"><?= htmlspecialchars($row['username']) ?></h6>
                            </a>
                        </div>
                        <form method="post" class="ml-auto">
                            <input type="hidden" name="action_user_id" value="<?= $row['id'] ?>">
                            <?php if ($row['is_following']): ?>
                                <button type="submit" name="follow_action" value="unfollow" class="btn btn-danger">Unfollow</button>
                            <?php else: ?>
                                <button type="submit" name="follow_action" value="follow" class="btn btn-primary">Follow</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('search');

        searchInput.addEventListener('input', function() {
            var filter = searchInput.value.toLowerCase();
            var users = document.querySelectorAll('.user-card');

            users.forEach(function(user) {
                var userName = user.querySelector('.user-card .card-body .user-link .card-title').textContent.toLowerCase();
                var userUsername = user.querySelector('.user-card .card-body .user-link .card-subtitle').textContent.toLowerCase();
                var displayStyle = (userName.includes(filter) || userUsername.includes(filter)) ? 'block' : 'none';
            user.style.display = displayStyle;

            });
        });
    });
</script>

<?php include "footer.php"; ?>
