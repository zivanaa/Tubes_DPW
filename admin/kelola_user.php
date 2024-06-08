<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
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
</style>
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
            <h1 style="text-align: center;">Data Pengguna</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Tanggal Dibuat</th>
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

                    // Query untuk mendapatkan data pengguna
                    $query = "SELECT name, username, email, created_at FROM users";
                    $result = mysqli_query($koneksi, $query);

                    // Tampilkan data pengguna dalam bentuk tabel
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "<td class='action-buttons'>";
                        echo "<button>Update</button>";
                        echo "<button>Hapus</button>";
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
    </div>
    </body>

    <footer class="footer">
        <p>&copy; 2024 Admin Panel</p>
    </footer>

</html>       
