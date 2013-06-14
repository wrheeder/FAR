<?php
class Model_ItemsJoin extends Model_Table {

    public $entity_code = 'items';

    function init() {
        parent::init();
       // $p_cat = $this->join('parts_catalogue','part_number','left'); //bring autocomplete in here
        $p_cat = $this->join('parts_catalogue', null, 'left');
       // $p_cat->addField('part_number')->display(array('form' => 'autocomplete/Basic'));
        $p_cat->addField('part_number');
        $p_cat->addField('description');
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
    }

}