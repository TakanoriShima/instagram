<?php
    require_once 'daos/UserDAO.php';
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        session_start();
        $name = $_POST['name'];
        $nickname = $_POST['nickname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $avatar = "";
        
        try {
    
            if (!empty($_FILES['image']['name'])) {//ファイルが選択されていれば$imageにファイル名を代入
            
                $user_dao = new UserDAO();
                $avatar = $user_dao->upload();
                
                $user = new User($name, $nickname, $email, $password, $avatar);
                $user_id = $user_dao->signup($user);
                
                $_SESSION['user_id'] = $user_id;
                $flash_message = "新規ユーザ登録が成功しました。";
                $_SESSION['flash_message'] = $flash_message;
                
                header('Location: login.php');
                exit;
            
            }
            
        } catch (PDOException $e) {
            echo 'PDO exception: ' . $e->getMessage();
            exit;
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

        <title>新規ユーザ登録</title>
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
                <h1 class="text-center col-sm-12">新規ユーザ登録</h1>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h1>
            </div>
            <div class="row mt-2">
                <form class="col-sm-12" action="register.php" method="POST" enctype="multipart/form-data">
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-2 col-form-label">名前</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-2 col-form-label">ニックネーム</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="nickname" required>
                        </div>
                    </div>
                    
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
                    
                    <div class="form-group row">
                        <label class="col-2 col-form-label">アバター画像</label>
                        <div class="col-2">
                            <input type="file" name="image" class="" required>
                        </div>
                        <canvas id="canvas" class="offset-sm-4 col-4" width="0" height="0"></canvas>
                    </div>
                    
                    <!-- 1行 -->
                    <div class="form-group row">
                        <div class="offset-2 col-10">
                            <button type="submit" class="btn btn-primary form-control" id="upload">登録</button>
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
        <script>
            
            $(function() {
                
              var file = null; // 選択されるファイル
              var blob = null; // 画像(BLOBデータ)
              const THUMBNAIL_WIDTH = 300; // 画像リサイズ後の横の長さの最大値
              const THUMBNAIL_HEIGHT = 300; // 画像リサイズ後の縦の長さの最大値
            
              // ファイルが選択されたら
              $('input[type=file]').change(function() {
          
                // ファイルを取得
                file = $(this).prop('files')[0];
                // 選択されたファイルが画像かどうか判定
                if (file.type != 'image/jpeg' && file.type != 'image/png') {
                  // 画像でない場合は終了
                  file = null;
                  blob = null;
                  return;
                }
            
                // 画像をリサイズする
                var image = new Image();
                var reader = new FileReader();
                reader.onload = function(e) {
                  image.onload = function() {
                    var width, height;
                    if(image.width > image.height){
                      // 横長の画像は横のサイズを指定値にあわせる
                      var ratio = image.height/image.width;
                      width = THUMBNAIL_WIDTH;
                      height = THUMBNAIL_WIDTH * ratio;
                    } else {
                      // 縦長の画像は縦のサイズを指定値にあわせる
                      var ratio = image.width/image.height;
                      width = THUMBNAIL_HEIGHT * ratio;
                      height = THUMBNAIL_HEIGHT;
                    }
                    // サムネ描画用canvasのサイズを上で算出した値に変更
                    var canvas = $('#canvas')
                                 .attr('width', width)
                                 .attr('height', height);
                    var ctx = canvas[0].getContext('2d');
                    // canvasに既に描画されている画像をクリア
                    ctx.clearRect(0,0,width,height);
                    // canvasにサムネイルを描画
                    ctx.drawImage(image,0,0,image.width,image.height,0,0,width,height);
            
                    // canvasからbase64画像データを取得
                    var base64 = canvas.get(0).toDataURL('image/jpeg');        
                    // base64からBlobデータを作成
                    var barr, bin, i, len;
                    bin = atob(base64.split('base64,')[1]);
                    len = bin.length;
                    barr = new Uint8Array(len);
                    i = 0;
                    while (i < len) {
                      barr[i] = bin.charCodeAt(i);
                      i++;
                    }
                    blob = new Blob([barr], {type: 'image/jpeg'});
                    console.log(blob);
                  }
                  image.src = e.target.result;
                }
                reader.readAsDataURL(file);
              });
            
            });
        </script>
    </body>
</html>