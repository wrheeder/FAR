<?php
class Model_ItemBookingList extends Model_Table{
    public $entity_code = 'item_booking_list';
    function init(){
        parent::init();
//        $this->debug();
        $this->hasOne('Items',null,'parts_catalogue')->display(array('form' => 'autocomplete/Plus'));
        $this->hasOne('ItemBookingForm',null,'booking_nr');
        $this->addField('comment');
        $this->addField('qty');
        $this->hasOne('PartStatus',null,'status');
    }
}