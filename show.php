<?php
    require_once "daos/PostDAO.php";
    require_once "daos/CommentDAO.php";
    
    session_start();
    
    // ログインしていたら
    if(isset($_SESSION['user_id']) === true){

        // ログインした人のユーザ番号
        $user_id = $_SESSION['user_id'];
        // 投稿番号
        $post_id = $_GET['post_id'];
    
        $flash_message = "";
        $commet_flash_message = "";
    
        try {
            // コメント投稿ボタンを押したとき
            if(isset($_POST['body']) === true){
                
                $body = $_POST['body'];
                
                $comment_dao = new CommentDAO();
                $comment = new Comment($user_id, $post_id, $body);
                $comment_dao->insert($comment);
                $comment_flash_message = "コメント投稿が成功しました。";
                header('Location: show.php?post_id=' . $post_id); 
                exit;
            }
            
            // コメント一覧の取得
            $post_dao = new PostDAO();
            $post = $post_dao->get_post_by_id($post_id);
            
            $comments = $post->get_comments();
        
            $user_filePath = USER_IMAGE_DIR . $post->get_user()->avatar;
            $post_filePath = POST_IMAGE_DIR . $post->image;
            
            
            if(isset($_SESSION['flash_message']) === true){
                $flash_message = $_SESSION['flash_message'];
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
                    <h3><?php print $post->nickname; ?> </h3>
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
                    <a href="show.php?post_id=<?php print $post->id; ?>">
                        <p><?php print $post->get_user()->nickname; ?>　<?php print $post->created_at; ?></p>
                        <p><?php print $post->title; ?></p>
                        <p><?php print $post->body; ?></p>
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
                        <p><img src="<?php print USER_IMAGE_DIR . $comment->get_user()->avatar; ?>" class="avator_image">　<?php print $comment->get_user()->nickname; ?>　<?php print $comment->created_at; ?></p>
                        <p><?php print $comment->body; ?></p>
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
