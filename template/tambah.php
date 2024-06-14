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
    <form action="template/upload.php" method="post" enctype="multipart/form-data" onsubmit="return handleFormSubmit(event)">
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

<!-- Notification Popup -->
<div id="notification" class="notification" style="display: none;">
    <span id="notification-message"></span>
    <button onclick="closeNotification()">x</button>
</div>

<?php include "footer.php"; ?>

<script>
function handleFormSubmit(event) {
    event.preventDefault();

    var form = event.target;
    var formData = new FormData(form);

    fetch('template/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        showNotification(data);
    })
    .catch(error => {
        showNotification("Ada kesalahan saat mengunggah konten.");
    });
}

function showNotification(message) {
    var notification = document.getElementById('notification');
    var notificationMessage = document.getElementById('notification-message');
    
    notificationMessage.innerText = message;
    notification.style.display = 'block';
}

function closeNotification() {
    var notification = document.getElementById('notification');
    notification.style.display = 'none';
}
</script>

<style>
.notification {
    position: fixed;
    bottom: 10px; /* Ubah dari top ke bottom */
    left: 50%;
    transform: translateX(-50%); /* Hanya perlu translateX untuk centering horizontal */
    background-color: #8a8a8a;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none; /* Sembunyikan notifikasi secara default */
}



.notification button {
    background: none;
    border: none;
    color: #fff;
    font-size: 20px;
    margin-left: 10px;
    cursor: pointer;
}

.notification button:hover {
    color: #ccc;
}
</style>
