<?php
    
    session_start();
    
    if(isset($_SESSION['user_id']) === true){

        $dsn = 'mysql:host=localhost;dbname=instagram';
        $db_username = 'root';
        $db_password = '';
        
        $user_id = $_SESSION['user_id'];
        $post_id = $_GET['post_id'];
    
        $flash_message = "";
        $commet_flash_message = "";
    
        try {
        
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // 失敗したら例外を投げる
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,   //デフォルトのフェッチモードはクラス
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',   //MySQL サーバーへの接続時に実行するコマンド
            ); 
            
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            if(isset($_POST['body']) === true){
                $body = $_POST['body'];
                //print "INSERT COMMENT";
                $stmt = $pdo -> prepare("INSERT INTO comments (user_id, post_id, body) VALUES (:user_id, :post_id, :body)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                $stmt->bindParam(':body', $body, PDO::PARAM_STR);
                
                $stmt->execute();
                
                $comment_flash_message = "コメント投稿が成功しました。";
                //$_SESSION['flash_message'] = $flash_message;
                $stmt = $pdo->prepare('SELECT users.nickname as nickname, users.image as avotor_image, comments.body as body, comments.created_at as created_at FROM comments join users on comments.user_id = users.id where comments.post_id =:post_id');
                $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                $stmt->execute();
                
                $comments = $stmt->fetchAll();
                
            }
            
            
            $stmt = $pdo->prepare('SELECT users.nickname as nickname, users.image as user_image, posts.title as title, posts.body as body, posts.image as post_image, posts.created_at FROM posts join users on posts.user_id = users.id where posts.id =:post_id');
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $post = $stmt->fetch();
            
            $stmt = $pdo->prepare('SELECT users.nickname as nickname, users.image as avator_image, comments.body as body, comments.created_at as created_at FROM comments join users on comments.user_id = users.id where comments.post_id =:post_id');
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $comments = $stmt->fetchAll();
            
            $user_filePath = 'uploads/users/' . $post['user_image'];
            $post_filePath = 'uploads/posts/' . $post['post_image'];
            
            
            if(isset($_SESSION['flash_message']) === true){
                $flash_message = $_SESSION['flash_message'];
                //$_SESSION['flash_message'] = null;
                unset($_SESSION["flash_message"]);
            }
        
            
            
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
                    <h3><?php print $post['nickname']; ?> </h3>
                </div>
                <div class="offset-sm-2 col-2 text-center">
                    <a href="logout.php" class="btn btn-primary form-control">ログアウト</a> 
                </div>
            </div>

            <div class="row mt-2">
                <h1 class=" col-sm-12 text-center">投稿詳細</h1>
            </div>
            <div class="row mt-2">
                <div class="offset-sm-3 col-sm-6 section">
                    <a href="show.php?post_id=<?php print $post['id']; ?>">
                        <p><?php print $post['nickname']; ?>　<?php print $post['created_at']; ?></p>
                        <p><?php print $post['title']; ?></p>
                        <p><?php print $post['body']; ?></p>
                        <p><img src="<?php print $post_filePath; ?>" style="width: 300px"></p>
                    </a>
                </div>
            </div>
            
            <?php if(count($comments) > 0){ ?>
            <div class="row mt-2">
                <h1 class="text-center col-sm-12">コメント一覧</h1>
            </div>
            <div class="row mt-2">
                <div class="offset-sm-3 col-sm-6 comments mt-2">
                <?php foreach($comments as $comment){ ?>
                    <div class="comment">
                        <p><img src="<?php print 'uploads/users/' . $comment['avator_image']; ?>" class="avator_image">　<?php print $comment['nickname']; ?>　<?php print $comment['created_at']; ?></p>
                        <p><?php print $comment['body']; ?></p>
                    </div>
                <?php } ?>
                </div>
            </div>
            <?php }else{ ?>
            <div class="row mt-2">
                <p class="text-center col-sm-12">コメントはまだありません</p>
            </div>
            <?php } ?>
            
            <div class="row mt-2">
                <h1 class="text-center col-sm-12">コメント投稿</h1>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $comment_flash_message; ?></h1>
            </div>
            <div class="row mt-2">
                <form class="col-sm-12" action="show.php?post_id=<?php print $post_id; ?>" method="POST">
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-2 col-form-label">内容</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="body" required>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" name="post_id" value="<?php print $post_id; ?>">
                    <!-- 1行 -->
                    <div class="form-group row">
                        <div class="offset-2 col-10">
                            <button type="submit" class="btn btn-primary" id="upload">コメント</button>
                        </div>
                    </div>
                </form>
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
