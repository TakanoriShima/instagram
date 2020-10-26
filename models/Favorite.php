<?php
class Favorite{
    
    public $id;
    public $user_id;
    public $post_id;
    public $created_at;
    
    public function __construct($user_id="", $post_id=""){
        $this->user_id = $user_id;
        $this->post_id = $post_id;
    }
}
?>