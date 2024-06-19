<?php
session_start();
include "header.php";

if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login terlebih dahulu.";
    exit();
}
?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="container d-flex justify-content-center align-items-centerontainer"  >
    <div class="justify-content-center" style="text-align:center">
        <div class="col-md-12">
            <div class="card mt-12 " style="background-color: #11174F;">
                <div class="card-header">
                    <h2>Upload Konten</h2>
                </div>
                <div class="card-body">
                    <form action="template/upload.php" method="post" enctype="multipart/form-data" onsubmit="return handleFormSubmit(event)">
                        <div class="form-group">
                            <label for="tweet-text">Tweet:</label>
                            <textarea class="form-control" id="tweet-text" name="tweet-text" rows="4" placeholder="What's happening?" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="upload-images">Upload Images:</label>
                            <input type="file" class="form-control-file" id="upload-images" name="upload-images[]" accept="image/*" multiple onchange="previewImages()">
                        </div>
                        <div id="preview-container" class="preview-container"></div>
                        <button type="submit" class="btn btn-primary btn-block">Tweet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Popup -->
<div id="notification" class="notification">
    <span id="notification-message"></span>
    <button onclick="closeNotification()" class="close-btn">x</button>
    <div class="notification-buttons">
        <a class="home-button" href="?mod=home">Go to Home</a>
    </div>
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
                    div.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail"><button class="remove-btn" data-index="' + index + '">x</button>';
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

function refreshContent() {
    location.reload(); // Merefresh halaman
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
    refreshContent(); // Merefresh konten setelah menutup notifikasi
}
</script>

<style>
.notification {
    position: fixed;
    top: 30%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #8a8a8a;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
    max-width: 80%;
    text-align: center;
}

.notification-buttons {
    margin-top: 10px;
}

.home-button {
    background-color: blue;
    color: white;
    border: none;
    padding: 5px 10px;
    font-size: 14px;
    cursor: pointer;
}

.home-button:hover {
    background-color: darkblue;
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

.card {
    margin-top: 50px;
    color: white;
    max-width:100%;
    position: center;
}

.card-header {
    text-align: center;
}
</style>
