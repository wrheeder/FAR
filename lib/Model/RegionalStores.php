<?php
class Model_REgionalStores extends Model_Stores{
    function init(){
        parent::init();
        $this->addCondition('store_type','Regional Store');
    }
}