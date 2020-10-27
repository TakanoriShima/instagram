<?php
class Follow{
    
    public $id;
    public $follow_user_id;
    public $followed_user_id;
    public $created_at;
    
    public function __construct($follow_user_id="", $followed_user_id=""){
        //if($this->follow_user_id !== $this->followed_user_id ){
            $this->follow_user_id = $follow_user_id;
            $this->followed_user_id = $followed_user_id;
        //}
    }
}
?>