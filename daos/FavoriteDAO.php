<?php
// 外部ファイルの読み込み
require_once 'config/Const.php';
require_once 'models/Favorite.php';

// データベースとやり取りを行う便利なクラス
class FavoriteDAO{
    
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
    
    // user_idを指定して、全テーブル情報を取得するメソッド
    public function get_my_favoriting_posts($user_id){
        $pdo = $this->get_connection();
        
        $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id=:user_id");

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // フェッチの結果を、Favoriteクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Favorite');
        
        $stmt->execute();
        
        $my_favoriting_posts = $stmt->fetchAll();
        
        $this->close_connection($pdo, $stmp);
        // Favoriteラスのインスタンスの配列を返す
        return $my_favoriting_posts;
    }
    
        
    // user_idと、post_id からいいね情報を抜き出すメソッド
    public function get_favorite($user_id, $post_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM favorites WHERE user_id = :user_id AND post_id = :post_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Favorite');
        $stmt->execute();
        
        $favorite = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Favoriteクラスのインスタンスを返す
        return $favorite;
    }
    
    // いいねデータを1件登録するメソッド
    public function insert($favorite){
        if($this->check_favoriting($favorite) === false){
            $pdo = $this->get_connection();
            $stmt = $pdo -> prepare("INSERT INTO favorites (user_id, post_id) VALUES (:user_id, :post_id)");
    
            // バインド処理
            $stmt->bindParam(':user_id', $favorite->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':post_id', $favorite->post_id, PDO::PARAM_INT);
        
            $stmt->execute();
            
            $this->close_connection($pdo, $stmp);
        }
    }
    
    // いいねデータを削除するメソッド
    public function delete($favorite){
        if($this->check_favoriting($favorite) === true){
            $pdo = $this->get_connection();
            $stmt = $pdo -> prepare("DELETE FROM favorites WHERE user_id=:user_id AND post_id=:post_id");
    
            // バインド処理
            $stmt->bindParam(':user_id', $favorite->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':post_id', $favorite->post_id, PDO::PARAM_INT);
        
            $stmt->execute();
            
            $this->close_connection($pdo, $stmp);
        }
    }
    
    public function check_favoriting($favorite){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("SELECT * FROM favorites WHERE user_id = :user_id AND post_id = :post_id");

        // バインド処理
        $stmt->bindParam(':user_id', $favorite->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $favorite->post_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Favorite');
        $stmt->execute();
        
        $favoriting_count = count($stmt->fetchAll());
        $this->close_connection($pdo, $stmp);
        
        if($favoriting_count == 1){
            return true;
        }else{
            return false;
        }
    }
}
