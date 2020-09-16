<?php

    $dsn = 'mysql:host=localhost;dbname=instagram';
    $db_username = 'root';
    $db_password = '';
    
    $image_dir = "uploads/users/";
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        session_start();
        $name = $_POST['name'];
        $nickname = $_POST['nickname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $image = "";
        
        try {
    
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // 失敗したら例外を投げる
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,   //デフォルトのフェッチモードはクラス
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',   //MySQL サーバーへの接続時に実行するコマンド
            ); 
            
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            
            if (!empty($_FILES['image']['name'])) {//ファイルが選択されていれば$imageにファイル名を代入
            
                $image = uniqid(mt_rand(), true); //ファイル名をユニーク化
                $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
                $file = $image_dir . $image;
            
                move_uploaded_file($_FILES['image']['tmp_name'], $file);//uploadディレクトリにファイル保存
                
        
                $stmt = $pdo -> prepare("INSERT INTO users (name, nickname, email, password, image) VALUES (:name, :nickname, :email, :password, :image)");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':image', $image, PDO::PARAM_STR);
                
                $stmt->execute();
                
                $flash_message = "新規ユーザ登録が成功しました。";
                $_SESSION['flash_message'] = $flash_message;
                
                header('Location: login.php');
            
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
                <form class="col-sm-12" action="resister.php" method="POST" enctype="multipart/form-data">
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
                            <input type="file" name="image" accept='image/*' onchange="previewImage(this);" class="" required　>
                        </div>
                        <div class="col-4">
                            <img id="preview" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" style="max-width:200px;">
                        </div>
                        <canvas id="canvas" class="col-4" width="0" height="0"></canvas>
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
            // function previewImage(obj)
            // {
            // 	var fileReader = new FileReader();
            // 	fileReader.onload = (function() {
            // 		document.getElementById('preview').src = fileReader.result;
            // 	});
            // 	fileReader.readAsDataURL(obj.files[0]);
            // }
            
            $(function() {
                
              var file = null; // 選択されるファイル
              var blob = null; // 画像(BLOBデータ)
              const THUMBNAIL_WIDTH = 100; // 画像リサイズ後の横の長さの最大値
              const THUMBNAIL_HEIGHT = 100; // 画像リサイズ後の縦の長さの最大値
            
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
            
            
              // アップロード開始ボタンがクリックされたら
              $('#upload').click(function(){
                        
            
                // ファイルが指定されていなければ何も起こらない
                if(!file || !blob) {
                    alert("ファイルが指定されていない");
                  return;
                }
            
                var name, fd = new FormData();
                fd.append('file', blob); // ファイルを添付する
                //alert("OK");
                $.ajax({
                  url: "new.php", // 送信先
                  type: 'POST',
                  dataType: 'json',
                  data: fd,
                  processData: false,
                  contentType: false
                })
                .done(function( data, textStatus, jqXHR ) {
                  alert("成功");
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                  alert("失敗");
                });  
            
              });
            
            });
        </script>
    </body>
</html>