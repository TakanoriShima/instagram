<?php
    
    session_start();
    
    if(isset($_SESSION['user_id']) === true){

        $dsn = 'mysql:host=localhost;dbname=instagram';
        $db_username = 'root';
        $db_password = '';
        
        $user_id = (int)$_GET['user_id'];
        //var_dump($user_id);
    
        try {
        
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // 失敗したら例外を投げる
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,   //デフォルトのフェッチモードはクラス
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',   //MySQL サーバーへの接続時に実行するコマンド
            ); 
           // $options = array();
            // print "usr_id: " . $user_id;
            
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $sql = 'SELECT posts.id as post_id, users.nickname as nickname, users.image as user_image, posts.title as title, posts.body as body, posts.image as post_image, posts.created_at FROM posts join users on posts.user_id = users.id where posts.user_id=:user_id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $my_posts = $stmt->fetchAll();
       
            $user_filePath = 'uploads/users/' . $my_posts[0]['user_image'];

        } catch (PDOException $e) {
            echo 'PDO exception: ' . $e->getMessage();
            exit;
        }
        
    }else{
        $flash_message = "不正アクセスです！ログインしてください";
        $_SESSION['flash_message'] = $flash_message;
        
        header('Location: index.php');
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
        <title>マイページ</title>
        <style>
            h2{
                color: red;
                background-color: pink;
            }
            .profile_image{
                object-fit: cover;
                border-radius: 50%;
                width: 15vw;
                height: 15vw;
            }
            .avator_image{
                object-fit: cover;
                border-radius: 50%;
                width: 3vw;
                height: 3vw;
            }
            .section{
                border: solid 1px blue;
                margin-bottom: 20px;
                padding: 15px;
            }
            .comments{
                border: solid 1px skyblue;
                margin-bottom: 20px;
                padding: 15px;
            }
            .comment{
                /*border: solid 1px skyblue;*/
                margin-bottom: 20px;
                padding: 15px;
                border-bottom: solid 1px skyblue;
            }
            .comment:last-child{
                border-bottom: none;
            }
            a{
                display: block;
                width: 100%;
                text-decoration: none;
                color: black;
            }
            a p{
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h2>
            </div>
            <div class="row mt-5">
                <div class="col-3 text-center">
                    <img src="<?php print $user_filePath; ?>" class="profile_image"> 
                </div>
                <div class="offset-sm-1 col-4 text-left">
                    <h3><?php print $my_posts[0]['nickname']; ?> </h3>
                </div>
                <div class="offset-sm-2 col-2 text-center">
                    <a href="logout.php" class="btn btn-primary form-control">ログアウト</a> 
                </div>
            </div>

            <div class="row mt-2">
                <h1 class=" col-sm-12 text-center">投稿一覧</h1>
            </div>
            <div class="row mt-2">
                <?php foreach($my_posts as $my_post){ ?>
                <div class="offset-sm-3 col-sm-6 section">
                    <a href="show.php?post_id=<?php print $my_post['post_id']; ?>">
                        <p><?php print $my_post['nickname']; ?>　<?php print $my_post['created_at']; ?></p>
                        <p><?php print $my_post['title']; ?></p>
                        <p><?php print $my_post['body']; ?></p>
                        <p><img src="<?php print 'uploads/posts/' . $my_post['post_image']; ?>" style="width: 300px"></p>
                    </a>
                </div>
                <?php } ?>
            </div>
            
            
            <div class="row mt-3">
                <div class="col-2 text-center">
                   <a href="top.php" class="btn btn-primary form-control">トップへ</a> 
                </div>
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
