<?php
class Model_Locators extends Model_Table{
    
    public $entity_code = 'locators';
    
    function init() {
        parent::init();
//        $this->debug();
        $this->hasOne('Stores',null,'store_name');
        $this->api->stickyGet('stores_id');
       // if($_GET['store_id']) $this->addCondition('stores_id',$_GET['store_id']);
        $this->addField('locator')->mandatory();
    }
}