<?php
// Koneksi ke database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_itsave';

$koneksi = mysqli_connect($host, $username, $password, $database);

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ?mod=login');
    exit();
}


date_default_timezone_set('Asia/Jakarta');


// Mendapatkan informasi pengguna dari database
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$row = mysqli_fetch_assoc($result);

// Ambil informasi profil pengguna
$user_name = $row['username'];
$user_profile_image = $row['profile_image']; // Kolom di tabel yang menyimpan path gambar profil
$user_role = $row['role']; // Kolom di tabel yang menyimpan role pengguna

// Fungsi untuk mendapatkan gambar profil dari database atau penyimpanan lainnya
function getUserProfileImage($user_id, $koneksi) {
    $query = "SELECT profile_image FROM users WHERE id = $user_id";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        return "default_profile_image.jpg"; // Default jika tidak ada gambar profil
    }

    $row = mysqli_fetch_assoc($result);
    return $row['profile_image'];
}

// Mendapatkan gambar profil pengguna dari fungsi
$user_profile_image = getUserProfileImage($user_id, $koneksi);

// Tutup koneksi database
mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>It Safe</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!-- <link rel="stylesheet" href="assets/css/main.css"> -->
	<!-- <script src="assets/scc/main.js" defer></script> -->
  <script src="assets/css/bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>
  <script src="assets/css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

  <style>
    .sidebar {
            width: 30%;
            background-color: white;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }
        .search-bar {
            padding: 1rem;
            border-bottom: 1px solid #ddd;
        }
        .search-bar input {
            width: 100%;
            padding: 0.5rem;
            border-radius: 20px;
            border: 1px solid #ddd;
        }
        .contacts {
            flex-grow: 1;
            overflow-y: auto;
        }
        .contact {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .contact:hover {
            background-color: #f9f9f9;
        }
        .contact img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
        }
        .contact .details {
            display: flex;
            flex-direction: column;
        }
        .contact .details .name {
            font-weight: 500;
        }
        .contact .details .message {
            color: #888;
        }
        .chat-section {
            width: 70%;
            display: flex;
            flex-direction: column;
            background-color: white;
        }
        .chat-header {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #ddd;
        }
        .chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
        }
        .chat-messages {
            flex-grow: 1;
            padding: 1rem;
            overflow-y: auto;
        }
        .message {
            margin-bottom: 1rem;
            display: flex;
        }
        .message.sent {
            justify-content: flex-end;
        }
        .message .content {
            max-width: 60%;
            padding: 0.75rem 1rem;
            border-radius: 20px;
            position: relative;
        }
        .message.received .content {
            background-color: #f0f0f0;
        }
        .message.sent .content {
            background-color: #0066cc;
            color: white;
        }
        .message .content::before {
            content: "";
            position: absolute;
            width: 0;
            height: 0;
        }
        .message.received .content::before {
            left: -10px;
            top: 10px;
            border: 10px solid transparent;
            border-right: 10px solid #f0f0f0;
        }
        .message.sent .content::before {
            right: -10px;
            top: 10px;
            border: 10px solid transparent;
            border-left: 10px solid #0066cc;
        }
        .chat-input {
            display: flex;
            padding: 1rem;
            border-top: 1px solid #ddd;
        }
        .chat-input input {
            flex-grow: 1;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 1rem;
        }
        .chat-input button {
            background-color: #0066cc;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            color: white;
            cursor: pointer;
        }
    .chat-container {
        max-width: 600px;
        margin: 50px auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        display: flex;
        flex-direction: column;
        height: 80vh;
    }
    .chat-header {
        background-color: #11174F;
        color: #fff;
        padding: 10px;
        border-radius: 8px 8px 0 0;
        text-align: center;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .chat-message {
        display: flex;
        align-items: flex-end;
        margin-bottom: 15px;
    }
    .chat-message.right {
        justify-content: flex-end;
    }
    .chat-message.left {
        justify-content: flex-start;
    }
    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 10px;
    }
    .profile-pic img {
        width: 100%;
        height: 100%;
    }
    .chat-message.right .profile-pic {
        order: 2;
        margin-left: 10px;
        margin-right: 0;
    }
    .chat-message.left .profile-pic {
        order: 1;
    }
    .message-content {
        background-color: #f1f1f1;
        padding: 10px;
        border-radius: 8px;
        max-width: 60%;
        word-wrap: break-word;
    }
    .chat-message.right .message-content {
        background-color: #e1ffc7;
    }
    .chat-input {
        display: flex;
        margin-top: 10px;
    }
    .chat-input input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px 0 0 5px;
    }
    .chat-input button {
        padding: 10px;
        border: none;
        background-color: #1da1f2;
        color: #fff;
        cursor: pointer;
        border-radius: 0 5px 5px 0;
        transition: background-color 0.3s ease;
    }
    .chat-input button:hover {
        background-color: #0d8cd1;
    }
    
    .chat-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 80vh;
        }
        .chat-header {
            background-color: #11174F;
            color: #fff;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .chat-message {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 8px;
        }
        .chat-input {
            display: flex;
            margin-top: 10px;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
        }
        .chat-input button {
            padding: 10px;
            border: none;
            background-color: #1da1f2;
            color: #fff;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
            transition: background-color 0.3s ease;
        }
        .chat-input button:hover {
            background-color: #0d8cd1;
        }
    body {
            font-family: Arial, sans-serif;
            background-color: #f5f8fa;
            margin: 0;
            padding: 20px;
        }
        .profile {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        /* form {
            display: flex;
            flex-direction: column;
        } */
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        input[type="file"] {
            margin-bottom: 15px;
        }
        button {
            background-color: #1da1f2;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0d8cd1;
        }
    body {
            font-family: Arial, sans-serif;
            background-color: #f5f8fa;
            margin: 0;
            padding: 0;
        }
        .profile {
            max-width: 650px;
            margin: 20px auto;
            background-color: #11174F;
            color : white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            margin-bottom: 10px;
        }
        p {
            margin: 10px 0;
        }
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
            float: left;
        }
        .cover {
            width: 100%;
            height: 200px;
            background-color: #1da1f2;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .user-info {
            overflow: hidden;
        }
        .user-info p {
            margin-bottom: 5px;
        }
    </style>
  </style>
</head>
<style>
  

  .container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(18rem, 1fr));
    gap: 1rem;
  }

  .card {
    flex: 1;
  }

  #header3 {
    width: 100%;
    background-color: #d3cfb7d1;
    padding: 30px 0;
    text-align: center;
  }

  .btnmenu {
    background-color: #373321;
    color: #fff;
    border: none;
    padding: 6px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
  }
</style>


<body>

  <!--header start-->
  <div style="background-color: #11174F;">
        <div class="container-fluid px-5">
            <div class="row align-items-center">
                <div class="col">
                    <a href="?mod=home"> <!-- Tambahkan link ke halaman home di sini -->
                        <img src="assets/img/images.png" style="height: 90px;">
                        <span style="font-size: 28px; font-weight: bold; color: #fff;">IT SAFE</span>
                    </a>
                </div>
                <div class="col text-right">
                    <div class="d-flex flex-column align-items-end">
                        <a href="?mod=profile">
                            <!-- Logo profil -->
                            <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'assets/profile/none.png' ?>" class="rounded-circle" alt="User Image" style="height: 40px; width: 40px; border-radius: 50%;">
                        </a>
                        <!-- Keterangan "Profile" -->
                        <span style="font-size: 14px; color: #fff;">Profile</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---header finish--->
    <!---nav star--->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #1974CF;">
  <a class="navbar-brand" href="#"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">

            <li class="nav-item">
              <a class="nav-link active" aria-current="page" style="color : #fff" href="?mod=home">Halaman Utama</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" style="color : #fff" href="?mod=trending">Trending</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" style="color : #fff" href="?mod=home_follow">Following</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" style="color : #fff" href="?mod=chat">Chat</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" style="color : #fff" href="?mod=tambah">Tambah</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color : #fff" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Pengguna
              </a>
              <ul class="dropdown-menu">  
                <li class="nav-item">
                    <a class="nav-link" href="?mod=all_user">Semua Pengguna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?mod=advo_user">Advokad</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?mod=report">Report</a>
                </li>
              </ul>
              <!-- Tampilkan menu Admin jika role pengguna adalah 'admin' -->
          <?php if ($user_role == 'admin') { ?>
            <li>
            <a class="nav-link" style="color : #fff" href="admin/reg_advo.php">Admin</a>
          </li>
          <?php } ?>
          
            </li>
          </ul>
        </div>
      </div>
  </nav>  
</div>
