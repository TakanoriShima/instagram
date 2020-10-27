<?php
// 外部ファイルの読み込み
require_once 'config/Const.php';
require_once 'daos/FollowDAO.php';

// データベースとやり取りを行う便利なクラス
class FollowDAO{
    
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
    
    // follow_user_idを指定して、フォローしているユーザ一覧情報を取得するメソッド
    public function get_my_fallowing_users($follow_user_id){
        $pdo = $this->get_connection();
        
        $stmt = $pdo->prepare("SELECT users.id AS id, users.name AS name, users.nickname AS nickname, users.email AS email, users.password AS password, users.avatar AS avatar, users.profile AS profile, users.created_at AS created_at, users.updated_at AS updated_at, users.last_logined_at AS last_logined_at FROM users JOIN follows ON users.id = follows.follow_user_id WHERE follows.follow_user_id=:follow_user_id");

        $stmt->bindParam(':follow_user_id', $follow_user_id, PDO::PARAM_INT);
        // フェッチの結果を、Userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        
        $my_following_users = $stmt->fetchAll();
        
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $my_following_users;
    }
    
    // followed_user_idを指定して、フォローしてくれているユーザ一覧情報を取得するメソッド
    public function get_my_followed_users($followed_user_id){
        $pdo = $this->get_connection();
        // https://www.dbonline.jp/mysql/select/index20.html#section2
        $stmt = $pdo->prepare("SELECT * FROM users WHERE users.id = any (SELECT follows.follow_user_id FROM follows where follows.followed_user_id = :followed_user_id)");

        $stmt->bindParam(':followed_user_id', $followed_user_id, PDO::PARAM_INT);
        
        // フェッチの結果を、Userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        
        $my_followed_users = $stmt->fetchAll();
        
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $my_followed_users;
    }

    // follow_user_idと、followed_user_id からフォロー情報を抜き出すメソッド
    public function get_follow($follow_user_id, $followed_user_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM follows WHERE follow_user_id = :follow_user_id AND followed_user_id = :followed_user_id');
        $stmt->bindParam(':follow_user_id', $follow_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':followed_user_id', $followed_user_id, PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Favorite');
        $stmt->execute();
        
        $follow = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Favoriteクラスのインスタンスを返す
        return $follow;
    }
    
    // フォローデータを1件登録するメソッド
    public function insert($follow){
    
        if($this->check_following($follow) == 0){

            $pdo = $this->get_connection();
            $stmt = $pdo -> prepare("INSERT INTO follows (follow_user_id, followed_user_id) VALUES (:follow_user_id, :followed_user_id)");
    
            // バインド処理
            $stmt->bindParam(':follow_user_id', $follow->follow_user_id, PDO::PARAM_INT);
            $stmt->bindParam(':followed_user_id', $follow->followed_user_id, PDO::PARAM_INT);
        
            $stmt->execute();
            
            $this->close_connection($pdo, $stmp);
        }
    }
    
    // フォローデータを削除するメソッド
    public function delete($follow){
        if($this->check_following($favorite) == 0){
            $pdo = $this->get_connection();
            $stmt = $pdo -> prepare("DELETE FROM follows WHERE follow_user_id=:follow_user_id AND followed_user_id=:followed_user_id");
    
            // バインド処理
            $stmt->bindParam(':follow_user_id', $follow->follow_user_id, PDO::PARAM_INT);
            $stmt->bindParam(':followed_user_id', $follow->followed_user_id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $this->close_connection($pdo, $stmp);
        }
    }
    
    public function check_following($follow){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("SELECT COUNT(*) AS count FROM follows WHERE follow_user_id = :follow_user_id AND followed_user_id = :followed_user_id");

        // バインド処理
        $stmt->bindParam(':follow_user_id', $follow->follow_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':followed_user_id', $follow->followed_user_id, PDO::PARAM_INT);
        // $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Follow');
        $stmt->execute();
        
        $count = $stmt->fetch()['count'];
        
        $this->close_connection($pdo, $stmp);

        if($count == 1){
            return true;
        }else{
            return false;
        }
    }
}
