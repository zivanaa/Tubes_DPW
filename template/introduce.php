<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to IT SAFE</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .intro-section {
            padding: 80px 0;
            text-align: center;
            background-color: #f5f7fa;
        }

        .intro-section h1 {
            font-size: 3em;
            margin-bottom: 20px;
            color: #333;
        }

        .intro-section p {
            font-size: 1.2em;
            margin-bottom: 40px;
            color: #666;
        }

        .intro-section img {
            max-width: 80%;
            height: auto;
            margin-bottom: 20px;
        }

        .features-section {
            padding: 80px 0;
            background-color: #ffffff;
        }

        .feature-item {
            margin-bottom: 40px;
            text-align: center;
        }

        .feature-item h3 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .feature-item p {
            font-size: 1em;
            color: #666;
        }

        .feature-item i {
            font-size: 3em;
            color: #007bff;
            margin-bottom: 20px;
        }

        .btn-custom {
            margin: 10px;
            padding: 15px 30px;
            font-size: 1.2em;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-register {
            background-color: #007bff;
            color: white;
        }

        .btn-register:hover {
            background-color: #218838;
            color: white;
        }

        .btn-login {
            background-color: #007bff;
            color: white;
        }

        .btn-login:hover {
            background-color: #218838;
            color: white;
        }

        .footer {
            background-color: #11174F;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>
    <br>

    <div class="container">
        <div class="intro-section">
            <h1>Welcome to IT SAFE</h1>
            <p>Selamat datang di It Safe dimana anda bisa mengeksplore berita berita terkin, membagikan pengalaman, <br>serta berbagi ilmu yang berkaitan dengan penegakan hukum di Indonesia</p>
            <img src="../assets/img/images.png" alt="IT SAFE">
        </div>
        <br>
        <div class="d-flex justify-content-center">
            <a href="../page.php?mod=login" class="btn btn-custom btn-register">Get Started →</a>
        </div>
        <br>
        <div>
            <h2 style="background-color: #f5f7fa; text-align: center; padding: 20px 0;">Our Features</h2>
        </div>
        <br>
        <div class="features-section">
            <div class="row">
                <div class="col-md-4 feature-item">
                    <i class="fas fa-lock"></i>
                    <h3>Secure Solutions</h3>
                    <p>Kami menjamin keaman data yang telah anda miliki dan kirim ke dalam website kami dengan baik</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="fas fa-headset"></i>
                    <h3>Expert Support</h3>
                    <p>Tim kami dapat menjamin dan mendukung segala kegiatan yang anda lakukan berjalan dengan baik</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Innovative Technology</h3>
                    <p>It Safe memberikan inovasi inovasi terkeni untuk memberikan kenyamanan user dalam menggunakan website</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 IT SAFE. All rights reserved.</p>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>