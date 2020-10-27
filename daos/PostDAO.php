<?php
// 外部ファイルの読み込み
require_once 'config/Const.php';
require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'models/Comment.php';
require_once 'models/Favorite.php';

// postsとやり取りを行う便利なクラス
class PostDAO{
    
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
    
    // 全投稿情報を取得するメソッド
    public function get_all_posts(){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM posts ORDER BY id DESC');
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'post');
        $stmt->execute();
        $posts = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $posts;
    }
    
    // id値からデータを抜き出すメソッド
    public function get_post_by_id($post_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Post');
        $stmt->execute();
        $post = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスを返す
        return $post;
    }
    
    // 画像ファイル名を取得するメソッド（uploadフォルダ内のファイルを物理削除するため）
    public function get_image_name_by_id($post_id){
       
        $post = $this->get_post_by_id($post_id);

        $this->close_connection($pdo, $stmp);
        
        return $post->image;
    }
    
    // データを1件登録するメソッド
    public function insert($post){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("INSERT INTO posts (user_id, title, body, image) VALUES (:user_id, :title, :body, :image)");
        // バインド処理
        $stmt->bindParam(':user_id', $post->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $post->title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $post->body, PDO::PARAM_STR);
        $stmt->bindParam(':image', $post->image, PDO::PARAM_STR);
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
    }
    
    
    // データを更新するメソッド
    public function update($post){
        $pdo = $this->get_connection();
        $image = $this->get_image_name_by_id($post->id);
        $stmt = $pdo->prepare('UPDATE posts SET title=:title, body=:body, image=:image WHERE id = :post_id');
                        
        $stmt->bindParam(':title', $post->title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $post->body, PDO::PARAM_STR);
        $stmt->bindParam(':image', $post->image, PDO::PARAM_STR);
        $stmt->bindParam(':post_id', $post->id, PDO::PARAM_INT);   
        
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
        
        // 画像の物理削除
        if($image !== $post->image){
            unlink(POST_IMAGE_DIR . $image);
        }
    }
    
    // データを削除するメソッド
    public function delete($id){
        $pdo = $this->get_connection();
        $image = $this->get_image_name_by_id($id);
        
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
        
        unlink(POST_IMAGE_DIR . $image);

    }
    
    // 投稿したユーザ情報を取得するメソッド
    public function get_user_by_post_id($post_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT users.id AS id, users.name AS name, users.nickname AS nickname, users.email AS email, users.password AS password, users.avatar AS avatar, users.profile AS profile, users.created_at AS created_at, users.updated_at AS updated_at, users.last_logined_at AS last_logined_at FROM users JOIN posts ON users.id = posts.user_id WHERE posts.user_id = :post_id');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $posting_user = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $posting_user;
    }
    
    // コメント一覧を取得するメソッド
    public function get_comments_by_post_id($post_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT comments.id AS id, comments.user_id AS user_id, comments.post_id AS post_id, comments.body AS body, comments.created_at AS created_at FROM comments JOIN posts ON comments.post_id = posts.id WHERE comments.post_id = :post_id');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Comment');
        $post_comments = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Commentクラスのインスタンスの配列を返す
        return $post_comments;
    }
    
    // コメントしたユーザ一覧を取得するメソッド
    public function get_users_by_post_id($post_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT users.id AS id, users.name AS name, users.nickname AS nickname, users.email AS email, users.password AS password, users.avatar AS avatar, users.profile AS profile, users.created_at AS created_at, users.updated_at AS updated_at, users.last_logined_at AS last_logined_at FROM users JOIN comments ON users.id = comments.user_id WHERE comments.post_id = :post_id');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        // フェッチの結果を、Userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $post_comments = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Userクラスのインスタンスの配列を返す
        return $post_comments;
    }
    
    
    // いいねしたユーザ一覧情報を取得するメソッド
    public function get_favoriting_users ($post_id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT users.id AS id, users.name AS name, users.nickname AS nickname, users.email AS email, users.password AS password, users.avatar AS avatar, users.profile AS profile, users.created_at AS created_at, users.updated_at AS updated_at, users.last_logined_at AS last_logined_at FROM users JOIN favorites ON users.id = favorites.user_id WHERE favorites.post_id = :post_id');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        // フェッチの結果を、Postクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
        $stmt->execute();
        
        $favoriting_users = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // Postクラスのインスタンスの配列を返す
        return $favoriting_users;
    }
    
    // その投稿をいいねしているか判定するメソッド
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
    
    // ファイルをアップロードするメソッド
    public function upload(){
        // ファイルを選択していれば
        if (!empty($_FILES['image']['name'])) {
            // ファイル名をユニーク化
            $image = uniqid(mt_rand(), true); 
            // アップロードされたファイルの拡張子を取得
            $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
            $file = POST_IMAGE_DIR . $image;
        
            // uploadディレクトリにファイル保存
            move_uploaded_file($_FILES['image']['tmp_name'], $file);
            
            return $image;
        }else{
            return null;
        }
    }
}
