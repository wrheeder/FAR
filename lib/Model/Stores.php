<?php

class Model_Stores extends Model_Table {

    public $entity_code = 'stores';
    public $title_field = 'store_name';
    public $store_type = null;
    function init() {
        parent::init();
        $this->addField('store_name')->caption('Name')->mandatory(true);
        $this->hasOne('storetype','store_type_id','type')->mandatory(true);
        $this->hasOne('Stores','parent_store_id','store_name');
        $this->add('dynamic_model\Controller_AutoCreator');
        $this->hasMany('UserStores','stores_id');
        $this->hasMany('Locators');
        if($this->owner->owner instanceof Model_Stores && $this->owner->owner->store_type){
            $this->addCondition('store_type','Regional Store');
        }elseif($this->store_type){
            $this->addCondition('store_type',$this->store_type);
        }
        if($this->owner->short_name=='parent_store_id'){
            $this->addCondition('store_type','Regional Store');
        }
    }

}