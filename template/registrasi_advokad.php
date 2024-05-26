<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Advokad</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #11174F;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .header img {
            height: 90px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        .container {
            background-color: #11174F;
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #1da1f2;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0d8cd1;
        }
        footer {
            background-color: #11174F;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="assets/img/images.png" alt="Logo" style="height: 90px;">
        <h1>IT SAFE</h1>
    </div>
    <br>
    <div class="container">
    <h1 style="text-align: center; color : #fff">Registrasi Advokad</h1>
        <form action="register_lawyer.php" method="post">
            <label for="name">Nama Lengkap:</label>
            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                        
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" required>

            <label for="specialization">Pendidikan Terakhir:</label>
            <input type="text" id="specialization" name="specialization" placeholder="Masukkan pendidikan terakhir" required>

            <label for="specialization">Gelar:</label>
            <input type="text" id="specialization" name="specialization" placeholder="Masukkan gelar anda" required>

            <label for="specialization">Spesialisasi:</label>
            <input type="text" id="specialization" name="specialization" placeholder="Masukkan spesialisasi hukum" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>

            <label for="confirm-password">Konfirmasi Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Konfirmasi password" required>

            <div style="text-align: center;">
                <button type="submit">Daftar</button>
            </div>
        </form>
    </div>
    <br>
    <br>
    <br>

</body>
<footer>
    <div>
        <small>&copy; 2024. It Safe Company | All rights reserved.</small>
    </div>
</footer>
</html>
