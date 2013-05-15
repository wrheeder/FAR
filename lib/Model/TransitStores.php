<?php

class Model_TransitStores extends Model_Stores {

    function init() {
        parent::init();
        $this->addCondition('store_type','Regional Transit Store');
    }

}