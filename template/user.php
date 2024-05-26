<?php include"header.php";?>
<style>
  .dashboard {
            background-color: #BBD4E0;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            text-align: left;
  }
  .profile-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .profile-stats div {
            text-align: center;
        }
        .profile-stats div span {
            display: block;
            font-size: 18px;
            font-weight: bold;
        }
</style>

<div class="profile">
       
        <div class="user-info">
            <img src="assets/img/gambar.png" alt="Avatar" class="avatar">
            <div>
                <div class="profile-stats">
                    <div>
                        <span>45</span>
                        Posts
                    </div>
                    <div>
                        <span>668</span>
                        Followers
                    </div>
                    <div>
                        <span>408</span>
                        Following
                    </div>
                </div>
                <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left : 110px" >Follow</button>
                <button style="background-color: #87CEFA; color: #11174F; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; margin-top: 10px; font-size: 14px; margin-left : 100px">Message</button>
                <h4>Nama</h4>
                <p>@username</p>
                <div class="profile-bio">
                <p>bio</p>
                <a href="#">See Translation</a>
            </div>
            <div class="dashboard">
                <p style="color: #0C0C0C">Professional dashboard</p>
                <div class="profile-links">
                <a href="#">instagram.com/o8.25am?igshid=MzRlODBiN...</a>
                </div>
            </div>
            </div>
            <br>
        </div>
        <div style="clear: both;"></div>
        <h3>Tweets:</h3>        
        
    </div>

    <div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        
        <!-- Feed -->
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <!-- Post -->
            <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white;">
                <div class="d-flex">
                    <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                    <div class="ms-3">
                        <h5 class="mb-0">User Name</h5>
                        <small  style="color : #fff">@username</small>
                    </div>
                </div>
                <p class="mt-3">This is a sample post content. It can be a tweet, an update, or anything you want to share with your followers.</p>
                <div class="d-flex justify-content-between" style="color: white;">
                    <a href="#" style="color: white;">
                    <div class="post" data-post-id="7712">
                      <div class="post-ratings-container">
                        <div class="post-rating">
                          <span class="post-rating-button material-icons">thumb_up</span>
                          <span class="post-rating-count">0</span>
                        </div>
                      </div>
                    </div>
                    </a>
                    <a href="#" style="color: white;">
                    <div class="post" data-post-id="7712">
                      <div class="post-ratings-container">
                        <div class="post-rating">
                          <span class="post-rating-button material-icons">thumb_down</span>
                          <span class="post-rating-count">0</span>
                        </div>
                      </div>
                    </div>
                    </a>
                    <a href="#" style="color: white;">
                        <img src="assets/img/gambar9.png" alt="Gambar 3" style="width: 20px; height: 30px;">
                        0
                    </a>
                    <a href="#" style="color: white;">
                        <img src="assets/img/gambar3.png" alt="Gambar 4" style="width: 20px; height: 20px;">
                        0
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
<br>

  <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
      <a class="page-link">Previous</a>
    </li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item">
      <a class="page-link" href="#">Next</a>
    </li>
  </ul>
</nav> 
 
<?php include"footer.php";?>
