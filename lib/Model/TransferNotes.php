<?php

class Model_TransferNotes extends Model_Table {

    public $entity_code = 'transfer_notes';

    function init() {
        parent::init();
        $this->addField('tn_code')->set(strtoupper(uniqid("TN".date("Ymd").'_')))->display(array('form'=>'readonly'));
        $this->hasOne('User',null,'username')->set($this->api->auth->model->id)->hidden(true);
        $this->hasOne('Stores','from_stores_id','store_name');
        $this->hasOne('Stores','to_stores_id','store_name');
        
    }

}