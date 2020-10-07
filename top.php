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
            //count(follow.followed_user_id)
            
            $stmt = $pdo->prepare('SELECT follow.follow_user_id as follow_users,users.nickname as nickname, users.image as image  FROM users join follow on users.id = follow.followed_user_id where follow.followed_user_id=:followed_user_id');
            $stmt->bindParam('followed_user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $following_users = $stmt->fetchAll();
          
            
            $stmt = $pdo->query('SELECT users.id as post_user_id, posts.id as id, users.nickname as name, users.image as user_image, posts.title as title, posts.body as body, posts.image as image, posts.created_at as created_at FROM posts left outer join users on users.id = posts.user_id order by posts.id desc');
            $posts = $stmt->fetchAll();
            
            $stmt = $pdo->prepare('SELECT users.id as post_user_id, posts.id as id, users.nickname as name, users.image as user_image, posts.title as title, posts.body as body, posts.image as image, posts.created_at as created_at FROM posts left outer join users on users.id = posts.user_id where posts.user_id=:user_id');
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $my_posts = $stmt->fetchAll();
            
            $stmt = $pdo->query('select users.id as post_user_id, posts.id as id, users.nickname as name, users.image as user_image, posts.title as title, posts.body as body, posts.image as image, posts.created_at as created_at from users join follow on users.id=follow.followed_user_id join posts on follow.followed_user_id=posts.user_id where follow.follow_user_id=' . $user_id);
            //$stmt->bindParam(':login_user_id', 5, PDO::PARAM_INT);
            $follow_users_posts = $stmt->fetchAll();

            
            //https://webkaru.net/php/function-array-merge-recursive/
            $timelines = array_merge_recursive($follow_users_posts,$my_posts);
            
            //https://qiita.com/shy_azusa/items/54dadc55e3e71cde1445
            foreach ((array) $timelines as $key => $value) {
                $sort[$key] = $value['id'];
            }
            
            array_multisort($sort, SORT_DESC, $timelines);

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
            .tab_item {
              display: none;
            }
            
            .is-active-item {
              display: block;
            }
            
            /* aタグをブロック要素にする。文字色は親クラスと同様に、下線は消す */
            a {
              display: block;
              color: inherit;
              text-decoration: none;
            }
            
            .tab_btn {
              font-size: 24px;
              padding: 5px;
              background-color: #E0F2F7;
            　/*display: inline-block; /* ボタンを横並びに。flexboxなどでも可 */
              opacity: 0.5;  /* 非アクティブなボタンは半透明にする */
              border-radius: 5px 5px 0 0;
            }
            
            .is-active-btn {
              opacity: 1;  /* アクティブなボタンは半透明を解除 */
              color: #00BFFF; /* 文字色も変える */
            }
            
            .tab_item {
              /*width: 100%;*/
              /*height: 1000px;*/
              padding: 5px;
              /*color: #00BFFF;*/
              /*background-color: #E0F2F7;*/
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
                    <p><a href="followd_users.php?user_id=<?php print $user_id; ?>"><?php print count($following_users);?></a>人にフォローされています</p>
                </div>
                 <div class="offset-sm-2 col-2 text-center">
                    <a href="logout.php" class="btn btn-primary form-control">ログアウト</a> 
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-sm-6">
                    <h1 class="text-center"><a class="tab_btn is-active-btn text-center" href="#item1">投稿一覧</a></h1>
                </div>
                <div class="col-sm-6">
                    <h1 class="text-center"><a class="tab_btn text-center" href="#item2">タイムライン</a></h1>
                </div>
            </div>
            <!-- 投稿一覧　-->
            <div id="item1" class="row mt-2 tab_item is-active-item">
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
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post['id']; ?>"><?php print likeCount($post['id']); ?>いいね</a></span>
                    </form>
                    
                    <?php }else{ ?>
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post['id']; ?>">
                        <button type="submit" name="likeOrUnlike" value="unlike">いいね解除</button>
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post['id']; ?>"><?php print likeCount($post['id']); ?>いいね</a></span>
                    </form>
                    <?php } ?>        
                        
                </div>
                <?php } ?>
            </div>
            
            <!-- タイムライン　-->
            <div id="item2" class="row mt-2 tab_item">
                <?php foreach($timelines as $post){ ?>
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
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post['id']; ?>"><?php print likeCount($post['id']); ?>いいね</a></span>
                    </form>
                    
                    <?php }else{ ?>
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post['id']; ?>">
                        <button type="submit" name="likeOrUnlike" value="unlike">いいね解除</button>
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post['id']; ?>"><?php print likeCount($post['id']); ?>いいね</a></span>
                    </form>
                    <?php } ?>        
                        
                </div>
                <?php } ?>
                </div>
                <div class="row mt-3 mb-3">
                    <div class="offset-sm-4 col-sm-4">
                        <a href="post.php" class="btn btn-primary">新規投稿</a>
                    </div>
                </div>
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
              $('.tab_btn').on('click', function(event) {
                $('.tab_item').removeClass("is-active-item");
                $($(this).attr("href")).addClass("is-active-item");
            
                //以下２行を追加
                $('.tab_btn').removeClass('is-active-btn');
                $(this).addClass('is-active-btn');
                //https://www.ilovex.co.jp/blog/system/softwaredevelopment/post-27.html
                return false;
              });
            });
        </script>
    </body>
</html>
