<?php
    require_once 'daos/UserDAO.php';
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        session_start();
        
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        try {
    
            $user_dao = new UserDAO();
            $user = $user_dao->login($email, $password);
            
            if($user !== false){
                $_SESSION['flash_message'] = "ログインに成功しました。";
                $_SESSION['user_id'] = $user['id'];
                header('Location: top.php');
                exit;
            }else{
                $flash_message = "ログイン情報が間違っています。";
            }
            
        } catch (PDOException $e) {
            echo 'PDO exception: ' . $e->getMessage();
            exit;
        }
    }else{ //GET通信
        if(isset($_SESSION['user_id']) === null){
            $flash_message = $_SESSION['flash_message'];
        }
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

        <title>ログイン</title>
        <style>
            h2{
                color: red;
                background-color: pink;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row mt-2">
                <h1 class="text-center col-sm-12">ログイン</h1>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h2>
            </div>
            <div class="row mt-2">
                <form class="col-sm-12" action="login.php" method="POST">
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-2 col-form-label">メールアドレス</label>
                        <div class="col-10">
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                
                    
                    <div class="form-group row">
                        <label class="col-2 col-form-label">パスワード</label>
                        <div class="col-10">
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    
                    
                    <!-- 1行 -->
                    <div class="form-group row">
                        <div class="offset-2 col-10">
                            <button type="submit" class="btn btn-primary form-control">ログイン</button>
                        </div>
                    </div>
                </form>
            </div>
             <div class="row mt-5">
                <a href="index.php" class="btn btn-primary">トップページへ</a>
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