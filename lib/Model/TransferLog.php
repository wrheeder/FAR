<?php

class Model_TransferLog extends Model_Table {

    public $entity_code = 'movement_log';

    function init() {
        parent::init();
        $this->hasOne('Items',null,'parts_catalogue'); //going through item->part_catalogue returning part number  ////,null,'parts_catalogue.part_number'? not working check
        $this->hasOne('User',null,'username');
        $this->hasOne('Stores','from_stores_id','store_name');
        $this->hasOne('Stores','to_stores_id','store_name');
        $this->addField('time')->type('datetime');
        $this->addField('system_comment');
        
    }

}