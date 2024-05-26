<?php
if ($_GET['mod']=='home'){
    include"template/home.php";

} else if ($_GET['mod']=='registrasi_user'){
    include"template/registrasi_user.php";

} else if ($_GET['mod']=='login'){
    include"template/login.php";

} else if ($_GET['mod']=='tambah'){
    include"template/tambah.php";

} else if ($_GET['mod']=='user'){
    include"template/user.php";

} else if ($_GET['mod']=='registrasi_advokad'){
    include"template/registrasi_advokad.php";

} else if ($_GET['mod']=='chat'){
    include"template/chat.php";

} else if ($_GET['mod']=='edit_profile'){
    include"template/edit_profile.php";

} else if ($_GET['mod']=='list_chat'){
    include"template/list_chat.php";

} else if ($_GET['mod']=='komen'){
    include"template/komen.php";

} else if ($_GET['mod']=='profile'){
    include"template/profile.php";

} else if ($_GET['mod']=='admin'){
    include"admin/pengguna.php";
}
?>