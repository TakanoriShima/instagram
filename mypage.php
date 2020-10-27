<?php
    
    require_once "daos/UserDAO.php";
    
    session_start();
    
    // ログインしていれば
    if(isset($_SESSION['user_id']) === true){

        // ログインしている自分のユーザID
        $user_id = $_SESSION['user_id'];
        
        // 注目している人のユーザID
        $target_user_id = $_GET['user_id'];
        
        try{
            // if($_SERVER['REQUEST_METHOD'] === 'POST'){
                
                // フォローボタンが押されたならば
            //     if($_POST['followOrUnFollow'] === 'follow'){
                    
            //         //$followed_user_id = $_POST['followed_user_id'];
                    
                    
                    
                    
            //         $stmt = $pdo -> prepare("INSERT INTO follow (follow_user_id, followed_user_id) VALUES (:follow_user_id, :followed_user_id)");
            //         $stmt->bindParam(':follow_user_id', $follow_user_id, PDO::PARAM_INT);
            //         $stmt->bindParam(':followed_user_id', $followed_user_id, PDO::PARAM_INT);
                    
            //         $stmt->execute();
            //         $flash_message = "フォローしました。";
            //         $_SESSION['flash_message'] = $flash_message;
                    
            //     }else{
            //         $followed_user_id = (int)$_POST['followed_user_id'];
            //         // print $followed_user_id;
            //         $stmt = $pdo -> prepare("Delete From follow where follow_user_id=:follow_user_id AND followed_user_id=:followed_user_id");
            //         $stmt->bindParam(':follow_user_id', $follow_user_id, PDO::PARAM_INT);
            //         $stmt->bindParam(':followed_user_id', $followed_user_id, PDO::PARAM_INT);
                    
            //         $stmt->execute();
            //         $flash_message = "フォローを解除しました。";
            //         $_SESSION['flash_message'] = $flash_message;
            //     }
                
            // }

            $user_dao = new UserDAO();
            $user = $user_dao->get_user_by_id($user_id);
            $target_user = $user_dao->get_user_by_id($target_user_id);
            $target_user_posts = $user_dao->get_my_posts($target_user_id);
    
            $user_filePath = USER_IMAGE_DIR . $target_user->avatar;

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
                    <h3><?php print $target_user->nickname; ?> </h3>
                    
                    <?php if($target_user_id != $user_id){ ?>
                        <?php if($user->check_follow($target_user_id) === false){ ?>
                            <form action="" method="POST">
                                <input type="hidden" name="followed_user_id" value="<?php print $target_user_id; ?>">
                                <input type="hidden" name="followOrUnFollow" value="follow">
                                <input type="submit" value="フォローする">
                            </form>
                        <?php }else{ ?>
                            <form action="" method="POST">
                                <input type="hidden" name="followed_user_id" value="<?php print $target_user_id; ?>">
                                <input type="hidden" name="followOrUnFollow" value="unfollow">
                                <input type="submit" value="フォローを解除する">
                            </form>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="offset-sm-2 col-2 text-center">
                    <a href="logout.php" class="btn btn-primary form-control">ログアウト</a> 
                </div>
            </div>

            <div class="row mt-2">
                <h1 class=" col-sm-12 text-center">投稿一覧</h1>
            </div>
            <div class="row mt-2">
                <?php foreach($target_user_posts as $post){ ?>
                <div class="offset-sm-3 col-sm-6 section">
                    <p><?php print $post->post_id; ?></p>
                    <p><?php print $post->get_user()->nickname ?>　<?php print $post->created_at; ?></p>
                    <p><?php print $post->title; ?></p>
                    <p><?php print $post->body; ?></p>
                    <p><img src="<?php print POST_IMAGE_DIR . $post->image; ?>" style="width: 300px"></p>
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
