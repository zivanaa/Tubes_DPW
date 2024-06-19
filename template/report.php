<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_itsave");

// Periksa koneksi
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}

$notification = ''; // Variabel untuk menyimpan notifikasi

// Periksa apakah form dikirimkan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Tangkap data dari form
    $reporter_username = $_POST['reporter_username'];
    $violation_category = $_POST['violation_category'];
    $description = $_POST['description'];

    // Proses unggah gambar
    // Tentukan direktori tempat untuk menyimpan file
    $upload_dir = "../assets/reports/";

    // Pastikan direktori eksis sebelum menyimpan file
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Inisialisasi variabel $uploadOk
    $uploadOk = 1;

    // File yang akan diunggah
    $original_filename = $_FILES["evidence"]["name"];
    $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    
    // Ubah nama file menjadi username_pelapor_tanggal_waktu.ext
    $timestamp = date('YmdHis');
    $image_name = $reporter_username . "_" . $timestamp . "." . $imageFileType;
    $target_file = $upload_dir . $image_name;

    // Jika file sudah ada, tambahkan angka di belakangnya
    $counter = 1;
    while (file_exists($target_file)) {
        $image_name = $reporter_username . "_" . $timestamp . "_" . $counter . "." . $imageFileType;
        $target_file = $upload_dir . $image_name;
        $counter++;
    }

    // Periksa ukuran file
    if ($_FILES["evidence"]["size"] > 10000000) { // 10 MB
        $notification = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Maaf, ukuran file terlalu besar.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        $uploadOk = 0;
    }

    // Izinkan hanya format gambar tertentu
    $allowed_formats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowed_formats)) {
        $notification = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Maaf, hanya format JPG, JPEG, PNG, dan GIF yang diperbolehkan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        $uploadOk = 0;
    }

    // Jika tidak ada masalah, upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["evidence"]["tmp_name"], $target_file)) {
            // File berhasil diunggah

            // Simpan informasi laporan ke dalam database
            $insert_query = "INSERT INTO reports (reporter_username, violation_category, description, evidence) 
                            VALUES (?, ?, ?, ?)";
            $stmt = $koneksi->prepare($insert_query);
            $stmt->bind_param("ssss", $reporter_username, $violation_category, $description, $image_name);

            if ($stmt->execute()) {
                // Laporan berhasil disimpan
                $notification = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Laporan berhasil diunggah! Laporan anda akan kami tindak lanjuti.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                // Redirect or show success message
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $notification = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Maaf, terjadi kesalahan saat mengunggah file.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
        }
    }
}

// Tutup koneksi database
mysqli_close($koneksi);
?>

<?php include "header.php"; ?>

<!-- report.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Laporan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-container {
            max-width: 600px; /* Atur lebar maksimum container */
            margin-top: 20px; /* Atur jarak atas */
        }
    </style>
</head>
<body>
    <div class="container custom-container">
        <h2>Form Laporan</h2>
        <?php echo $notification; ?> <!-- Menampilkan notifikasi -->

        <form action="" method="post" enctype="multipart/form-data">
            <label for="reporter_username">Username Pelapor:</label>
            <input type="text" id="reporter_username" name="reporter_username" value="<?php echo $user_name; ?>" readonly class="form-control"><br>

            <label>Kategori Pelanggaran:</label>
            <div class="form-check">
                <input type="radio" id="category_spam" name="violation_category" value="Spam" required class="form-check-input">
                <label for="category_spam" class="form-check-label">Spam</label><br>
                <input type="radio" id="category_abuse" name="violation_category" value="Abuse" required class="form-check-input">
                <label for="category_abuse" class="form-check-label">Kekerasan atau Pelecehan</label><br>
                <input type="radio" id="category_inappropriate" name="violation_category" value="Inappropriate Content" required class="form-check-input">
                <label for="category_inappropriate" class="form-check-label">Konten yang Tidak Pantas</label><br><br>
            </div>
            <label for="evidence">Bukti Pelanggaran (Max 10 MB):</label><br>
            <input type="file" id="evidence" name="evidence" accept="image/*" required class="form-control-file"><br>
            <label for="description">Deskripsi:</label>
            <textarea id="description" name="description" rows="4" cols="50" required class="form-control"></textarea>
            <button type="submit" name="submit" class="btn btn-primary mt-3">Kirim Laporan</button>
            <br>
            <br>
            <br>
        </form>
    </div>

    <!-- Bootstrap JS (untuk tombol close pada notifikasi) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include "footer.php"; ?>
