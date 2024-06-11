<?php
session_start();
// Pastikan ada sesi username, jika tidak, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header('Location: template/login.php');
    exit();
}

// Ambil username dari sesi
$username = $_SESSION['username'];

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "db_itsave");

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Ambil data user dari database berdasarkan username
$sql = "SELECT name, username FROM users WHERE username = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    die("User tidak ditemukan.");
}

// Tutup statement dan koneksi
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

        .profile-links {
            text-align: left;
            margin-bottom: 20px;
        }

        .profile-links a {
            color: #1da1f2;
            text-decoration: none;
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
                <img id="profileImage" src="https://via.placeholder.com/80" alt="Profile Image">
                <div>
                    <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                </div>
            </div>
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
            <div class="profile-bio">
                <p>bio</p>
                <a href="#">See Translation</a>
            </div>
            <div class="dashboard">
                <p style="color: #0C0C0C">Professional dashboard</p>
                <div class="profile-links">
                    <a href="#">instagram.com/o8.25am?igshid=MzRlODBiN...</a>
                </div>
            </div>
            <div class="profile-buttons">
                <button id="editProfileBtn">Edit profile</button>
                <button>Share profile</button>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Profile -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Profile</h2>
            <form id="editProfileForm">
                <div class="form-group">
                    <label for="profileImageUpload">Profile Image</label>
                    <img id="profileImagePreview" src="https://via.placeholder.com/80" alt="Profile Image Preview">
                    <input type="file" id="profileImageUpload" name="profileImageUpload" accept="image/*">

                </div>
                <div class="form-group">
                    <label for="profileName">Name</label>
                    <input type="text" id="profileName" name="profileName">
                </div>
                <div class="form-group">
                    <label for="profileUsername">Username</label>
                    <input type="text" id="profileUsername" name="profileUsername">
                </div>
                <div class="form-group">
                    <label for="profileBio">Bio</label>
                    <input type="text" id="profileBio" name="profileBio">
                </div>
                <div class="form-group">
                    <button type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        document.getElementById('profileImageUpload').onchange = function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var preview = document.getElementById('profileImagePreview');
                preview.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        document.getElementById('editProfileForm').onsubmit = function(event) {
            event.preventDefault();

            var profileImage = document.getElementById('profileImagePreview').src;
            var profileName = document.getElementById('profileName').value;
            var profileUsername = document.getElementById('profileUsername').value;
            var profileBio = document.getElementById('profileBio').value;

            document.getElementById('profileImage').src = profileImage;
            document.querySelector('.profile-header h4').innerText = profileName;
            document.querySelector('.profile-header h2').innerText = '@' + profileUsername;
            document.querySelector('.profile-bio p').innerText = profileBio;

            modal.style.display = "none";
        }
    </script>

    <div class="container-fluid" style="margin-top: 40px; display: flex; justify-content: center;">
        <div class="row" style="width: 100%; max-width: 4000px;">
            <div class="col-md-6 feed" style="margin: 0 auto;">
                <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white;">
                    <div class="d-flex align-items-center">
                        <a href="?mod=user" class="d-flex align-items-center text-decoration-none">
                            <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                            <div class="ms-3">
                                <h5 class="mb-0" style="color: #fff;">User Name</h5>
                                <small style="color: #fff;">@username</small>
                            </div>
                        </a>
                    </div>
                    <p class="mt-3" style="color: #fff;">This is a sample post content. It can be a tweet, an update, or anything you want to share with your followers.</p>
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

<?php include "footer.php"; ?>