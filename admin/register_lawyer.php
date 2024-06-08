<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pendidikan_terakhir = $_POST['education']; // Ubah nama sesuai dengan nama input di form
    $gelar = $_POST['degree']; // Ubah nama sesuai dengan nama input di form
    $spesialisasi = $_POST['specialization']; // Ubah nama sesuai dengan nama input di form
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
    $koneksi = new mysqli("localhost", "root", "", "tubes_dpw");

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
        $sql_advokad = "INSERT INTO advokad (user_id, pendidikan_terakhir, gelar, spesialisasi, created_at) VALUES (?, ?, ?, ?, NOW())";

        // Persiapkan statement
        $stmt_advokad = $koneksi->prepare($sql_advokad);
        if ($stmt_advokad === false) {
            throw new Exception("Persiapan statement gagal: " . $koneksi->error);
        }

        // Bind parameter
        $stmt_advokad->bind_param("isss", $user_id, $pendidikan_terakhir, $gelar, $spesialisasi);

        // Eksekusi statement
        if ($stmt_advokad->execute() === false) {
            throw new Exception("Eksekusi statement gagal: " . $stmt_advokad->error);
        }

        // Komit transaksi
        $koneksi->commit();

        // Registrasi berhasil, arahkan kembali ke reg_advo.php dengan popup konfirmasi
        echo "<script>alert('Registrasi advokad berhasil.'); window.location.href = 'reg_advo.php';</script>";
        exit;
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
