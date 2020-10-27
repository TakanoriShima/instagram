<?php
    require_once 'daos/UserDAO.php';
    require_once 'daos/PostDAO.php';
    require_once 'daos/FavoriteDAO.php';
    require_once 'daos/FollowDAO.php';
    
    session_start();
    $flash_message = "";
    
    if(isset($_SESSION['flash_message']) === true){
        $flash_message = $_SESSION['flash_message'];
        unset($_SESSION["flash_message"]);
    }
    
    // ログインしていれば
    if(isset($_SESSION['user_id']) === true){
        
        $user_id = $_SESSION['user_id'];

        // いいねする、いいね解除するのボタンを押したとき
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            if(isset($_POST['post_id']) === true){
                $post_id = $_POST['post_id'];
                
                $favorite = new Favorite($user_id, $post_id);
                
                $favorite_dao = new FavoriteDAO();
                
                // いいねボタンを押したとき
                if($_POST['likeOrUnlike'] === 'like'){
                    $favorite_dao->insert($favorite);
                    $flash_message = "お気に入りに追加しました。";
                }else{ // いいね解除ボタンを押したとき
                    $favorite_dao->delete($favorite);
                    $flash_message = "お気に入りを削除しました。";
                }
            }
        }
        
        // 自分のインスタンスを生成
        $user_dao = new UserDAO();
        $user = $user_dao->get_user_by_id($user_id);
        
        // 自分のアバターアイコンのファイル名を取得
        $avatar = $user_dao->get_avatar_by_id($user_id);

        $filePath = USER_IMAGE_DIR . $avatar;
        
        // 投稿一覧を取得する
        $post_dao = new PostDAO();
        $posts = $post_dao->get_all_posts();
        
        // 自分の投稿一覧を取得する
        $my_posts = $user_dao->get_my_posts($user_id);
       
        // 自分がフォローしているユーザ一覧を取得する
        $follow_dao = new FollowDAO();
        $my_fallowing_users = $follow_dao->get_my_fallowing_users($user_id);
        
        // 自分をフォローしてくれているユーザ一覧を取得する
        $my_followed_users = $follow_dao->get_my_followed_users($user_id);

        // 自分がフォローしているユーザの投稿一覧を取得する
        $following_user_posts = $user_dao->get_my_following_user_posts($user_id);

        //https://webkaru.net/php/function-array-merge-recursive/
        $timelines = array_merge_recursive($following_user_posts, $my_posts);
        
        // 投稿idの逆順に並べ替え
        $timelines = array_reverse($timelines, true);
        
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
                    <img src="<?php print $filePath;?>" class="profile_image"> 
                </div>
                <div class="offset-sm-1 col-4 text-left">
                    <h3><?php print $user->nickname; ?> </h3>
                    <p><a href="followd_users.php?user_id=<?php print $user_id; ?>"><?php print count($my_followed_users);?></a>人にフォローされています</p>
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
                    <a href="show.php?post_id=<?php print $post->id; ?>">
                        <p><?php print $post->id; ?></p>
                    </a>
                    <a href="mypage.php?user_id=<?php print $post->user_id; ?>">
                        <p><img src="<?php print USER_IMAGE_DIR . $post->get_user()->avatar; ?>" class="avator_image">　<?php print $post->get_user->nickname; ?>　<?php print $post->created_at; ?></p>
                        
                    </a>
                        <p><?php print $post->title; ?></p>
                        <p><?php print $post->body; ?></p>
                        <p><img src="<?php print POST_IMAGE_DIR . $post->image; ?>" style="width: 300px"></p>
                    
                    <!--いいねしていなければ-->
                    <?php if($post->check_favoriting($user_id) === false){ ?>
                    
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post->id; ?>">
                        <button type="submit" name="likeOrUnlike" value="like">いいね</button>
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post->id; ?>"><?php print $post->favoriting_count($user_id); ?>いいね</a></span>
                    </form>
                    
                    <?php }else{ ?>
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post->id; ?>">
                        <button type="submit" name="likeOrUnlike" value="unlike">いいね解除</button>
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post->id; ?>"><?php print $post->favoriting_count($user_id); ?>いいね</a></span>
                    </form>
                    <?php } ?>        
                        
                </div>
                <?php } ?>
            </div>
            
            <!-- タイムライン　-->
            <div id="item2" class="row mt-2 tab_item">
                <?php foreach($timelines as $post){ ?>
                <div class="offset-sm-3 col-sm-6 section">
                    <a href="show.php?post_id=<?php print $post->id; ?>">
                        <p><?php print $post->id; ?></p>
                    </a>
                    <a href="mypage.php?user_id=<?php print $post->user_id; ?>">
                        <p><img src="<?php print USER_IMAGE_DIR . $post->get_user()->avatar; ?>" class="avator_image">　<?php print $post->get_user->nickname; ?>　<?php print $post->created_at; ?></p>
                        
                    </a>
                        <p><?php print $post->title; ?></p>
                        <p><?php print $post->body; ?></p>
                        <p><img src="<?php print POST_IMAGE_DIR . $post->image; ?>" style="width: 300px"></p>
                    
                    <!--いいねしていなければ-->
                    <?php if($post->check_favoriting($user_id) === false){ ?>
                    
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post->id; ?>">
                        <button type="submit" name="likeOrUnlike" value="like">いいね</button>
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post->id; ?>"><?php print $post->favoriting_count($user_id); ?>いいね</a></span>
                    </form>
                    
                    <?php }else{ ?>
                    <form action="top.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php print $post->id; ?>">
                        <button type="submit" name="likeOrUnlike" value="unlike">いいね解除</button>
                        <span><a href="favoriting_users_lsit.php?post_id=<?php print $post->id; ?>"><?php print $post->favoriting_count($user_id); ?>いいね</a></span>
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
