<?php

class Model_UnitOfMeasure extends Model_Table {

    public $entity_code = 'unit_of_measure';

    function init() {
        parent::init();
        $this->addField('uom');
    }

}