<?php
class Model_PartStatus extends Model_Table{
    
    public $entity_code = 'part_status';
    
    function init() {
        parent::init();
        $this->addField('status');
    }
}