<?php
session_start();


// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

// Periksa koneksi
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}

// Ambil nama user dari session
$user_name = $_SESSION['username'];

// Periksa apakah form update atau hapus telah disubmit
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $content = $_POST['content'];
        $image = $_POST['image'];
        
        $created_at = $_POST['created_at'];

        // Update data konten
        $query = "UPDATE posts SET content='$content', image='$image', created_at='$created_at' WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $message = "Data berhasil diperbarui!";
        } else {
            $message = "Error: " . mysqli_error($koneksi);
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Hapus data konten
        $query = "DELETE FROM posts WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $message = "Data berhasil dihapus!";
        } else {
            $message = "Error: " . mysqli_error($koneksi);
        }
    }
}


// Periksa apakah ada pencarian
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel It Safe</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS yang sama seperti sebelumnya */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background-color: #11174F;
            color: white;
            padding: 15px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .header img {
            height: 80px;
            width: auto;
        }

        .container {
            display: flex;
            flex: 1;
        }

        .sidebar {
            background-color: #a6a6bf;
            padding: 20px;
            width: 200px;
            flex-shrink: 0;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 10px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            display: block;
        }

        .sidebar ul li a:hover {
            background-color: #ddd;
            padding-left: 10px;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: top;
            align-items: center;
            flex-direction: column;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #11174F;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .footer {
            background-color: #11174F;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .action-buttons button {
            margin-right: 5px;
            background-color: #567cba; 
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .action-buttons button:hover {
            background-color: #87cefa; 
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
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #11174F;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .modal-content h2 {
            margin-top: 0;
            color: #fff;
        }

        .form-group {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        .form-group label {
            align-self: center;
            font-weight: bold;
            color: #fff;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-update {
            background-color: #567cba;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .btn-update:hover {
            background-color: #87cefa;
        }

        .cancel-button {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel-button:hover {
            background-color: #da190b;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .search-bar input {
            width: 300px;
            background-color: #d7d7d9;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
        }

        .search-bar button {
            padding: 8px 16px;
            border: none;
            background-color: #11174F;
            color: white;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #333;
        }

        /* CSS untuk modal gambar */
        #imageModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 450px;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
        }

        #imageModal img {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
        }

        #imageModal .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        #imageModal .close:hover,
        #imageModal .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="header">
        <img src="../assets/img/images.png" alt="Logo">
        <h1>Admin Panel</h1>
        <div>
            <p>Yellow babe, <br> <?php echo $user_name; ?></p>
        </div>
    </header>
    <div class="container">
        <aside class="sidebar">
            <ul>
                <li><a href="reg_advo.php">Registrasi Advokad</a></li>
                <li><a href="kelola_user.php">Kelola User</a></li>
                <li><a href="kelola_konten.php">Kelola Konten</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1 style="text-align: center;">Data Konten</h1>
            
            <!-- Search Bar -->
            <div class="search-bar">
                <form method="get" action="">
                    <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Cari konten...">
                    <button type="submit">Search</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Content</th>
                        <th>Image</th>
                        <th>Created At</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mendapatkan data konten
                    $query = "SELECT id, user_id, content, image, created_at FROM posts";

                    if (!empty($search)) {
                        $query .= " WHERE content LIKE '%$search%' OR user_id LIKE '%$search%'";
                    }

                    $result = mysqli_query($koneksi, $query);

                    // Tampilkan data konten dalam bentuk tabel
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['user_id'] . "</td>";
                        echo "<td>" . $row['content'] . "</td>";
                        echo "<td>";

                        // Pisahkan gambar dengan koma
                        $images = explode(',', $row['image']);
                        foreach ($images as $image) {
                            echo "<img src='../assets/konten/" . trim($image) . "' width='50' alt='Image' style='margin: 5px;' onclick=\"openImageModal('../assets/konten/" . trim($image) . "')\">";
                        }

                        echo "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "<td class='action-buttons'>";
                        echo "<button onclick=\"openUpdateModal('" . $row['id'] . "', '" . $row['user_id'] . "', '" . addslashes($row['content']) . "', '" . addslashes($row['image']) . "', '" . $row['created_at'] . "')\">Update</button>";
                        echo "<button onclick=\"openDeleteForm('" . $row['id'] . "')\">Hapus</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='9' style='text-align:center;'>Data tidak terdaftar</td>";
                    }

                    // Bebaskan hasil query
                    mysqli_free_result($result);

                    // Tutup koneksi database
                    mysqli_close($koneksi);
                    ?>
                </tbody>
            </table>

            <!-- Modal Form untuk Update -->
            <div id="updateModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('updateModal')">&times;</span>
                    <h2>Update Data Konten</h2>
                    <form id="updateForm" method="post" class="update-form">
                        <input type="hidden" name="id" id="updateId">
                        <div class="form-group">
                            <label for="updateUserId">User ID:</label>
                            <input type="text" name="user_id" id="updateUserId" required>
                        </div>
                        <div class="form-group">
                            <label for="updateContent">Content:</label>
                            <input type="text" name="content" id="updateContent" required>
                        </div>
                        <div class="form-group">
                            <label for="updateImage">Image:</label>
                            <input type="text" name="image" id="updateImage" required>
                        </div>
                        <div class="form-group">
                            <label for="updateLikes">Likes:</label>
                            <input type="text" name="likes" id="updateLikes" required>
                        </div>
                        <div class="form-group">
                            <label for="updateDislikes">Dislikes:</label>
                            <input type="text" name="dislikes" id="updateDislikes" required>
                        </div>
                        <div class="form-group">
                            <label for="updateCommentsCount">Comments Count:</label>
                            <input type="text" name="comments_count" id="updateCommentsCount" required>
                        </div>
                        <div class="form-group">
                            <label for="updateCreatedAt">Created At:</label>
                            <input type="text" name="created_at" id="updateCreatedAt" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="update" class="btn-update">Update</button>
                            <button type="button" class="cancel-button" onclick="closeModal('updateModal')">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form untuk Hapus -->
            <form id="deleteForm" method="post" style="display:none;">
                <input type="hidden" name="id" id="deleteId">
                <input type="hidden" name="delete" value="true">
            </form>

            <!-- Modal untuk Gambar -->
            <div id="imageModal" class="modal">
                <span class="close" onclick="closeImageModal()">&times;</span>
                <img class="modal-content" id="modalImage">
            </div>
        </main>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Admin Panel</p>
    </footer>

    <script>
        // Fungsi untuk membuka modal update
        function openUpdateModal(id, userId, content, image, likes, dislikes, commentsCount, createdAt) {
            document.getElementById('updateId').value = id;
            document.getElementById('updateUserId').value = userId;
            document.getElementById('updateContent').value = content;
            document.getElementById('updateImage').value = image;
            document.getElementById('updateLikes').value = likes;
            document.getElementById('updateDislikes').value = dislikes;
            document.getElementById('updateCommentsCount').value = commentsCount;
            document.getElementById('updateCreatedAt').value = createdAt;
            document.getElementById('updateModal').style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Fungsi untuk membuka form hapus
        function openDeleteForm(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }

        // Fungsi untuk membuka modal gambar
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').style.display = 'flex';
        }

        // Fungsi untuk menutup modal gambar
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
    </script>
</body>
</html>
