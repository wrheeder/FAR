<?php

class Model_Items extends Model_Table {

    public $entity_code = 'items';

    function init() {
        parent::init();
        $pc = $this->hasOne('PartsCatalogue', null, 'part_number')->mandatory(true)->display(array('form' => 'autocomplete/Basic')); //bring autocomplete in here
        $pc->ref('desciption');
        $this->addField('serial');
        $this->addField('qty')->type('number')->defaultValue(1);
        if ($_GET['sel_store']) {
            $this->hasOne('Stores', null, 'store_name')->mandatory(true)->defaultValue($_GET['sel_store']);
        } else {
            $this->hasOne('Stores', null, 'store_name')->mandatory(true);
        }

        $this->hasMany('ItemTrfList');
        $this->hasOne('PartStatus', null, 'status')->mandatory(true)->defaultValue(1);
        $this->hasMany('ItemBookingList');
        $this->hasOne('Locators', null, 'locator')->Caption('Locater')->display(array('form' => 'autocomplete/Basic'));
        $this->addHook('beforeSave', $this);
    }

    function beforeSave($model) {
        //die($model->get('part_status_id'));
        $pc = $model->ref('parts_catalogue_id');
        $status = $model->get('part_status_id');
        $qty_to_add = $model->get('qty');
        $store_id = $model->get('stores_id');
        $locator_id = $model->get('locators_id');
        $serial = $model->get('serial');
        //$this->unload();
        if ($pc->loaded()) {
            if (!$pc->get('serialized')) {
                $this->addCondition('part_status_id', $status);
                $this->addCondition('stores_id', $store_id);
                $this->tryLoadBy('parts_catalogue_id', $pc->id);
                if ($this->loaded()) {
                    $this->set('locators_id', $locator_id);
                    if ( $this->get('qty') == 0) { //removed this here because it could cause issues --- test  ;$this->get('qty') == 1 ||
                        $this->set('qty', $qty_to_add);
                    } else {
                        $this->set('qty', $this->get('qty') + $qty_to_add);
                    }
                } else {
                    
                    $this->set('parts_catalogue_id', $pc->id);
                    $this->set('stores_id', $store_id);
                    $this->set('part_status_id', $status); 
                    $this->set('locators_id', $locator_id); 
                    $this->set('qty', $qty_to_add);

                }
            }
//            else{
//                $this->set('stores_id', $store_id);
//                $this->set('serial', $serial);
//                $this->set('parts_catalogue_id', $pc->id);
//                $this->set('locators_id', $locator_id); 
//                $this->set('qty', $qty_to_add);
//            }
        }
        $this->set('part_status_id', $model->get('part_status_id'));
    }

}