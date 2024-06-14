<?php
session_start();
session_destroy();
header('Location: ../page.php?mod=login');
exit();
?>
