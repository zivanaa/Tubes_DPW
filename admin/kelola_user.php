<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])|| $_SESSION['role'] !== 'admin') {
    header('Location: ../page.php?mod=login');
    exit();
}
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

$user_name = $_SESSION['username'];

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
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        // Update data pengguna
        $query = "UPDATE users SET name='$name', username='$username', email='$email', role='$role' WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $message = "Data berhasil diperbarui!";
        } else {
            $message = "Error: " . mysqli_error($koneksi);
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Hapus data terkait di tabel posts
        $query = "DELETE FROM posts WHERE user_id='$id'";
        if (mysqli_query($koneksi, $query)) {
            // Hapus data pengguna
            $query = "DELETE FROM users WHERE id='$id'";
            if (mysqli_query($koneksi, $query)) {
                $message = "Data berhasil dihapus!";
            } else {
                $message = "Error: " . mysqli_error($koneksi);
            }
        } else {
            $message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Ambil nilai pencarian dari form pencarian
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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styling yang sama seperti sebelumnya */
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
            padding: 5px 10px;
            border: none;
            background-color: #567cba;
            color: white;
            cursor: pointer;
        }

        .action-buttons button:hover {
            background-color: #15366b;
        }

        #updateModal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            width: 300px;
            border-radius: 8px;
        }

        #updateModal form {
            display: flex;
            flex-direction: column;
        }

        #updateModal label {
            margin-top: 10px;
        }

        #updateModal input {
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #updateModal button {
            margin-top: 15px;
            padding: 10px;
            border: none;
            background-color: #11174F;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        #updateModal button:hover {
            background-color: #333;
        }

        #updateModal .cancel-button {
            background-color: #f44336;
        }

        #updateModal .cancel-button:hover {
            background-color: #e53935;
        }

        #messagePopup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            width: 300px;
            border-radius: 8px;
            text-align: center;
        }

        #messagePopup button {
            margin-top: 15px;
            padding: 10px;
            border: none;
            background-color: #11174F;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        #messagePopup button:hover {
            background-color: #333;
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
    </style>
    <script>
        function openModal(userId, name, username, email, role) {
            document.getElementById('updateUserId').value = userId;
            document.getElementById('updateName').value = name;
            document.getElementById('updateUsername').value = username;
            document.getElementById('updateEmail').value = email;
            document.getElementById('updateRole').value = role;
            document.getElementById('updateModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('updateModal').style.display = 'none';
        }

        function deleteUser(userId) {
            if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                document.getElementById('deleteUserId').value = userId;
                document.getElementById('deleteForm').submit();
            }
        }

        function showMessagePopup(message) {
            document.getElementById('messagePopupText').innerText = message;
            document.getElementById('messagePopup').style.display = 'block';
        }

        function closeMessagePopup() {
            document.getElementById('messagePopup').style.display = 'none';
        }

        <?php if (!empty($message)) : ?>
            document.addEventListener('DOMContentLoaded', function() {
                showMessagePopup('<?php echo $message; ?>');
            });
        <?php endif; ?>
    </script>
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
                <li><a href="report.php">Report</a></li>
                <li><a href="../page.php?mod=home">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1 style="text-align: center;">Data Pengguna</h1>

            <!-- Search Bar -->
            <div class="search-bar">
                <form method="get">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari...">
                    <button type="submit">Search</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mendapatkan data pengguna dengan filter pencarian
                    $query = "SELECT id, name, username, email, role, created_at FROM users";
                    if (!empty($search)) {
                        $query .= " WHERE name LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%' OR 
                                    username LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%' OR 
                                    email LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%' OR 
                                    role LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'";
                    }
                    $result = mysqli_query($koneksi, $query);

                    // Tampilkan data pengguna dalam bentuk tabel
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['username'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['role'] . "</td>";
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button onclick=\"openModal('".$row['id']."', '".$row['name']."', '".$row['username']."', '".$row['email']."', '".$row['role']."')\">Update</button>";
                            echo "<button onclick=\"deleteUser('".$row['id']."')\">Hapus</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>Data tidak terdaftar</td></tr>";
                    }

                    // Bebaskan hasil query
                    mysqli_free_result($result);

                    // Tutup koneksi database
                    mysqli_close($koneksi);
                    ?>
                </tbody>
            </table>

            <!-- Modal Form for Update -->
            <div id="updateModal">
                <form id="updateForm" method="post">
                    <input type="hidden" name="id" id="updateUserId">
                    <label for="updateName">Name:</label>
                    <input type="text" name="name" id="updateName" required>
                    <label for="updateUsername">Username:</label>
                    <input type="text" name="username" id="updateUsername" required>
                    <label for="updateEmail">Email:</label>
                    <input type="email" name="email" id="updateEmail" required>
                    <label for="updateRole">Role:</label>
                    <select name="role" id="updateRole" required>
                        <option value="admin">admin</option>
                        <option value="advokad">advokad</option>
                        <option value="user">user</option>
                    </select>
                    <button type="submit" name="update">Update</button>
                    <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                </form>
            </div>


            <!-- Form for Delete -->
            <form id="deleteForm" method="post" style="display:none;">
                <input type="hidden" name="id" id="deleteUserId">
                <input type="hidden" name="delete" value="true">
            </form>

            <!-- Message Popup -->
            <div id="messagePopup">
                <p id="messagePopupText"></p>
                <button onclick="closeMessagePopup()">Close</button>
            </div>
    </div>
    </body>

    <footer class="footer">
        <p>&copy; 2024 Admin Panel</p>
    </footer>

</html>
