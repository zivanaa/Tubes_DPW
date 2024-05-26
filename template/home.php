<script>
  function detail(element) {
    // Menggunakan element sebagai referensi untuk menemukan elemen detail terkait
    $(element).siblings(".detail").show();
    $(element).hide(); // Sembunyikan tombol "penjelasan singkat" yang ditekan
  }

  function closeDetail(element) {
    $(element).closest(".card-body").find(".detail").hide(); // Sembunyikan penjelasan singkat
    $(element).closest(".card-body").find(".selengkapnya").show(); // Tampilkan tombol "penjelasan singkat" kembali
  }
</script>

<?php include "header.php"; ?>
<div class="container-fluid" style="margin-top: 15px; display: flex; justify-content: center;">
    <div class="row" style="width: 100%; max-width: 2500px;">
        <!-- Feed -->
        <div class="col-md-6 feed" style="margin: 0 auto;">
            <!-- Post -->
            <div class="post" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; background-color: #11174F; color: white;">
                <a href="?mod=user" style="color: white; text-decoration: none;"> <!-- Tautan untuk mengarahkan ke halaman ?mod=user -->
                    <div class="d-flex">
                        <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Image">
                        <div class="ms-3">
                            <h5 class="mb-0">User Name</h5>
                            <small style="color: #fff">@username</small>
                        </div>
                    </div>
                </a>
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
                    <a href="?mod=komen" style="color: white;">
                        <img src="assets/img/gambar3.png" alt="Gambar 4" style="width: 20px; height: 20px;">
                        0
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
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
<?php include "footer.php"; ?>