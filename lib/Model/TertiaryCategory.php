<?php

class Model_TertiaryCategory extends Model_Table {

    public $entity_code = 'tertiary_category';

    function init() {
        parent::init();
        $this->addField('category_code')->mandatory(true);
        $this->addField('category')->mandatory(true);
    }

}