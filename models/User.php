<?php
require_once 'daos/UserDAO.php';

class User{
    
    public $id;
    public $name;
    public $nickname;
    public $email;
    public $avatar;
    public $password;
    public $profile;
    public $created_at;
    public $updated_at;
    public $last_logined_at;
    
    public function __construct($name="", $nickname="", $email="", $password="", $avatar=""){
        $this->name = $name;
        $this->nickname = $nickname;
        $this->email = $email;
        $this->password = $password;
        $this->avatar = $avatar;
    }
    
    // 自分の投稿一覧を取得するメソッド
    public function get_my_posts(){
        $user_dao = new UserDAO();
        $my_posts = $user_dao->get_my_posts($this->id);
        return $my_posts;
    }
    
    // 自分がコメントした投稿一覧を取得するメソッド
    public function get_my_commenting_posts(){
        $user_dao = new UserDAO();
        $my_commenting_posts = $user_dao->my_commenting_posts($this->id);
        return $my_commenting_posts;
    }
    
    // 自分がしたコメント一覧を取得するメソッド
    public function get_my_comments(){
        $user_dao = new UserDAO();
        $my_comments = $user_dao->get_my_comments($this->id);
        return $my_comments;
    }
    
    // 自分がいいねした投稿リストを取得するメソッド
    public function get_my_favoriting_posts(){
        $user_dao = new UserDAO();
        $my_favoriting_posts = $user_dao->get_my_favoriting_posts($this->id);
        return $my_favoriting_posts;
    } 
    
    // 自分がフォローしたユーザリストを取得するメソッド
    public function get_my_following_users(){
        $user_dao = new UserDAO();
        $my_following_users = $user_dao->get_my_following_users($this->id);
        return $my_following_users;
    } 
    
    // 自分をフォローしてくれているユーザリストを取得するメソッド
    public function get_my_followed_users (){
        $user_dao = new UserDAO();
        $my_followed_users = $user_dao->get_my_followed_users($this->id);
        return $my_followed_users;
    }
}
?>