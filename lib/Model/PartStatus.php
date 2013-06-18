<?php
class Model_PartStatus extends Model_Table{
    
    public $entity_code = 'part_status';
    public $title_field = 'status';
    
    function init() {
        parent::init();
        $this->addField('status');
    }
}