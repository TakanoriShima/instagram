<?php
    require_once 'daos/PostDAO.php';
    session_start();
    //var_dump($_SESSION);
    $flash_message = "";
    if(isset($_SESSION['flash_message']) === true || isset($_SESSION['usre_id']) === true){
        $flash_message = $_SESSION['flash_message'];
    }
    
    $post_dao = new PostDAO();
    $posts = $post_dao->get_all_posts();
    session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>写真投稿サイト</title>
        <style>
            h1{
                border: solid 2px blue;
                border-radius: 40px;
                padding: 10px;
            }
            h2{
                color: red;
                background-color: pink;
            }
            img{
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row mt-4 mr-1 ml-1">
                <h1 class=" col-sm-12 text-center">写真投稿サイト</h1>
            </div>
            <div class="row mt-3">
                <h3 class="col-sm-12 text-center">会員登録して写真を共有しよう</h3>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h2>
            </div>
            <div class="row mt-5 mb-4">
                <div class="offset-sm-3 col-sm-3 text-center mb-4">
                    <a href="register.php" class="btn btn-primary col-sm-12">新規会員登録</a>
                </div>
                <div class="col-sm-3 mb-4">
                    <a href="login.php" class="btn btn-primary col-sm-12">ログイン</a>
                </div>
            </div>
            <div class="row mt-4">
                <?php foreach($posts as $post){ ?>
                <div class="col-sm-3 mb-4"><img src="upload/posts/<?php print $post->image; ?>"></div>
                <?php } ?>
            </div>
        </div>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS, then Font Awesome -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"></script>
    </body>
</html>
