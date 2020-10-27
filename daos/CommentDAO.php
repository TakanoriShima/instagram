<?php
// 外部ファイルの読み込み
require_once 'config/Const.php';
require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'models/Comment.php';

// postsとやり取りを行う便利なクラス
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
    
    // 全コメント情報を取得するメソッド
    public function get_all_comments(){
        $pdo = $this->get_connection();
        $stmt = $pdo->query('SELECT * FROM comments ORDER BY id DESC');
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Comment');
        $stmt->execute();
        $comments = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $comments;
    }
    
    // id値からコメントを抜き出すメソッド
    public function get_comment_by_id($comment_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = :id');
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Comment');
        $stmt->execute();
        $comment = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスを返す
        return $comment;
    }
    
    // コメントを1件登録するメソッド
    public function insert($comment){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("INSERT INTO comments (user_id, post_id, body) VALUES (:user_id, :post_id, :body)");
        // バインド処理
        $stmt->bindParam(':user_id', $comment->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $comment->post_id, PDO::PARAM_INT);
        $stmt->bindParam(':body', $comment->body, PDO::PARAM_STR);
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
    }
    
    // コメントしたユーザ情報を取得するメソッド
    public function get_user_by_comment_id($comment_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM users JOIN comments ON users.id = comments.user_id WHERE comments.id = :comment_id');
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        $commenting_user = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスを返す
        return $commenting_user;
    }
    
    // コメントした投稿を取得するメソッド
    public function get_post_by_comment_id($comment_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT comments.id AS id, comments.user_id AS user_id, comments.post_id AS post_id, comments.body AS body, comments.created_at AS created_at FROM posts JOIN comments ON comments.post_id = posts.id WHERE comments.comment_id = :comment_id');
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $stmt->execute();
        $commenting_post = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Commentクラスのインスタンスの配列を返す
        return $commenting_post;
    }
}
