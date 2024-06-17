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

}  else if ($_GET['mod']=='list_chat'){
    include"template/list_chat.php";

} else if ($_GET['mod']=='komen'){
    include"template/komen.php";

} else if ($_GET['mod']=='profile'){
    include"template/profilepalsu.php";

} else if ($_GET['mod']=='update_profile'){
    include"template/update_profile.php";

}else if ($_GET['mod']=='admin'){
    include"admin/pengguna.php";

} else if ($_GET['mod']=='detail_post'){
    include "template/detail_post.php";

} else if ($_GET['mod']=='show_profile'){
    include "template/show_profile.php";

} else if ($_GET['mod']=='comment'){
    include "template/comment.php";

} else if ($_GET['mod']=='detail_post'){
    include "template/detail_post.php";

} else if ($_GET['mod']=='trending'){
    include "template/trending.php";

} else if ($_GET['mod']=='send_message'){
    include "template/send_message.php";

}  else if ($_GET['mod']=='get_messages'){
    include "template/get_messages.php";
  
} else if ($_GET['mod']=='all_user'){
    include "template/all_user.php";

} else if ($_GET['mod']=='advo_user'){
    include "template/advo_user.php";

} else if ($_GET['mod']=='home_follow'){
    include "template/home_follow.php";

} else if ($_GET['mod']=='save_contact'){
    include "template/save_contact.php";

}  else if ($_GET['mod']=='load_contact'){
    include "template/load_contact.php";

}

?>