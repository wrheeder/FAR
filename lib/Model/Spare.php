<?php

class Model_Spare extends Model_Table {

    public $entity_code = 'spare';

    function init() {
        parent::init();
        $this->addField('part_number')->mandatory(true);
        $this->addField('description');
        $this->hasOne('UnitOfMeasure',null,'uom');
        $this->hasOne('Stores',null,'store_name')->mandatory(true);
        $this->addField('finance_track')->type('boolean')->mandatory(true);
        $this->addField('serialized')->type('boolean')->mandatory(true);
        $this->hasOne('SecondaryCategory',null,'category')->mandatory(true);
        $this->hasOne('TertiaryCategory',null,'category')->mandatory(true);
        $this->hasOne('Supplier',null,'supplier_name');
        $this->addField('alternative_part_number');
    }

}