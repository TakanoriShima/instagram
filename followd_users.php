<?php
    
    require_once "daos/UserDAO.php";
    require_once "daos/FollowDAO.php";
    
    // 注目している人のユーザ番号
    $user_id = $_GET['user_id'];
    
    session_start();

    $flash_message = "";

    try {
        $user_dao = new UserDAO();
        $user = $user_dao->get_user_by_id($user_id);
        $follow_dao = new FollowDAO();
        $my_followed_users = $follow_dao->get_my_followed_users($user_id);

    } catch (PDOException $e) {
        echo 'PDO exception: ' . $e->getMessage();
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
        <title>フォローをしてくれている皆さま</title>
        <style>
            h2{
                color: red;
                background-color: pink;
            }
            .avator_image{
                object-fit: cover;
                border-radius: 50%;
                width: 50px;
                height: 50px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row mt-2">
                <h1 class=" col-sm-12 text-center">フォローをしてくれた皆さま</h1>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h1>
            </div>
            <div class="row mt-2">
            <?php if(count($my_followed_users) !== 0){ ?> 
                <table class="col-sm-12 table table-bordered table-striped">
                    <tr>
                        <th>ユーザID</th>
                        <th>アバター</th>
                        <th>ユーザ名</th>
                    </tr>
                    </tr>
                <?php foreach($my_followed_users as $user){ ?>
                    <tr>
                        <td><a href="mypage.php?user_id=<?php print $user->id; ?>"><?php print $user->id; ?></a></td>
                        <td><img src="<?php print USER_IMAGE_DIR . $user->avatar; ?>" class="avator_image"></td>
                        <td><?php print $user->nickname; ?></td>
                    </tr>
                <?php } ?>
                </table>
            <?php }else{ ?>
                    <p>データ一件もありません。</p>
            <?php } ?>
            </div>
            <div class="row mt-5">
                <a href="top.php" class="btn btn-primary">トップへ</a>
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
