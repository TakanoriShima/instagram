<?php
    
    session_start();
    
    if(isset($_SESSION['user_id']) === true){
        $dsn = 'mysql:host=localhost;dbname=instagram';
        $db_username = 'root';
        $db_password = '';
        
        $user_id = $_SESSION['user_id'];
    
        $flash_message = "";
    
        try {
        
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // 失敗したら例外を投げる
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,   //デフォルトのフェッチモードはクラス
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',   //MySQL サーバーへの接続時に実行するコマンド
            ); 
            
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            if(isset($_POST['post_id']) === true){
                if($_POST['likeOrUnlike'] === 'like'){
                    $post_id = $_POST['post_id'];
                    $stmt = $pdo -> prepare("INSERT INTO favorites (user_id, post_id) VALUES (:user_id, :post_id)");
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                    
                    $stmt->execute();
                    $flash_message = "お気に入りに追加しました。";
                }else{
                    $post_id = $_POST['post_id'];
                    $stmt = $pdo -> prepare("DELETE FROM favorites where user_id=:user_id && post_id=:post_id");
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                    
                    $stmt->execute();
                    $flash_message = "お気に入りを削除しました。";
                }
            }
            
            $stmt = $pdo->prepare('SELECT * FROM users where id = :id');
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $user = $stmt->fetch();
            $filePath = 'uploads/users/' . $user['image'];
            
            if(isset($_SESSION['flash_message']) === true){
                $flash_message = $_SESSION['flash_message'];
                //$_SESSION['flash_message'] = null;
                unset($_SESSION["flash_message"]);
            }
            
            $stmt = $pdo->query('SELECT users.id as post_user_id, posts.id as id, users.nickname as name, users.image as user_image, posts.title as title, posts.body as body, posts.image as image, posts.created_at as created_at FROM posts left outer join users on users.id = posts.user_id order by posts.id desc');
            $posts = $stmt->fetchAll();
            
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
    
    function isLike($post_id){
        global $user_id;
        global $pdo;
        $stmt = $pdo->prepare('SELECT count(*) as liked_count FROM favorites where user_id=:user_id && post_id=:post_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
            
        $data = $stmt->fetch();
        //var_dump($liked_count);
        return $data['liked_count'];
    }
    
    function likeCount($post_id){
        global $pdo;
        $stmt = $pdo->prepare('SELECT count(*) as liked_count FROM favorites where post_id=:post_id');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
            
        $data = $stmt->fetch();
        //var_dump($liked_count);
        return $data['liked_count'];
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
        <title>ユーザトップ</title>
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
                width: 50px;
                height: 50px;
            }
            .section{
                border: solid 1px blue;
                margin-bottom: 20px;
                padding: 15px;
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
                    <img src="<?php print 'uploads/users/' . $user['image']; ?>" class="profile_image"> 
                </div>
                <div class="offset-sm-1 col-4 text-left">
                    <h3><?php print $user['nickname']; ?> </h3>
                </div>
                 <div class="offset-sm-2 col-2 text-center">
                    <a href="logout.php" class="btn btn-primary form-control">ログアウト</a> 
                </div>
            </div>

            <div class="row mt-2">
                <h1 class=" col-sm-12 text-center">投稿一覧</h1>
            </div>
            <div class="row mt-2">
                <?php foreach($posts as $post){ ?>
                <div class="offset-sm-3 col-sm-6 section">
                    <a href="show.php?post_id=<?php print $post['id']; ?>">
                        <p><?php print $post['id']; ?></p>
                    </a>
                    <a href="mypage.php?user_id=<?php print $post['post_user_id']; ?>">
                        <p><img src="<?php print 'uploads/users/' . $post['user_image']; ?>" class="avator_image">　<?php print $post['name']; ?>　<?php print $post['created_at']; ?></p>
                    </a>
                        <p><?php print $post['title']; ?></p>
                        <p><?php print $post['body']; ?></p>
                        <p><img src="uploads/posts/<?php print $post['image']; ?>" style="width: 300px"></p>
                    
                    
                    <?php if(isLike($post['id']) == 0){ ?>
                    
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post['id']; ?>">
                        <button type="submit" name="likeOrUnlike" value="like">いいね</button>
                        <span><?php print likeCount($post['id']); ?>いいね</span>
                    </form>
                    
                    <?php }else{ ?>
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post['id']; ?>">
                        <button type="submit" name="likeOrUnlike" value="unlike">いいね解除</button>
                        <span><?php print likeCount($post['id']); ?>いいね</span>
                    </form>
                    <?php } ?>        
                        
                </div>
                <?php } ?>
            </div>
            <a href="post.php" class="btn btn-primary">新規投稿</a>
     
         
           </div>
        
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS, then Font Awesome -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"></script>
    </body>
</html>
