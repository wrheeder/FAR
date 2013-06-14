<?php
class Model_ItemTrfList extends Model_Table{
    public $entity_code='items_transfer_list';
    function init() {
        parent::init();
        $this->hasOne('Items',null,'parts_catalogue');
        $this->hasOne('TransferNotes',null,'tn_code');
        $this->addField('comment')->type('Text');
        $this->addField('qty');
        $this->hasOne('PartStatus','from_part_status_id','status');
        $this->hasOne('PartStatus','to_part_status_id','status');
    }
}