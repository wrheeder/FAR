<?php
class Model_Supplier extends Model_Table{
    
    public $entity_code = 'supplier';
    function init() {
        parent::init();
        $this->addField('supplier_name')->mandatory(true);
        $this->addField('supplier_type')->enum(array('Supplier','Manufacturer'));
        $this->addField('po_box');
        $this->addField('town');
        $this->addField('postal_code');
        $this->addField('email');
        $this->addField('phone');
        $this->addField('fax');
    }
}