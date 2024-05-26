<?php include"header.php";?>

<div class="profile">
        <h2>Upload Konten</h2>
        <br>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="tweet-text">Tweet:</label>
            <br>
            <textarea id="tweet-text" name="tweet-text" rows="4" placeholder="What's happening?" required></textarea>
            <br>
            <label for="upload-image">Upload Image:</label>
            <br>
            <input type="file" id="upload-image" name="upload-image" accept="image/*">
            <br>
            <button type="submit" style="width : 610px">Tweet</button>
            <br>
        </form>
    </div>
 
<?php include"footer.php";?>
