<?php

class Model_PrimaryCategory extends Model_Table {

    public $entity_code = 'primary_category';

    function init() {
        parent::init();
        $this->addField('category_code')->mandatory(true);
        $this->addField('category')->mandatory(true);
    }

}