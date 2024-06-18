<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
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
            border-radius: 15px; /* Membuat ujung kontainer tidak runcing */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 6px 20px rgba(0, 0, 0, 0.19); /* Menambahkan efek 3D */
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

        input[type="text"],
        input[type="email"],
        input[type="password"] {
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

        footer small {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="container-fluid px-5">
            <div class="row align-items-center">
                <div class="col">
                    <img src="assets/img/images.png" style="height: 90px;">
                </div>
                <div class="col">
                    <h1>IT SAFE</h1>
                </div>
                <div class="col text-right">
                    <div class="d-flex flex-column align-items-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    <div class="container">
        <h2 style="color: white; text-align: center;">Registrasi User</h2>
        <form action="template/register.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Masukkan nama" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>

            <label for="confirm-password">Konfirmasi Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Konfirmasi password" required>

            <div style="text-align: center;">
                <button type="submit">Daftar</button>
            </div>
        </form>

        <p style="text-align: center; color: white;">Sudah memiliki akun? <a href="page.php?mod=login" style="color: #1da1f2;">Login</a></p>
        <!-- <p style="text-align: center; color: white;">Anda Adokad? <a href="admin/reg_advo.php" style="color: #1da1f2;">Register Adokad</a></p> -->
    </div>
    <br>
    <br>
    <br>
    <br>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');

            // Set initial value with @ if it's empty
            if (usernameInput.value === '') {
                usernameInput.value = '@';
            }

            usernameInput.addEventListener('input', function(event) {
                const value = event.target.value;

                // If the first character is not @, reset the value to start with @
                if (!value.startsWith('@')) {
                    event.target.value = '@';
                }
            });

            usernameInput.addEventListener('keydown', function(event) {
                // Prevent user from deleting the @ symbol
                if (event.key === 'Backspace' && usernameInput.selectionStart <= 1) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

<footer>
    <div>
        <small>&copy; 2024. IT SAFE Company | All rights reserved.</small>
    </div>
</footer>

</html>
