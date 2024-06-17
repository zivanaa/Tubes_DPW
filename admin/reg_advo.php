<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])|| $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $education = $_POST['education'];
    $degree = $_POST['degree'];
    $specialization = $_POST['specialization'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $role = "advokad"; // Set default role

    // Validasi input
    if ($password !== $confirm_password) {
        die("Password dan konfirmasi password tidak cocok.");
    }

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Koneksi ke database
    $koneksi = new mysqli("localhost", "root", "", "db_itsave");

    // Periksa koneksi
    if ($koneksi->connect_error) {
        die("Koneksi database gagal: " . $koneksi->connect_error);
    }

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // SQL untuk memasukkan data ke tabel users
        $sql_users = "INSERT INTO users (name, username, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())";

        // Persiapkan statement
        $stmt_users = $koneksi->prepare($sql_users);
        if ($stmt_users === false) {
            throw new Exception("Persiapan statement gagal: " . $koneksi->error);
        }

        // Bind parameter
        $stmt_users->bind_param("sssss", $name, $username, $email, $hashed_password, $role);

        // Eksekusi statement
        if ($stmt_users->execute() === false) {
            throw new Exception("Eksekusi statement gagal: " . $stmt_users->error);
        }

        // Dapatkan user_id yang baru saja disimpan
        $user_id = $stmt_users->insert_id;

        // SQL untuk memasukkan data ke tabel advokad
        $sql_advokad = "INSERT INTO advokad (user_id, education, degree, specialization, created_at) VALUES (?, ?, ?, ?, NOW())";

        // Persiapkan statement
        $stmt_advokad = $koneksi->prepare($sql_advokad);
        if ($stmt_advokad === false) {
            throw new Exception("Persiapan statement gagal: " . $koneksi->error);
        }

        // Bind parameter
        $stmt_advokad->bind_param("isss", $user_id, $education, $degree, $specialization);

        // Eksekusi statement
        if ($stmt_advokad->execute() === false) {
            throw new Exception("Eksekusi statement gagal: " . $stmt_advokad->error);
        }

        // Komit transaksi
        $koneksi->commit();

        echo "Registrasi advokad berhasil.";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $koneksi->rollback();
        echo "Terjadi kesalahan: " . $e->getMessage();
    } finally {
        // Tutup statement dan koneksi
        $stmt_users->close();
        $stmt_advokad->close();
        $koneksi->close();
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
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    form {
        background-color: #f1f1f1;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1000px; /* Lebar maksimum form */
        box-sizing: border-box;
    }

    label {
        display: block;
        margin-bottom: 10px;
    }

    input[type="text"], input[type="email"], input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    button {
        background-color: #1da1f2;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0d8cd1;
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
</style>
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
            <form action="register_lawyer.php" method="post">
                <h1 style="text-align: center;">Registrasi Advokad</h1>
                <label for="name">Nama Lengkap:</label>
                <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap" required>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                            
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email" required>

                <label for="education">Pendidikan Terakhir:</label>
                <input type="text" id="education" name="education" placeholder="Masukkan pendidikan terakhir" required>

                <label for="degree">Gelar:</label>
                <input type="text" id="degree" name="degree" placeholder="Masukkan gelar anda" required>

                <label for="specialization">Spesialisasi:</label>
                <input type="text" id="specialization" name="specialization" placeholder="Masukkan spesialisasi hukum" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>

                <label for="confirm-password">Konfirmasi Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Konfirmasi password" required>

                <div style="text-align: center; color: #11174F">
                    <button type="submit">Daftar</button>
                </div>
            </form>
        </main>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Admin Panel</p>
    </footer>
</body>
</html>
