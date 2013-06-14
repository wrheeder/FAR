<?php

class Page_SingleTransfer extends Page {

    function init() {
        parent::init();
        $this->api->stickyGet('spare_id');
        $spare = $this->add('Model_Spare')->load($_GET['spare_id']);
        $f = $this->add('Form');
        $f->add('H3')->set('Transfer single item to Transit/Funcitonal Store');
        $f->setModel($spare);
//        $f->getElement('part_number')->setAttr('disabled',true);
//        $f->getElement('description')->setAttr('disabled',true);
//        $f->getElement('unit_of_measure_id')->setAttr('disabled',true);
//        $f->getElement('stores_id')->setAttr('disabled',true);
//        $f->getElement('finance_track')->setAttr('disabled',true);
//        $f->getElement('serialized')->setAttr('disabled',true);
//        $f->getElement('secondary_category_id')->setAttr('disabled',true);
//        $f->getElement('tertiary_category_id')->setAttr('disabled',true);
//        $f->getElement('supplier_id')->setAttr('disabled',true);
//        $f->getElement('alternative_part_number')->setAttr('disabled',true);
//        
//        
        
        $f->addSeparator();
        
        $m_usr = $this->add("Model_User")->addCondition('id', $this->api->auth->model->id);
        $m_usr->loadAny();
        $m_reg_store = $m_usr->ref("UserStores");
        $available_stores = $m_reg_store->getRows();
        $dest_store_ids = array();
        foreach($available_stores as $store_option){
            $dest_store_ids[]=$store_option['stores_id'];
        }
        $dest_stores =  $this->add('Model_Stores');
        $dest_stores->addCondition('id','in',$dest_store_ids);
        $dest_stores->addCondition('store_type','Regional Transit Store');
        $out = array();
        foreach($dest_stores as $dest_store){
            $out[$dest_store['id']]=$dest_store['store_name'];
        }
   
        
        $dd = $f->addField('Dropdown','Destination Store');
        
        $dd->setValueList($out);
        
        if ($f->isSubmitted()) {
            $f->set('stores_id',$dd->get());
            $log = $this->add('Model_TransferLog');
            $output = array('spare_id'=>$_GET['spare_id'],'user_id'=>$this->api->auth->model->id,'from_stores_id'=>$f->model->get('stores_id'),'to_stores_id'=>$dd->get(),'time'=>date("Y-m-d H:i:s"));
            //die(var_dump($output));
            $log->set($output);
            $log->save();
            //$f->model->set('stores_id',10);
            $js = array();
            $js[] = $f->js()->univ()->getFrameOpener()->closest('.atk4_grid')->atk4_grid('reload');
            $js[]=$f->js()->univ()->successMessage('Transfered Item successfully!');
            $f->update();
            $f->js(true,$js)->univ()->closeDialog()->execute();
        }
    }

}