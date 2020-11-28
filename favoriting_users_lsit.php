<?php

    require_once 'daos/FavoriteDAO.php';

    $post_id = $_GET['post_id'];
    session_start();
    

    $messages = array();

    $flash_message = "";

    try {
    
        $favorite_dao = new FavoriteDAO();
        
        $favoriting_users = $favorite_dao->get_favoriting_users($post_id);
    
        
    } catch (PDOException $e) {
        echo 'PDO exception: ' . $e->getMessage();
        exit;
    }
    
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
        <title>いいねをしてくれた皆さま</title>
        <style>
            h2{
                color: red;
                background-color: pink;
            }
            .avator_image{
                object-fit: cover;
                border-radius: 50%;
                width: 50px;
                height: 50px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row mt-2">
                <h1 class=" col-sm-12 text-center">いいねをしてくれた皆さま</h1>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h1>
            </div>
            <div class="row mt-2">
            <?php if(count($favoriting_users) !== 0){ ?> 
                <table class="col-sm-12 table table-bordered table-striped">
                    <tr>
                        <th>ユーザID</th>
                        <th>アバター</th>
                        <th>ユーザ名</th>
                    </tr>
                    </tr>
                <?php foreach($favoriting_users as $user){ ?>
                    <tr>
                        <td><a href="mypage.php?user_id=<?php print $user->user_id; ?>"><?php print $user->user_id; ?></a></td>
                        <td><img src="upload/users/<?php print $user->avatar; ?>" class="avator_image"></td>
                        <td><?php print $user->nickname; ?></td>
                    </tr>
                <?php } ?>
                </table>
            <?php }else{ ?>
                    <p>データ一件もありません。</p>
            <?php } ?>
            </div>
            <div class="row mt-5">
                <a href="top.php" class="btn btn-primary">トップへ</a>
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
