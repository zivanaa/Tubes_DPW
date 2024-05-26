<?php include"header.php";?>
<style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        background-color: #f0f2f5;
    }
    .container {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }
    .feed {
        width: 100%;
        max-width: 4000px;
        margin: 0 auto;
    }
    .post {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 10px;
        background-color: #11174F;
        color: white;
        margin-bottom: 15px;
        position: relative;
    }
    .post img {
        width: 50px;
        height: 50px;
    }
    .post h5, .post small {
        margin: 0;
        color: white;
    }
    .post p {
        margin: 10px 0;
    }
    .post .post-rating {
        display: flex;
        align-items: center;
    }
    .post .post-rating-button {
        cursor: pointer;
    }
    .comments-tab {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
    }
    .comments-tab .content {
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        height: 70%;
        overflow-y: auto;
    }
    .close-btn {
        position: absolute;
        top: 10px;
        right: 20px;
        cursor: pointer;
    }
    .post-divider {
        width: 2px;
        height: 15px; /* Adjust height to connect the posts */
        background-color: #ddd;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: -15px; /* Adjust position to align with posts */
    }
</style>
</head>
<body>
    
    <div class="container">
        <div class="feed col-md-6">
            <!-- Post -->
            <div class="post">
                <div class="d-flex">
                    <a href="?mod=user" class="d-flex" style="text-decoration: none;">
                        <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                        <div class="ms-3">
                            <h5 class="mb-0">User Name</h5>
                            <small>@username</small>
                        </div>
                    </a>
                </div>
                <p class="mt-3">This is a sample post content. It can be a tweet, an update, or anything you want to share with your followers.</p>
                <div class="d-flex justify-content-between">
                    <a href="#" class="post-rating" style="color: white;">
                        <span class="material-icons">thumb_up</span>
                        <span class="post-rating-count">0</span>
                    </a>
                    <a href="#" class="post-rating" style="color: white;">
                        <span class="material-icons">thumb_down</span>
                        <span class="post-rating-count">0</span>
                    </a>
                    <a href="#" style="color: white;">
                        <img src="assets/img/gambar9.png" alt="Gambar 9" style="width: 20px; height: 30px;">
                        0
                    </a>
                    <a href="#" class="comment-link" style="color: white;">
                        <img src="assets/img/gambar3.png" alt="Gambar 3" style="width: 20px; height: 20px;">
                        0
                    </a>
                </div>
                <div class="post-divider"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="feed col-md-6">
            <!-- Post -->
            <div class="post">
                <div class="d-flex">
                    <a href="?mod=user" class="d-flex" style="text-decoration: none;">
                        <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                        <div class="ms-3">
                            <h5 class="mb-0">User Name</h5>
                            <small>@username</small>
                        </div>
                    </a>
                </div>
                <p class="mt-3">This is a sample post content. It can be a tweet, an update, or anything you want to share with your followers.</p>
                <div class="d-flex justify-content-between">
                    <a href="#" class="post-rating" style="color: white;">
                        <span class="material-icons">thumb_up</span>
                        <span class="post-rating-count">0</span>
                    </a>
                    <a href="#" class="post-rating" style="color: white;">
                        <span class="material-icons">thumb_down</span>
                        <span class="post-rating-count">0</span>
                    </a>
                    <a href="#" style="color: white;">
                        <img src="assets/img/gambar9.png" alt="Gambar 9" style="width: 20px; height: 30px;">
                        0
                    </a>
                    <a href="#" class="comment-link" style="color: white;">
                        <img src="assets/img/gambar3.png" alt="Gambar 3" style="width: 20px; height: 20px;">
                        0
                    </a>
                </div>
                <div class="post-divider"></div>
            </div>
        </div>
    </div>

    <div class="comments-tab" id="commentsTab">
        <div class="content">
            <span class="close-btn" id="closeCommentsTab">X</span>
            <h3>Comments</h3>
            <div id="commentsContent">
                <p>Comment 1: This is a detailed comment.</p>
                <p>Comment 2: This is another detailed comment.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const commentLinks = document.querySelectorAll('.comment-link');
            const commentsTab = document.getElementById('commentsTab');
            const closeCommentsTab = document.getElementById('closeCommentsTab');

            commentLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    commentsTab.style.display = 'flex';
                });
            });

            closeCommentsTab.addEventListener('click', function () {
                commentsTab.style.display = 'none';
            });
        });
    </script>
</body>
<?php include "footer.php"; ?>
