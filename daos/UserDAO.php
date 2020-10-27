<?php
// 外部ファイルの読み込み
require_once 'config/Const.php';
require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'models/Follow.php';

// usersテーブルとやり取りを行う便利なクラス
class UserDAO{
    
    // データベースと接続を行うメソッド
    public function get_connection(){
        $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
        return $pdo;
    }
    
    // データベースとの切断を行うメソッド
    public function close_connection($pdo, $stmp){
        $pdo = null;
        $stmp = null;
    }
    
    // 全ユーザ情報を取得するメソッド
    public function get_all_users(){
        $pdo = $this->get_connection();
        $stmt = $pdo->query('SELECT * FROM users');
        // フェッチの結果を、userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        $users = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $users;
    }
    
    // id値からユーザ情報を取得するメソッド
    public function get_user_by_id($id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id=:id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          // フェッチの結果を、userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        $user = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスを返す
        return $user;
    }
    
    // id値からユーザ情報を取得するメソッド
    public function get_avatar_by_id($id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id=:id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        // フェッチの結果を、userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        $user = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // avatar画像ファイル名を返す
        return $user->avatar;
    }
    
    // 会員登録をするメソッド
    public function signup($user){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("INSERT INTO users (name, nickname, email, password, avatar) VALUES (:name, :nickname, :email, :password, :avatar)");
        // バインド処理
        $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindParam(':nickname', $user->nickname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $user->email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $user->password, PDO::PARAM_STR);
        $stmt->bindParam(':avatar', $user->avatar, PDO::PARAM_STR);
        
        $stmt->execute();
        $user_id = $pdo->lastInsertId();
        
        $this->close_connection($pdo, $stmp);
        return $user_id;
    }
    
    // ログイン処理をするメソッド
    public function login($email, $password){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email=:email AND password=:password');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        $user = $stmt->fetch();
        //$this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスを返す
        return $user;
    }
    
    // 更新処理をするメソッド
    public function update($user){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('UPDATE users SET name=:name, nickname=:nickname, email=:email, avatar=:avatar, password=:password, profile=:profile, updated_at=:updated_at, last_logined_at=:last_logined_at WHERE id=:id');
        $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindParam(':nickname', $user->nickname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $user->email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $user->password, PDO::PARAM_STR);
        $stmt->bindParam(':avatar', $user->avatar, PDO::PARAM_STR);
        $stmt->bindParam(':profile', $user->profile, PDO::PARAM_STR);
        $stmt->bindParam(':profile', $user->profile, PDO::PARAM_STR);
        $stmt->bindParam(':updated_at', $user->updated_at, PDO::PARAM_STR);
        $stmt->bindParam(':last_logined_at', $user->last_logined_at, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user->id, PDO::PARAM_INT);
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
    }
    
    // 自分の投稿リストを取得するメソッド
    public function get_my_posts($user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE posts.user_id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $stmt->execute();
        $my_posts = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $my_posts;
    }
    
    // 自分がコメントした投稿リストを取得するメソッド
    public function get_my_commenting_posts($user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT posts.id AS id, posts.user_id AS user_id, posts.title AS title, posts.body AS body, posts.image AS image, posts.created_at AS created_at FROM posts JOIN comments ON posts.id = comments.post_id WHERE comments.user_id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $stmt->execute();
        $my_commenting_posts = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $my_commenting_posts;
    }
    
    // 自分がコメントしたコメントリストを取得するメソッド
    public function get_my_comments($user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT comments.id AS id, comments.user_id AS user_id, comments.post_id AS post_id, comments.body AS body, comments.created_at AS created_at FROM comments JOIN users ON comments.user_id = users.id WHERE comments.user_id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Comment');
        $stmt->execute();
        $my_comments = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $my_comments;
    }
    
    // 自分がいいねした投稿リストを取得するメソッド
    public function get_my_favoriting_posts ($user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT posts.id AS id, posts.user_id AS user_id, posts.title AS title, posts.body AS body, posts.image AS image, posts.created_at AS created_at FROM posts JOIN favorites ON posts.id = favorites.post_id WHERE favorites.user_id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $stmt->execute();
        $my_favoriting_posts = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $my_favoriting_posts;
    }
    
    // 自分がフォローしたユーザリストを取得するメソッド
    public function get_my_following_users ($user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT users.id AS id, users.name AS name, users.nickname AS nickname, users.email AS email, users.password AS password, users.avatar AS avatar, users.profile AS profile, users.created_at AS created_at, users.updated_at AS updated_at, users.last_logined_at AS last_logined_at FROM users JOIN follows ON users.id = follows.follow_user_id WHERE follows.follow_user_id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        $my_following_users = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $my_following_users;
    }
    
    // フォローしているか判定するメソッド
    public function check_follow($user_id, $target_user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM follows WHERE follow_user_id = :follow_user_id AND followed_user_id = :followed_user_id');
        $stmt->bindParam(':follow_user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':followed_user_id', $target_user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Follow');
        $stmt->execute();
        $follows = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        
        if(count($follows) === 1){
            return true;
        }else{
            return false;
        }
    }
    
    // 自分をフォローしてくれているユーザリストを取得するメソッド
    public function get_my_followed_users($user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT users.id AS id, users.name AS name, users.nickname AS nickname, users.email AS email, users.password AS password, users.avatar AS avatar, users.profile AS profile, users.created_at AS created_at, users.updated_at AS updated_at, users.last_logined_at AS last_logined_at FROM users JOIN follows ON users.id = follows.followed_user_id WHERE follows.followed_user_id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        $my_followed_users = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $my_followed_users;
    }
    
    // 自分がフォローしたユーザの投稿リストを取得するメソッド
    public function get_my_following_user_posts($user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT FROM posts JOIN users ON posts.user_id = users.id JOIN follows ON follows.follow_user_id = users.id WHERE follows.follow_user_id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $stmt->execute();
        $my_following_user_posts = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $my_following_user_posts;
    }
    
    // ファイルをアップロードするメソッド
    public function upload(){
        // ファイルを選択していれば
        if (!empty($_FILES['image']['name'])) {
            // ファイル名をユニーク化
            $image = uniqid(mt_rand(), true); 
            // アップロードされたファイルの拡張子を取得
            $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
            $file = USER_IMAGE_DIR . $image;
        
            // uploadディレクトリにファイル保存
            move_uploaded_file($_FILES['image']['tmp_name'], $file);
            
            return $image;
        }else{
            return null;
        }
    }
}
