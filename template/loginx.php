<?php
// Start session
session_start();

// Database connection
$servername = "localhost"; // Update this with your server name
$username = "root"; // Update this with your database username
$password = ""; // Update this with your database password
$dbname = "db_itsave";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo "<script>alert('All fields are required.'); window.location.href='login.php';</script>";
        exit();
    }

    // Check credentials in the database
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to home page with the mod parameter
            header("Location: ../page.php?mod=home");
            exit();
        } else {
            echo "<script>alert('Username or password is incorrect.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Username or password is incorrect.'); window.location.href='login.php';</script>";
    }
}

$conn->close();
?>
