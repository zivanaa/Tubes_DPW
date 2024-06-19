<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - IT SAFE</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .service-section {
            padding: 80px 0;
            background-color: #f5f7fa;
        }
        .service-section h1 {
            font-size: 3em;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .service-item {
            text-align: center;
            margin-bottom: 40px;
        }
        .service-item h3 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }
        .service-item p {
            font-size: 1em;
            color: #666;
        }
        .service-item i {
            font-size: 3em;
            color: #007bff;
            margin-bottom: 20px;
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
    <div class="service-section">
        <h1>Our Services</h1>
        <div class="row">
            <div class="col-md-4 service-item">
                <i class="fas fa-shield-alt"></i>
                <h3>Cybersecurity</h3>
                <p>Protect your business with our comprehensive cybersecurity solutions.</p>
            </div>
            <div class="col-md-4 service-item">
                <i class="fas fa-cloud"></i>
                <h3>Cloud Services</h3>
                <p>Leverage the power of the cloud with our secure and scalable cloud services.</p>
            </div>
            <div class="col-md-4 service-item">
                <i class="fas fa-tools"></i>
                <h3>IT Support</h3>
                <p>Get expert IT support to ensure your business operations run smoothly.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 service-item">
                <i class="fas fa-network-wired"></i>
                <h3>Network Solutions</h3>
                <p>Implement robust network solutions tailored to your business needs.</p>
            </div>
            <div class="col-md-4 service-item">
                <i class="fas fa-code"></i>
                <h3>Software Development</h3>
                <p>Develop custom software solutions to enhance your business processes.</p>
            </div>
            <div class="col-md-4 service-item">
                <i class="fas fa-database"></i>
                <h3>Data Management</h3>
                <p>Manage your data efficiently with our reliable data management services.</p>
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
