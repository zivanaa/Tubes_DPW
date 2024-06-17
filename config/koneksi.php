<?php
$servername = "localhost"; // sesuaikan dengan server Anda
$username = "root"; // sesuaikan dengan username MySQL Anda
$password = ""; // sesuaikan dengan password MySQL Anda
$dbname = "db_itsave"; // sesuaikan dengan nama database Anda

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
