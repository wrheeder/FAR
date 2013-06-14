<?php

class Model_PartsCatalogue extends Model_Table {

    public $entity_code = 'parts_catalogue';

    function init() {
        parent::init();
        $this->addField('part_number')->mandatory(true);
        $this->addField('description');
        $this->hasOne('UnitOfMeasure',null,'uom');
        $this->addField('finance_track')->type('boolean')->mandatory(true);
        $this->addField('serialized')->type('boolean')->mandatory(true);
        $this->addField('warrantee');
        $this->addField('asset_tag_managed')->type('boolean')->mandatory(true);
        $this->hasOne('PrimaryCategory',null,'category')->mandatory(true);
        $this->hasOne('SecondaryCategory',null,'category')->mandatory(true);
        $this->hasOne('TertiaryCategory',null,'category')->mandatory(true);
        $this->hasOne('Supplier',null,'supplier_name');
        $this->addField('alternative_part_number');
    }

}