<?php

class Model_ItemBookingForm extends Model_Table {

    public $entity_code = 'item_booking_form';

    function init() {
        parent::init();
//        $this->debug();
        $this->addField('booking_nr');
        $this->addField('purchase_order');
        $this->addField('delivery_note');
        $this->hasOne('User',null,'username')->set($this->api->auth->model->id)->hidden(true);
        $this->addCondition('user_id',$this->api->auth->model->id);
        $this->hasOne('Stores',null,'store_name');
        $this->addHook('beforeSave',$this);
        $this->hasMany('ItemBookingList');
        $this->addField('date_created')->type('datetime');
        $this->addField('date_changed')->type('datetime');
        $this->addField('notes');
    }
    
    function beforeSave($model){
        
    }
    function getBookingFormId($booking_nr){
        $this->tryLoadBy('booking_nr',$booking_nr);
        if($this->loaded()){
            return $this->id;
            
        }else{
            return null;
        }
    }
}