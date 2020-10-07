<?php

    $user_id = $_GET['user_id'];
    session_start();
    
    $dsn = 'mysql:host=localhost;dbname=instagram';
    $username = 'root';
    $password = '';
    $messages = array();

    $flash_message = "";

    try {
    
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // 失敗したら例外を投げる
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,   //デフォルトのフェッチモードはクラス
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',   //MySQL サーバーへの接続時に実行するコマンド
        ); 
        
        $pdo = new PDO($dsn, $username, $password, $options);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        $stmt = $pdo->prepare('SELECT follow.follow_user_id as user_id, users.nickname as nickname, users.image as image from follow join users on users.id = follow.follow_user_id where follow.followed_user_id=:followed_user_id order by users.id');
        $stmt->bindParam('followed_user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
            
        $following_users = $stmt->fetchAll();
        
        
    
        
        
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
        <title>フォローをしてくれている皆さま</title>
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
            <?php if(count($following_users) !== 0){ ?> 
                <table class="col-sm-12 table table-bordered table-striped">
                    <tr>
                        <th>ユーザID</th>
                        <th>アバター</th>
                        <th>ユーザ名</th>
                    </tr>
                    </tr>
                <?php foreach($following_users as $user){ ?>
                    <tr>
                        <td><a href="mypage.php?user_id=<?php print $user['user_id']; ?>"><?php print $user['user_id']; ?></a></td>
                        <td><img src="uploads/users/<?php print $user['image']; ?>" class="avator_image"></td>
                        <td><?php print $user['nickname']; ?></td>
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
