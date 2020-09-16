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
            
            $stmt = $pdo->prepare('SELECT * FROM users where id = :id');
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $user = $stmt->fetch();
            $filePath = 'uploads/' . $user['image'];
            
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
                    <h3><?php print $user['name']; ?> </h3>
                </div>
            </div>

            <div class="row mt-2">
                <h1 class=" col-sm-12 text-center">投稿一覧</h1>
            </div>
            
     
            <div class="row">
                <div class="col-12 text-center">
                   <a href="logout.php" class="btn btn-primary form-control">ログアウト</a> 
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
