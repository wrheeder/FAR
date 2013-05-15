<?php
class Model_UserStores extends Model_Table{
    
    public $entity_code = 'user_stores';
    function init() {
        parent::init();
        $this->hasOne('User','user_id','username');
        $this->hasOne('Stores','stores_id','store_name');  
        $this->api->stickyGet('id');
        $this->api->stickyGet('user_id');
        if($_GET['user_id']){
            $this['user_id']=$_GET['user_id'];
        }
    }
}