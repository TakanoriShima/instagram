<?php
// 外部ファイルの読み込み
require_once 'config/Const.php';
require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'models/Comment.php';

// データベースとやり取りを行う便利なクラス
class CommentDAO{
    
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
    
    // comment_idを指定して、ユーザ情報を取得するメソッド
    public function get_comment_user_by_comment_id($comment_id){
        $pdo = $this->get_connection();
        
        $stmt = $pdo->prepare("SELECT * users JOIN comments ON users.id = comments.user_id WHERE comments.id=:comment_id");

        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        // フェッチの結果を、Userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $commenting_user = $stmt->fetch();
        
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $commenting_user;
    }
    
    // comment_idを指定して、投稿情報を取得するメソッド
    public function get_post_by_comment_id($comment_id){
        $pdo = $this->get_connection();
        
        $stmt = $pdo->prepare("SELECT * posts JOIN comments ON posts.id = comments.post_id WHERE comments.id=:comment_id");

        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        // フェッチの結果を、Userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $commenting_post = $stmt->fetch();
        
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスを返す
        return $commenting_post;
    }
    
    // comment_idを指定して、情報を取得するメソッド
    public function get_post_by_comment_id($comment_id){
        $pdo = $this->get_connection();
        
        $stmt = $pdo->prepare("SELECT * posts JOIN comments ON posts.id = comments.post_id WHERE comments.id=:comment_id");

        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        // フェッチの結果を、Userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $commenting_post = $stmt->fetchAll();
        
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスを返す
        return $commenting_post;
    }
    
    // データを1件登録するメソッド
    public function insert($favorite){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("INSERT INTO favorites (user_id, post_id) VALUES (:user_id, :post_id)");

        // バインド処理
        $stmt->bindParam(':user_id', $comment->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $comment->post_id, PDO::PARAM_INT);
    
        $stmt->execute();
        
        $this->close_connection($pdo, $stmp);
    }
    
    public function check_favoriting($favorite){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND post_id = :post_id");

        // バインド処理
        $stmt->bindParam(':user_id', $favorite->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $favorite->post_id, PDO::PARAM_INT);
    
        $stmt->execute();
        
        // フェッチの結果を、Favoriteクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Favorite');
        $favoriting_count = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        
        if($favoriting_count == 1){
            return true;
        }else{
            return false;
        }
    }
}
