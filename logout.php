<?php
    session_start();
    //$_SESSION['user_id'] = null;
    //session_destroy();
    $flash_message = "ログアウトしました。";
    $_SESSION['flash_message'] = $flash_message;
  
    header('Location: index.php');
    exit;
?>