<?php
require_once 'daos/UserDAO.php';
require_once 'daos/PostDAO.php';

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
        return $comments;
    }
}
?>