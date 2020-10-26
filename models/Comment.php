<?php
class Comment{
    
    public $id;
    public $user_id;
    public $post_id;
    public $body;
    public $created_at;
    
    public function __construct($user_id="", $post_id="", $body=""){
        $this->user_id = $user_id;
        $this->post_id = $post_id;
        $this->body = $body;
    }
}
?>