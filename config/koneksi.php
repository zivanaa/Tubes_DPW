<?php

$koneksi = mysqli_connect("localhost","root","","db_itsave"); //ip,root,pass db, anama db

if (mysqli_connect_errno()){
    echo "koneksi database gagal : " . mysqli_connect_error();

}

?>