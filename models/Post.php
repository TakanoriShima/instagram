<?php
require_once 'daos/UserDAO.php';
require_once 'daos/PostDAO.php';
require_once 'daos/FavoriteDAO.php';

class Post{
    
    public $id;
    public $user_id;
    public $title;
    public $body;
    public $image;
    public $created_at;
    
    public function __construct($user_id="", $title="", $body="", $image=""){
        $this->user_id = $user_id;
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
    }
    
    public function get_user(){
        $user_dao = new UserDAO();
        $user = $user_dao->get_user_by_id($this->user_id);
        return $user;
    }
    
    public function get_comments(){
        $post_dao = new PostDAO();
        $comments = $post_dao->get_comments_by_post_id($this->id);
        // $comment_dao = new CommentDAO();
        // $comments = $comment_dao->get_all_comments();
        return $comments;
    }
    
    // いいね数を求めるメソッド
    public function favoriting_count($user_id){
        $post_dao = new PostDAO();
        $favorited_count = $post_dao->get_favoriting_users($this->id);
        
        return count($favorited_count);
    }
    
    public function check_favoriting($user_id){
        $favorite = new Favorite($user_id, $this->id);
        $post_dao = new PostDAO();
        $check_favoriting = $post_dao->check_favoriting($favorite);
        return $check_favoriting;
    }
}
?>