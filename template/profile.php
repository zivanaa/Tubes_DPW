<?php
session_start();
// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: template/login.php');
    exit();
}

$currentUsername = $_SESSION['username'];

// Database connection
$koneksi = new mysqli("localhost", "root", "", "db_itsave");

// Check connection
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Get user_id from username
$user_query = "SELECT id FROM users WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $user_query);
mysqli_stmt_bind_param($stmt, 's', $currentUsername);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $user_id);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$user_id) {
    echo "<script>alert('User not found'); window.location.href='template/login.php';</script>";
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

// Fetch user data
$sql = "SELECT name, username, bio, dashboard, profile_image FROM users WHERE username = ?";
$stmt = $koneksi->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $koneksi->error);
}

$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    die("User tidak ditemukan.");
}

// Update profile data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['profileName'];
    $bio = $_POST['profileBio'];
    $dashboard = $_POST['profileDashboard'];
    $newUsername = $_POST['profileUsername'];

    // Check if new username is already taken
    $checkUsernameSql = "SELECT username FROM users WHERE username = ? AND username != ?";
    $checkUsernameStmt = $koneksi->prepare($checkUsernameSql);
    $checkUsernameStmt->bind_param("ss", $newUsername, $currentUsername);
    $checkUsernameStmt->execute();
    $checkUsernameResult = $checkUsernameStmt->get_result();

    if ($checkUsernameResult->num_rows > 0) {
        die("Username sudah digunakan.");
    }


    


    // Assume $currentUsername is obtained from the session or previous query
    $currentUsername = $_SESSION['username'];


    // Handle profile image upload
    $relativeImagePath = $user['profile_image']; // Default to current profile image
    if (!empty($_FILES['profileImageUpload']['name'])) {
        $uploadsDirectory = __DIR__ . '/../assets/profile/';
        $filename = basename($_FILES['profileImageUpload']['name']);
        $profileImage = $uploadsDirectory . $filename;
        $relativeImagePath = 'assets/profile/' . $filename;

        // Validate file type and size (e.g., only allow images and max 2MB)
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profileImageUpload']['tmp_name']);
        $fileSize = $_FILES['profileImageUpload']['size'];
        if (!in_array($fileType, $allowedMimeTypes) || $fileSize > 2 * 1024 * 1024) {
            die("File tidak valid. Hanya gambar dengan ukuran maksimal 2MB yang diperbolehkan.");
        }

        if (!move_uploaded_file($_FILES['profileImageUpload']['tmp_name'], $profileImage)) {
            die("Gagal mengunggah file.");
        }
    }

    

    // Update user profile
    $updateSql = "UPDATE users SET name = ?, bio = ?, dashboard = ?, profile_image = ?, username = ? WHERE username = ?";
    $updateStmt = $koneksi->prepare($updateSql);

    if ($updateStmt === false) {
        die("Error preparing update statement: " . $koneksi->error);
    }

    $updateStmt->bind_param("ssssss", $name, $bio, $dashboard, $relativeImagePath, $newUsername, $currentUsername);

    if ($updateStmt->execute()) {
        // Update session data and user array
        $_SESSION['username'] = $newUsername;
        $user['name'] = $name;
        $user['bio'] = $bio;
        $user['dashboard'] = $dashboard;
        $user['profile_image'] = $relativeImagePath;
        $user['username'] = $newUsername;

        // Redirect to user profile page
        header("Location: ?mod=profile");
        exit();
    } else {
        die("Error updating profile: " . $updateStmt->error);
    }

    $updateStmt->close();
}


// Fetch user data 
$sql_user = "SELECT id, name, username, bio, dashboard, profile_image FROM users WHERE username = ?";
$stmt_user = $koneksi->prepare($sql_user);

if ($stmt_user === false) {
    die("Error preparing statement: " . $koneksi->error);
}

$stmt_user->bind_param("s", $currentUsername);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows == 1) {
    $user = $result_user->fetch_assoc();
    $user_id = $user['id']; // Retrieve user_id from the fetched user data
} else {
    die("User tidak ditemukan.");
}

$stmt_user->close();

// Fetch jumlah post
$posts_count_query = "SELECT COUNT(*) as posts_count FROM posts WHERE user_id = ?";
$stmt_posts_count = $koneksi->prepare($posts_count_query);
$stmt_posts_count->bind_param("i", $user_id);
$stmt_posts_count->execute();
$result_posts_count = $stmt_posts_count->get_result();
$posts_count = $result_posts_count->fetch_assoc()['posts_count'];

$stmt_posts_count->close();

$followers_count_query = "SELECT COUNT(*) as followers_count FROM followers WHERE user_id = ?";
$stmt_followers_count = $koneksi->prepare($followers_count_query);
$stmt_followers_count->bind_param("i", $user_id);
$stmt_followers_count->execute();
$result_followers_count = $stmt_followers_count->get_result();
$followers_count = $result_followers_count->fetch_assoc()['followers_count'];

$stmt_followers_count->close();

// Fetch jumlah following
$following_count_query = "SELECT COUNT(*) as following_count FROM followers WHERE follower_id = ?";
$stmt_following_count = $koneksi->prepare($following_count_query);
$stmt_following_count->bind_param("i", $user_id);
$stmt_following_count->execute();
$result_following_count = $stmt_following_count->get_result();
$following_count = $result_following_count->fetch_assoc()['following_count'];

$stmt_following_count->close();


// Query untuk mendapatkan konten pengguna berdasarkan user_id
$sql_content = "SELECT p.*, 
                      (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'like') AS likes,
                      (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'dislike') AS dislikes,
                      (SELECT COUNT(*) FROM post_actions WHERE post_id = p.id AND action_type = 'repost') AS reposts,
                      (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments_count
               FROM posts p 
               WHERE p.user_id = ?
               ORDER BY p.created_at DESC";
            
$stmt_content = $koneksi->prepare($sql_content);
$stmt_content->bind_param("i", $user_id); // Use bind_param with "i" for integer user_id
$stmt_content->execute();
$result_content = $stmt_content->get_result();


$stmt->close();
$koneksi->close();
?>

<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Card</title>
    <style>
        /* Gaya CSS Anda di sini */
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: white;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header,
        footer {
            width: 100%;
            padding: 10px;
            background-color: #1c1c1c;
            text-align: center;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #11174F;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            width: 100%;
        }

        .profile-card {
            width: 70%;
            max-width: 656px;
            padding: 20px;
            background-color: #11174F;
            border-radius: 10px;
            box-shadow: 0 0 10px #1193D3;
            text-align: center;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header img {
            border-radius: 50%;
            position: center;
            width: 80px;
            height: 80px;
        }

        .profile-header div {
            flex-grow: 1;
            margin-left: 10px;
            text-align: left;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 20px;
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

        .profile-bio {
            text-align: left;
            margin: 20px 0;
        }

        .profile-bio p {
            margin: 5px 0;
        }

        .profile-username {
            border-radius: 5px;
            padding: 5px;
            margin-top: 20px;
            text-align: left;
            font-style: bold;
            margin: 20px 0;
        }

        .profile-username p {
            margin: 5px 0;
            
        }

        .profile-buttons {
            display: flex;
            color: #1193D3;
            justify-content: space-around;
            margin-top: 20px;
        }

        .profile-buttons button {
            background-color: #1193D3;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
        }

        .profile-buttons button:hover {
            background-color: #555;
        }

        .dashboard {
            background-color: #BBD4E0;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            text-align: left;
            color:#000;
            word-wrap: break-word; /* Ensures long words break to the next line */
            overflow-wrap: break-word; /* Ensures long words break to the next line */
        }

        .clicked {
            color: red;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group img {
            display: block;
            margin: 10px 0;
            max-width: 100%;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <br>

    <div class="content">
        <div class="profile-card">
            <div class="profile-header">
                <img id="profileImage" src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image">
                <div class="profile-username">
                    <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                    <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                </div>
            </div>
            <div class="profile-bio">
            <div class="profile-stats">
                <div>
                <span><?php echo $posts_count; ?></span>
                Posts
            </div>
            <div>
                <span><?php echo $followers_count; ?></span>
                Followers
            </div>
            <div>
                <span><?php echo $following_count; ?></span>
                Following
            </div>
                </div>
                <p><?php echo htmlspecialchars($user['bio']); ?></p>
                <div class="profile-buttons">
                    <button id="editProfileBtn">Edit Profile</button>
                    <button>Share Profile</button>
                    <button>Logout</button>
                </div>
            </div>
            <div class="dashboard">
                <h4>Profesional dashboard:</h4>
                <p><?php echo nl2br(htmlspecialchars($user['dashboard'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Modal for editing profile -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Profile</h2>
            <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                    <label for="profileImageUpload">Profile Image:</label>
                    <img id="previewImage" src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image Preview">
                    <input type="file" id="profileImageUpload" name="profileImageUpload" accept="image/*">
            </div>
                <div class="form-group">
                    <label for="profileName">Name:</label>
                    <input type="text" id="profileName" name="profileName" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="profileUsername">Username:</label>
                    <input type="text" id="profileUsername" name="profileUsername" value="<?php echo htmlspecialchars($user['username']); ?>" required oninput="validateUsername()">
                </div>
                <div class="form-group">
                    <label for="profileBio">Bio:</label>
                    <input type="text" id="profileBio" name="profileBio" value="<?php echo htmlspecialchars($user['bio']); ?>">
                </div>
                <div class="form-group">
                    <label for="profileDashboard">Dashboard:</label>
                    <input type="text" id="profileDashboard" name="profileDashboard" value="<?php echo htmlspecialchars($user['dashboard']); ?>">
                </div>
                
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript untuk modal
        var modal = document.getElementById("editProfileModal");
        var btn = document.getElementById("editProfileBtn");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "flex";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Preview image
        document.getElementById('profileImageUpload').addEventListener('change', function(e) {
            var previewImage = document.getElementById('previewImage');
            var file = e.target.files[0];
            var reader = new FileReader();

            reader.onloadend = function() {
                previewImage.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                previewImage.src = "<?php echo htmlspecialchars($user['profile_image']); ?>";
            }
        });
        function validateUsername() {
            const usernameInput = document.getElementById("profileUsername");
            if (!usernameInput.value.includes('@')) {
                usernameInput.value = '@';
            }
        }
    </script>

    <!-- BAGIAN KONTEN USER -->
    <div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <?php while ($row = mysqli_fetch_assoc($result_content)): ?>
                <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white; margin-bottom: 15px;">
                    <a href="?mod=show_profile&user_id=<?= htmlspecialchars($row['user_id']) ?>" style="color: white; text-decoration: none;">
                        <div class="d-flex">
                            <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'assets/profile/none.png' ?>" class="rounded-circle" alt="User Image" style="width: 50px; height: 50px;">
                            <div class="ms-3">
                                <strong class="mb-0"><?= htmlspecialchars($user['name']) ?></strong>
                                <br>
                                <h7 style="color: #fff"><?= htmlspecialchars($user['username']) ?></h7>
                            </div>
                        </div>
                    </a>
                    <p class="mt-3"><?= htmlspecialchars($row['content']) ?></p>
                    <div class="horizontal-scroll">
                        <?php foreach (explode(",", $row['image']) as $image): ?>
                            <!-- Tambahkan link untuk membuka modal -->
                            <a href="#" class="open-modal" data-toggle="modal" data-target="#imageModal<?= $user['id'] ?>">
                                <img src="assets/konten/<?= htmlspecialchars($image) ?>" alt="Post Image" class="horizontal-image">
                            </a>

                            <!-- Modal -->
                            <!-- <div class="modal fade" id="imageModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="imageModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel<?= $user['id'] ?>">Gambar Postingan</h5>
                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="assets/konten/<?= htmlspecialchars($image) ?>" alt="Full Image" style="max-width: 100%; max-height: 80vh;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?> -->
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
    <!-- END KONTEN USER -->

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


</body>

</html>


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
<?php include "footer.php";?>
