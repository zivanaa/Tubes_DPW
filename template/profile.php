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

    // Handle profile image upload
    if (!empty($_FILES['profileImageUpload']['name'])) {
        $uploadsDirectory = _DIR_ . '/../assets/profile/';
        $profileImage = $uploadsDirectory . basename($_FILES['profileImageUpload']['name']);
        $relativeImagePath = 'assets/profile/' . basename($_FILES['profileImageUpload']['name']);

        if (move_uploaded_file($_FILES['profileImageUpload']['tmp_name'], $profileImage)) {
            // File berhasil diunggah, lanjutkan dengan proses penyimpanan ke database
        } else {
            die("Gagal mengunggah file.");
        }
    } else {
        $relativeImagePath = $user['profile_image']; // Jika tidak ada file baru diunggah, gunakan gambar yang sudah ada
    }

    $updateSql = "UPDATE users SET name = ?, bio = ?, dashboard = ?, profile_image = ?, username = ? WHERE username = ?";
    $updateStmt = $koneksi->prepare($updateSql);

    if ($updateStmt === false) {
        die("Error preparing update statement: " . $koneksi->error);
    }

    $updateStmt->bind_param("ssssss", $name, $bio, $dashboard, $relativeImagePath, $newUsername, $currentUsername);

    if ($updateStmt->execute()) {
        // Update session data
        $_SESSION['username'] = $newUsername;
        $user['name'] = $name;
        $user['bio'] = $bio;
        $user['dashboard'] = $dashboard;
        $user['profile_image'] = $relativeImagePath;
        $user['username'] = $newUsername;
    } else {
        echo "Error updating profile: " . $koneksi->error;
    }

    $updateStmt->close();
}

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
                    <span>150</span>
                    Posts
                </div>
                <div>
                    <span>300</span>
                    Followers
                </div>
                <div>
                    <span>200</span>
                    Following
                </div>
                </div>
                
                <p><?php echo htmlspecialchars($user['bio']); ?></p>
                <div class="profile-buttons">
                    <button id="editProfileBtn">Edit Profile</button>
                    <button>Share Profile</button>
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
        <div class="container-fluid" style="margin-top: 40px; display: flex; justify-content: center;">
        <div class="row" style="width: 100%; max-width: 4000px;">
            <div class="col-md-6 feed" style="margin: 0 auto;">
                <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white;">
                    <div class="d-flex align-items-center">
                        <a href="?mod=profile" class="d-flex align-items-center text-decoration-none">
                            <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                            <div class="ms-3">
                                <h5 class="mb-0" style="color: #fff;">User Name</h5>
                                <small style="color: #fff;">@username</small>
                            </div>
                        </a>
                    </div>
                    <p class="mt-3" style="color: #fff;">This is a sample post content. It can be a tweet, an update, or anything you want to share with your follower.</p>
                    <div class="d-flex justify-content-between" style="color: white;">
                        <div class="post-ratings-container">
                            <div class="post-rating">
                                <a href="#" style="color: white;">
                                    <span class="post-rating-button material-icons">thumb_up</span>
                                    <span class="post-rating-count">0</span>
                                </a>
                            </div>
                        </div>
                        <div class="post-ratings-container">
                            <div class="post-rating">
                                <a href="#" style="color: white;">
                                    <span class="post-rating-button material-icons">thumb_down</span>
                                    <span class="post-rating-count">0</span>
                                </a>
                            </div>
                        </div>
                        <a href="#" style="color: white;">
                            <img src="assets/img/gambar9.png" alt="Gambar 3" style="width: 20px; height: 30px;">
                            <span style="vertical-align: middle;">0</span>
                        </a>
                        <a href="#" style="color: white;">
                            <img src="assets/img/gambar3.png" alt="Gambar 4" style="width: 20px; height: 20px;">
                            <span style="vertical-align: middle;">0</span>
                        </a>
                    </div>
                </div>
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


</body>

</html>
<?php include "footer.php"; ?>
