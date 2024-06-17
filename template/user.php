<?php 
include "header.php";
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

if (mysqli_connect_errno()) {
    echo "<script>alert('Koneksi database gagal: " . mysqli_connect_error() . "');</script>";
    exit();
}

if (!isset($_GET['user_id'])) {
    echo "<script>alert('User ID tidak ditemukan.'); window.location.href='home.php';</script>";
    exit();
}

$user_id = $_GET['user_id'];

// Fetch user info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($koneksi, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if (!$user_data) {
    echo "<script>alert('User tidak ditemukan.'); window.location.href='home.php';</script>";
    exit();
}

// Determine if current user is following this profile
$current_user_id = 1; // Replace with your logic to get the current user ID
$is_following_query = "SELECT * FROM followers WHERE user_id = $user_id AND follower_id = $current_user_id";
$is_following_result = mysqli_query($koneksi, $is_following_query);
$is_following = mysqli_num_rows($is_following_result) > 0;

// Fetch user's posts
$posts_query = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$posts_result = mysqli_query($koneksi, $posts_query);
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
</style>

<div class="profile">

    <div class="user-info">
        <img src="<?= !empty($user_data['profile_image']) ? htmlspecialchars($user_data['profile_image']) : 'assets/profile/none.png' ?>" alt="Avatar" class="avatar">
        <div>
            <div class="profile-stats">
                <div>
                    <span>45</span>
                    Posts
                </div>
                <div class="follower-count">
                    <span>668</span>
                    Followers
                </div>
                <div>
                    <span>408</span>
                    Following
                </div>
            </div>
            <button id="follow-button" data-user-id="<?= $user_id ?>" data-follower-id="<?= $current_user_id ?>" class="<?= $is_following ? 'unfollow' : 'follow' ?>">
                <?= $is_following ? 'Unfollow' : 'Follow' ?>
            </button>
            <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left: 100px">Message</button>
            <h4><?= htmlspecialchars($user_data['name']) ?></h4>
            <p>@<?= htmlspecialchars($user_data['username']) ?></p>
            <div class="profile-bio">
                <p><?= htmlspecialchars($user_data['bio']) ?></p>
                <a href="#">See Translation</a>
            </div>
            <div class="dashboard">
                <p style="color: #0C0C0C">Professional dashboard</p>
                <div class="profile-links">
                    <a href="#">instagram.com/<?= htmlspecialchars($user_data['username']) ?>?igshid=MzRlODBiN...</a>
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

        <!-- Feed -->
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
                <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white; margin-bottom: 15px;">
                    <div class="d-flex">
                        <img src="<?= !empty($user_data['profile_image']) ? htmlspecialchars($user_data['profile_image']) : 'https://via.placeholder.com/50' ?>" class="rounded-circle" alt="User Image">
                        <div class="ms-3">
                            <h5 class="mb-0"><?= htmlspecialchars($user_data['name']) ?></h5>
                            <small style="color: #fff">@<?= htmlspecialchars($user_data['username']) ?></small>
                        </div>
                    </div>
                    <p class="mt-3"><?= htmlspecialchars($post['content']) ?></p>
                    <div class="d-flex justify-content-between" style="color: white;">
                        <a href="#" style="color: white;">
                            <div class="post" data-post-id="<?= $post['id'] ?>">
                                <div class="post-ratings-container">
                                    <div class="post-rating">
                                        <span class="post-rating-button material-icons">thumb_up</span>
                                        <span class="post-rating-count">0</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" style="color: white;">
                            <div class="post" data-post-id="<?= $post['id'] ?>">
                                <div class="post-ratings-container">
                                    <div class="post-rating">
                                        <span class="post-rating-button material-icons">thumb_down</span>
                                        <span class="post-rating-count">0</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" style="color: white;">
                            <img src="assets/img/gambar9.png" alt="Gambar 3" style="width: 20px; height: 30px;">
                            0
                        </a>
                        <a href="#" style="color: white;">
                            <img src="assets/img/gambar3.png" alt="Gambar 4" style="width: 20px; height: 20px;">
                            0
                        </a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    var followButton = document.getElementById('follow-button');
    if (followButton) { // Memastikan tombol follow ditemukan
        followButton.addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah perilaku default klik link

            var userId = this.getAttribute('data-user-id');
            var followerId = this.getAttribute('data-follower-id');
            var action = this.classList.contains('unfollow') ? 'unfollow' : 'follow';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'follow_unfollow.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert(response.message);

                        // Update jumlah pengikut di UI
                        var followerCountElement = document.querySelector('.profile-stats .follower-count span');
                        var currentFollowerCount = parseInt(followerCountElement.innerText);

                        if (action == 'follow') {
                            followerCountElement.innerText = currentFollowerCount + 1;
                            followButton.classList.remove('follow');
                            followButton.classList.add('unfollow');
                            followButton.innerText = 'Unfollow';
                        } else {
                            followerCountElement.innerText = currentFollowerCount - 1;
                            followButton.classList.remove('unfollow');
                            followButton.classList.add('follow');
                            followButton.innerText = 'Follow';
                        }
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send('user_id=' + userId + '&follower_id=' + followerId + '&action=' + action);
        });
    }
});
</script>

<?php include "footer.php"; ?>
<?php 
include "header.php";
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

if (mysqli_connect_errno()) {
    echo "<script>alert('Koneksi database gagal: " . mysqli_connect_error() . "');</script>";
    exit();
}

if (!isset($_GET['user_id'])) {
    echo "<script>alert('User ID tidak ditemukan.'); window.location.href='home.php';</script>";
    exit();
}

$user_id = $_GET['user_id'];

// Fetch user info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($koneksi, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if (!$user_data) {
    echo "<script>alert('User tidak ditemukan.'); window.location.href='home.php';</script>";
    exit();
}

// Determine if current user is following this profile
$current_user_id = 1; // Replace with your logic to get the current user ID
$is_following_query = "SELECT * FROM followers WHERE user_id = $user_id AND follower_id = $current_user_id";
$is_following_result = mysqli_query($koneksi, $is_following_query);
$is_following = mysqli_num_rows($is_following_result) > 0;

// Fetch user's posts
$posts_query = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$posts_result = mysqli_query($koneksi, $posts_query);
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
</style>

<div class="profile">

    <div class="user-info">
        <img src="<?= !empty($user_data['profile_image']) ? htmlspecialchars($user_data['profile_image']) : 'assets/profile/none.png' ?>" alt="Avatar" class="avatar">
        <div>
            <div class="profile-stats">
                <div>
                    <span>45</span>
                    Posts
                </div>
                <div class="follower-count">
                    <span>668</span>
                    Followers
                </div>
                <div>
                    <span>408</span>
                    Following
                </div>
            </div>
            <button id="follow-button" data-user-id="<?= $user_id ?>" data-follower-id="<?= $current_user_id ?>" class="<?= $is_following ? 'unfollow' : 'follow' ?>">
                <?= $is_following ? 'Unfollow' : 'Follow' ?>
            </button>
            <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left: 100px">Message</button>
            <h4><?= htmlspecialchars($user_data['name']) ?></h4>
            <p>@<?= htmlspecialchars($user_data['username']) ?></p>
            <div class="profile-bio">
                <p><?= htmlspecialchars($user_data['bio']) ?></p>
                <a href="#">See Translation</a>
            </div>
            <div class="dashboard">
                <p style="color: #0C0C0C">Professional dashboard</p>
                <div class="profile-links">
                    <a href="#">instagram.com/<?= htmlspecialchars($user_data['username']) ?>?igshid=MzRlODBiN...</a>
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

        <!-- Feed -->
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
                <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white; margin-bottom: 15px;">
                    <div class="d-flex">
                        <img src="<?= !empty($user_data['profile_image']) ? htmlspecialchars($user_data['profile_image']) : 'https://via.placeholder.com/50' ?>" class="rounded-circle" alt="User Image">
                        <div class="ms-3">
                            <h5 class="mb-0"><?= htmlspecialchars($user_data['name']) ?></h5>
                            <small style="color: #fff">@<?= htmlspecialchars($user_data['username']) ?></small>
                        </div>
                    </div>
                    <p class="mt-3"><?= htmlspecialchars($post['content']) ?></p>
                    <div class="d-flex justify-content-between" style="color: white;">
                        <a href="#" style="color: white;">
                            <div class="post" data-post-id="<?= $post['id'] ?>">
                                <div class="post-ratings-container">
                                    <div class="post-rating">
                                        <span class="post-rating-button material-icons">thumb_up</span>
                                        <span class="post-rating-count">0</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" style="color: white;">
                            <div class="post" data-post-id="<?= $post['id'] ?>">
                                <div class="post-ratings-container">
                                    <div class="post-rating">
                                        <span class="post-rating-button material-icons">thumb_down</span>
                                        <span class="post-rating-count">0</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" style="color: white;">
                            <img src="assets/img/gambar9.png" alt="Gambar 3" style="width: 20px; height: 30px;">
                            0
                        </a>
                        <a href="#" style="color: white;">
                            <img src="assets/img/gambar3.png" alt="Gambar 4" style="width: 20px; height: 20px;">
                            0
                        </a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    var followButton = document.getElementById('follow-button');
    if (followButton) { // Memastikan tombol follow ditemukan
        followButton.addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah perilaku default klik link

            var userId = this.getAttribute('data-user-id');
            var followerId = this.getAttribute('data-follower-id');
            var action = this.classList.contains('unfollow') ? 'unfollow' : 'follow';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'follow_unfollow.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert(response.message);

                        // Update jumlah pengikut di UI
                        var followerCountElement = document.querySelector('.profile-stats .follower-count span');
                        var currentFollowerCount = parseInt(followerCountElement.innerText);

                        if (action == 'follow') {
                            followerCountElement.innerText = currentFollowerCount + 1;
                            followButton.classList.remove('follow');
                            followButton.classList.add('unfollow');
                            followButton.innerText = 'Unfollow';
                        } else {
                            followerCountElement.innerText = currentFollowerCount - 1;
                            followButton.classList.remove('unfollow');
                            followButton.classList.add('follow');
                            followButton.innerText = 'Follow';
                        }
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send('user_id=' + userId + '&follower_id=' + followerId + '&action=' + action);
        });
    }
});
</script>

<?php include "footer.php"; ?>
