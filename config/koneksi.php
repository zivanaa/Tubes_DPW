<?php

$koneksi = mysqli_connect("localhost","root","","tubes_dpw"); //ip,root,pass db, anama db

if (mysqli_connect_errno()){
    echo "koneksi database gagal : " . mysqli_connect_error();

}

?>