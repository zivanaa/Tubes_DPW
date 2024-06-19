<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - IT SAFE</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .about-section {
            padding: 80px 0;
            text-align: center;
            background-color: #f5f7fa;
        }

        .about-section h1 {
            font-size: 3em;
            margin-bottom: 20px;
            color: #333;
        }

        .about-section p {
            font-size: 1.2em;
            margin-bottom: 40px;
            color: #666;
        }

        .team-section {
            padding: 80px 0;
            background-color: #ffffff;
        }

        .team-item {
            margin-bottom: 40px;
            text-align: center;
        }

        .team-item img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .team-item h3 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .team-item p {
            font-size: 1em;
            color: #666;
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
        <div class="about-section">
            <h1>About IT SAFE</h1>
            <p>It Safe adalah media sosial yang berfokus pada penegakan hukum di Indonesia</p>
            <img src="../assets/img/images.png" alt="IT SAFE">
        </div>
    </div>
    <br>
    <div class="container">
        <div>
            <h2 style="background-color: #f5f7fa; text-align: center; padding: 20px 0;">Meet Our Team</h2>
        </div>

        <div class="team-section">
            <div class="row">
                <div class="col-md-4 team-item">
                    <img style="width  : 200px; height:auto" src="../assets/img/tika.png" alt="Team Member 1">
                    <h3>Dewi Atika Muthi</h3>
                </div>
                <div class="col-md-4 team-item">
                <img style="width  : 200px; height:auto" src="../assets/img/zivana.png" alt="Team Member 2">
                    <h3>Zivana Afra Yulianto</h3>
                </div>
                <div class="col-md-4 team-item">
                    <img style="width  : 200px; height:auto" src="../assets/img/fauzan.png" alt="Team Member 3">
                    <h3>Fauzan Rofif Ardiyanto</h3>
                </div>
                <div class="col-md-4 team-item">
                    <img style="width  : 120px; height:auto" src="../assets/img/candra.png" alt="Team Member 4">
                    <h3>Dwi Candra Pratama</h3>
                </div>
                <div class="col-md-4 team-item">
                    <img style="width  : 200px; height:auto" src="../assets/img/ricky.png" alt="Team Member 5">
                    <h3>Ricky Revenando</h3>
                </div>
                <div class="col-md-4 team-item">
                    <img style="width  : 250px; height:auto" src="../assets/img/tmo.png" alt="Team Member 6">
                    <h3>Nurul Ahmad</h3>
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