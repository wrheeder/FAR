<?php
class page_admin_UserStore extends Page{
    function init() {
        parent::init();
        $m=$this->add("Model_UserStores")->addCondition('user_id',$_GET['user_id']);
        $this->api->stickyGet('user_id');
        $crud=$this->add("CRUD");
        $crud->setModel($m);
        
        if($crud->grid){
            
        }
    }
}