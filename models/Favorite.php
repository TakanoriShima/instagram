<?php
require_once 'daos/FavoriteDAO.php';
class Favorite{
    
    public $id;
    public $user_id;
    public $post_id;
    public $created_at;
    
    public function __construct($user_id="", $post_id=""){
        $this->user_id = $user_id;
        $this->post_id = $post_id;
    }
    
    public function get_favoriting_users(){
        $favorite_dao = new FavoriteDAO();
        $favoriting_users = $favorite_dao->get_favoriting_users($this->post_id);
        return $favoriting_users;
    }
}
?>