<?php

class Model_ItemCollectionForm extends Model_Table {

    public $entity_code = 'transfer_notes';

    function init() {
        parent::init();
//        $this->debug();
        $this->addField('tn_code')->Caption('Cn Code');
        $this->hasOne('User',null,'username')->set($this->api->auth->model->id)->hidden(true);
        $this->addCondition('user_id',$this->api->auth->model->id);
        $this->hasOne('Stores','from_stores_id','store_name')->Caption('From Store');
        $this->hasOne('Stores','to_stores_id','store_name')->Caption('To Store');
        $this->addHook('beforeSave',$this);
        $this->hasMany('ItemTrfList');
        $this->addField('date_created')->type('datetime');
        $this->addField('date_changed')->type('datetime');
        $this->addField('notes');
    }
    
    function beforeSave($model){
        
    }
    function getTransferFormId($tn_code){
        $this->tryLoadBy('tn_code',$tn_code);
        if($this->loaded()){
            return $this->id;
        }else{
            return null;
        }
    }
}