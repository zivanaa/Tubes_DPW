<?php
session_start();
include "header.php";

if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login terlebih dahulu.";
    exit();
}
?>

<div class="profile">
    <h2>Upload Konten</h2>
    <br>
    <form action="template/upload.php" method="post" enctype="multipart/form-data">
        <label for="tweet-text">Tweet:</label>
        <br>
        <textarea id="tweet-text" name="tweet-text" rows="4" placeholder="What's happening?" required></textarea>
        <br>
        <label for="upload-image">Upload Image:</label>
        <br>
        <input type="file" id="upload-image" name="upload-image" accept="image/*">
        <br>
        <button type="submit" style="width: 610px">Tweet</button>
        <br>
    </form>
</div>

<?php include "footer.php"; ?>
