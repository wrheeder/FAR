<?php

class Model_StoreType extends Model_Table {

    public $entity_code = "store_type";

    function init() {
        parent::init();
        $this->addField('type');
    }

}