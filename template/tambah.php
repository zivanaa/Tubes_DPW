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
        <label for="upload-images">Upload Images:</label>
        <br>
        <input type="file" id="upload-images" name="upload-images[]" accept="image/*" multiple onchange="previewImages()">
        <br>
        <div id="preview-container" class="preview-container"></div>
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

function previewImages() {
    var previewContainer = document.getElementById('preview-container');
    var files = document.getElementById('upload-images').files;

    // Clear previous previews
    previewContainer.innerHTML = '';

    if (files.length > 4) {
        showNotification("Anda hanya dapat mengunggah maksimal 4 gambar.");
        document.getElementById('upload-images').value = "";
        return;
    }

    if (files) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();

            reader.onload = (function (file, index) {
                return function (e) {
                    var div = document.createElement('div');
                    div.className = 'image-preview';
                    div.innerHTML = '<img src="' + e.target.result + '" style="width: 100px; height: 100px; margin: 5px;"><button class="remove-btn" data-index="' + index + '">x</button>';
                    previewContainer.appendChild(div);
                };
            })(file, i);

            reader.readAsDataURL(file);
        }
    }

    // Add event listener for remove buttons
    setTimeout(() => {
        var removeButtons = document.getElementsByClassName('remove-btn');
        for (var button of removeButtons) {
            button.addEventListener('click', function(event) {
                var index = event.target.getAttribute('data-index');
                removeImage(index);
            });
        }
    }, 100);
}

function removeImage(index) {
    var input = document.getElementById('upload-images');
    var files = Array.from(input.files);
    files.splice(index, 1);

    // Update the file input with the new files array
    var dataTransfer = new DataTransfer();
    files.forEach(function(file) {
        dataTransfer.items.add(file);
    });
    input.files = dataTransfer.files;

    // Re-preview images
    previewImages();
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
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #8a8a8a;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
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

.preview-container {
    display: flex;
    flex-wrap: wrap;
}

.image-preview {
    position: relative;
    display: inline-block;
}

.image-preview img {
    width: 100px;
    height: 100px;
    margin: 5px;
}

.remove-btn {
    position: absolute;
    top: 0;
    right: 0;
    background: grey;
    color: white;
    border: none;
    font-size: 16px;
    cursor: pointer;
}

.remove-btn:hover {
    background: darkred;
}
</style>
