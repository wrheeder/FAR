<?php

class Model_SecondaryCategory extends Model_Table {

    public $entity_code = 'secondary_category';

    function init() {
        parent::init();
        $this->addField('category_code')->mandatory(true);
        $this->addField('category')->mandatory(true);
    }

}