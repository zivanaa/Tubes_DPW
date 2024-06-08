<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "tubes_dpw");

// Periksa koneksi
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}

// Periksa apakah form update atau hapus telah disubmit
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $content = $_POST['content'];
        $image = $_POST['image'];
        $likes = $_POST['likes'];
        $dislikes = $_POST['dislikes'];
        $comments_count = $_POST['comments_count'];
        $created_at = $_POST['created_at'];

        // Update data konten
        $query = "UPDATE posts SET content='$content', image='$image', `likes`='$likes', dislikes='$dislikes', comments_count='$comments_count', created_at='$created_at' WHERE user_id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $message = "Data berhasil diperbarui!";
        } else {
            $message = "Error: " . mysqli_error($koneksi);
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Hapus data konten
        $query = "DELETE FROM posts WHERE user_id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $message = "Data berhasil dihapus!";
        } else {
            $message = "Error: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        justify-content: center;
        gap: 15px;
    }

    .header img {
        height: 40px; /* Sesuaikan tinggi logo dengan tinggi teks */
        width: auto; /* Pertahankan rasio aspek logo */
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
        background-color: #f2f2f2;
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
    }
    /* CSS untuk Form Update */
.modal-content {
    background-color: #11174F;
    color: #fff;
    padding: 20px;
    border-radius: 10px;
    max-width: px;
    margin: 0 auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.modal-content h2 {
    margin-top: 0;
    color: #fff;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.form-group input[type="text"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.btn-update {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.btn-update:hover {
    background-color: #45a049;
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

</style>
<script>
    function openUpdateModal(user_id, content, image, likes, dislikes, commentsCount, createdAt) {
        document.getElementById('updateUserId').value = user_id;
        document.getElementById('updateContent').value = content;
        document.getElementById('updateImage').value = image;
        document.getElementById('updateLikes').value = likes;
        document.getElementById('updateDislikes').value = dislikes;
        document.getElementById('updateCommentsCount').value = commentsCount;
        document.getElementById('updateCreatedAt').value = createdAt;
        document.getElementById('updateModal').style.display = 'block';
    }

    function openDeleteForm(userId) {
        if (confirm('Apakah Anda yakin ingin menghapus konten ini?')) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteForm').submit();
        }
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function showMessagePopup(message) {
        document.getElementById('messagePopupText').innerText = message;
        document.getElementById('messagePopup').style.display = 'block';
    }

    function closeMessagePopup() {
        document.getElementById('messagePopup').style.display = 'none';
    }
</script>


</head>
<body>
    <header class="header">
        <img src="../assets/img/images.png" alt="Logo">
        <h1>Admin Panel</h1>
    </header>
    <div class="container">
        <aside class="sidebar">
            <ul>
                <li><a href="reg_advo.php">Registrasi Advokad</a></li>
                <li><a href="kelola_user.php">Kelola User</a></li>
                <li><a href="kelola_konten.php">Kelola Konten</a></li>
                <li><a href="#logout">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
    <h1 style="text-align: center;">Data Konten</h1>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Content</th>
                <th>Image</th>
                <th>Like</th>
                <th>Dislike</th>
                <th>Comments Count</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Koneksi ke database
            $koneksi = mysqli_connect("localhost", "", "", "tubes_dpw");

            // Periksa koneksi
            if (mysqli_connect_errno()) {
                echo "Koneksi database gagal: " . mysqli_connect_error();
                exit();
            }

            // Query untuk mendapatkan data konten
            $query = "SELECT user_id, content, image, `likes`, dislikes, comments_count, created_at FROM posts";
            $result = mysqli_query($koneksi, $query);

            // Tampilkan data konten dalam bentuk tabel
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['user_id'] . "</td>";
                echo "<td>" . $row['content'] . "</td>";
                echo "<td>" . $row['image'] . "</td>";
                echo "<td>" . $row['likes'] . "</td>";
                echo "<td>" . $row['dislikes'] . "</td>";
                echo "<td>" . $row['comments_count'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "<td class='action-buttons'>";
                echo "<button onclick=\"openUpdateModal('".$row['user_id']."', '".$row['content']."', '".$row['image']."', '".$row['likes']."', '".$row['dislikes']."', '".$row['comments_count']."', '".$row['created_at']."')\">Update</button>";
                echo "<button onclick=\"openDeleteForm('".$row['user_id']."')\">Hapus</button>";
                echo "</td>";
                echo "</tr>";
            }
            

            // Bebaskan hasil query
            mysqli_free_result($result);

            // Tutup koneksi database
            mysqli_close($koneksi);
            ?>
        </tbody>
    </table>

    <!-- Modal Form untuk Update -->
<div id="updateModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('updateModal')">&times;</span>
        <h2>Update Data Konten</h2>
        <form id="updateForm" method="post" class="update-form">
            <input type="hidden" name="id" id="updateUserId">
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
        <input type="hidden" name="id" id="deleteUserId">
        <input type="hidden" name="delete" value="true">
    </form>

    
</main>

    </div>
    <footer class="footer">
        <p>&copy; 2024 Admin Panel</p>
    </footer>
