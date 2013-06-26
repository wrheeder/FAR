<?php

class Model_User extends Model_Table {

    public $entity_code = 'user';

    function init() {
        parent::init();
        $this->addField('username')->mandatory('Username required');
        $this->addField('email')->mandatory('Email required');
        $this->addField('password')->display(array('form' => 'password'))->mandatory('Type your password');
        $this->addField('isAdmin')->type('boolean');
        $this->addField('store_search')->type('boolean');
        $this->addField('pc_search')->type('boolean')->Caption('Parts Catalogue Browser');
        $this->hasMany('UserStores','user_id');
    }
    

}