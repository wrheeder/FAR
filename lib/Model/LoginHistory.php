<?php

class Model_LoginHistory extends Model_Table {

    public $entity_code = 'login_history';

    function init() {
        parent::init();
        $this->hasOne('User',null,'username');
        $this->addField('action');
        $this->addField('date')->type('datetime');
        $this->addField('ip');
    }

}