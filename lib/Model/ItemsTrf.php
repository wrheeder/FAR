<?php

class Model_ItemsTrf extends Model_Items {

    public $entity_code = 'items';
    public $new_store = null;
    public $pc_id = null;
    public $qty_to_add = null;
    public $serialized = null;
    public $locator = null;

    function init() {
        parent::init();
    }

    function setStore($store, $pc, $serial, $to_store, $qty, $tn_code, $locator, $locator_name, $from_status, $to_status) {
        $this->new_store = $to_store;
        $this->current_store = $store;
        $this->locator = $locator;
        ////get some more store info
        $m_stores = $this->add('Model_Stores');
        $m_stores->load($store);
        $store_name = $m_stores->get('store_name');
        $m_stores->load($to_store);
        $to_store_name = $m_stores->get('store_name');
        ////get some more status info
        $m_status = $this->add('Model_PartStatus');
        $m_status->load($from_status);
        $from_status_name = $m_status->get('status');
        $m_status->load($to_status);
        $to_status_name = $m_status->get('status');
        $warrantee = null;
        $version_fw = null;
        ////////////////////////////////
        //
        //die($status);
        /// Load Partscat in from store
        $this->addCondition('stores_id', $store);
        $this->addCondition('part_status_id', $from_status);
        $this->loadBy('parts_catalogue_id', $pc);
        $warrantee = $this->get('warrantee');
        $version_fw = $this->get('version_fw');
        /// Get ref to PartsCat that is loaded
        $parts_cat = $this->ref('parts_catalogue_id');
        $id = $this->id;

        $m_transfer_log = $this->add('Model_TransferLog');
        if ($parts_cat->get('serialized')) {
            $m_items = $this->add('Model_Items');
            $m_items->addCondition('stores_id', $store);
            $m_items->addCondition('serial', $serial);
            $m_items->LoadBy('parts_catalogue_id', $pc);
            $m_items->set('stores_id', $to_store);
            $m_items->set('locators_id', $locator);
            $m_items->set('part_status_id', $to_status);
            $id = $m_items->id;
            $m_items->saveAndUnload();
            $m_transfer_log->set('user_id', $this->api->auth->model->id);
            $m_transfer_log->set('from_stores_id', $store);
            $m_transfer_log->set('to_stores_id', $to_store);
            $m_transfer_log->set('time', date("Y-m-d H:i:s"));
            $m_transfer_log->set('system_comment', $qty . ' Item(s) transfered from [' . $store_name . '] to [' . $to_store_name . '] - TN :' . $tn_code . ' Locator :[' . $locator_name . ']' . '- From Status ->' . $from_status_name . ' Dest Status ->' . $to_status_name);
            $m_transfer_log->set('items_id', $id);
            $m_transfer_log->saveAndUnload();
        } else {
            //Non serialized, from Store already loaded, adjust qty, write log
//            $this->unload();
//            $this->addCondition('stores_id', $store);
//            $this->addCondition('part_status_id',$status);
//            $this->tryLoadBy('parts_catalogue_id', $pc);
            $this->set('qty', -$qty);
            $this->save();
            $m_transfer_log->set('user_id', $this->api->auth->model->id);
            $m_transfer_log->set('from_stores_id', $store);
            $m_transfer_log->set('to_stores_id', $to_store);
            $m_transfer_log->set('time', date("Y-m-d H:i:s"));
            $m_transfer_log->set('system_comment', $qty . ' Item(s) transfered from [' . $store_name . '] to [' . $to_store_name . '] - TN :' . $tn_code . ' Locator :[' . $locator_name . ']' . '- From Status ->' . $from_status_name . ' Dest Status ->' . $to_status_name);
            $m_transfer_log->set('items_id', $id);
            $m_transfer_log->saveAndUnload();
            //Add Moved QTY to Dest Store, first check if exists
            $this->unload();
            $m_items = $this->add('Model_Items');

            $m_items->addCondition('stores_id', $to_store);
            $m_items->addCondition('part_status_id', $to_status);
            $this->tryLoadBy('parts_catalogue_id', $pc);
            $m_items->tryLoadBy('parts_catalogue_id', $pc);
            if ($m_items->loaded()) {
                //this item exists in destination store - update qty
                $m_items->set('locators_id', $locator);
                $m_items->set('qty', $qty);
                $m_items->save();
                $to_id = $m_items->id;
                $m_transfer_log->set('user_id', $this->api->auth->model->id);
                $m_transfer_log->set('from_stores_id', $store);
                $m_transfer_log->set('to_stores_id', $to_store);
                $m_transfer_log->set('time', date("Y-m-d H:i:s"));
                $m_transfer_log->set('system_comment', $qty . ' Item(s) transfered from [' . $store_name . '] to [' . $to_store_name . '] - TN :' . $tn_code . ' Locator :[' . $locator_name . ']' . '- From Status ->' . $from_status_name . ' Dest Status ->' . $to_status_name);
                $m_transfer_log->set('items_id', $to_id);
                $m_transfer_log->saveAndUnload();
            } else {
                //this item does not exist in destination store yet - will be created
                $m_items->set('parts_catalogue_id', $pc);
                $m_items->set('warrantee', $warrantee);
                $m_items->set('version_fw', $version_fw);
                $m_items->set('stores_id', $to_store);
                $m_items->set('part_status_id', $to_status); ////to modify function later to accomodate part status to be sent.(now default new)
                $m_items->set('locators_id', $locator);  //here the locator id should be copied
                $m_items->set('qty', $qty);

                $m_items->save();
                $to_id = $m_items->id;
                $m_transfer_log->set('user_id', $this->api->auth->model->id);
                $m_transfer_log->set('from_stores_id', $store);
                $m_transfer_log->set('to_stores_id', $to_store);
                $m_transfer_log->set('time', date("Y-m-d H:i:s"));
                $m_transfer_log->set('system_comment', $qty . ' Item(s) transfered from [' . $store_name . '] to [' . $to_store_name . '] - TN :' . $tn_code . ' Locator :[' . $locator_name . ']' . '- From Status ->' . $from_status_name . ' Dest Status ->' . $to_status_name);
                $m_transfer_log->set('items_id', $to_id);
                $m_transfer_log->saveAndUnload();
            }
            //write log using to_id
        }
        return $id;
    }

    function beforeSave($model) {
        if ($this->serialized)
            $this->set('stores_id', $this->new_store);
        parent::beforeSave($model);
    }

}

////        //die($store.'-'.$pc.'-'.$serial.'-'.$to_store);
////        
//        if ($to_store == null) {
//            die('to_Store not defined');
//        }
//        $id = 9999;
//        $this->loadBy('parts_catalogue_id', $pc);
//        $parts_cat = $this->ref('parts_catalogue_id');
//        $this->qty_to_add = $this->get('qty');
//        $this->pc_id = $parts_cat->id;
//        $store_id = $this->get('stores_id');
//        if ($parts_cat->loaded()) {
//            if (!$parts_cat->get('serialized')) {
//                $this->serialized = false;
//                die('not loaded');
//            } else {
//                $this->serialized = true;
//                $this->addCondition('parts_catalogue_id', $pc)->addCondition('stores_id', $store);
//                $id = $this->loadBy('serial', $serial)->id;
//                $this->set('locators_id', $locator);
//                $this->saveAndUnload();
//            }
//        }
//        return $id;
//
//$m_to_store = $this->add('Model_Items')->addCondition('stores_id', $to_store)->addCondition('parts_catalogue_id', $pc);
//                $m_to_store->tryLoadAny();
//                if ($m_to_store->loaded()) {
//                    //this non serialised item exists in dest store, just add the qty and save ... dont forget to remove transfered qty from current store
//                    $m_to_store->set('qty', $qty);
//                    $m_to_store->set('stores_id', $to_store);
//                    $id =$m_to_store->save()->id;
//                } else {
//                    $m_to_store->set('parts_catalogue_id', $pc);
//                    $m_to_store->set('stores_id', $to_store);
//                    $m_to_store->set('qty', $qty);
//                    $id = $m_to_store->saveAndUnload()->id;
//                    $m_transfer_log = $this->add('Model_TransferLog');
//                    $m_transfer_log->set('user_id', $this->api->auth->model->id);
//                    $m_transfer_log->set('from_stores_id', $store);
//                    $m_transfer_log->set('to_stores_id', $to_store);
//                    $m_transfer_log->set('time', date("Y-m-d H:i:s"));
//                    $m_transfer_log->set('system_comment', $qty . '+ Item(s) transfered from Store ' . $store . ' to Transit Store ' . $to_store . ' - TN :' . $tn_code . ' Locator :' . $locator);
//                    $m_transfer_log->set('items_id', $id);
//                    $m_transfer_log->saveAndUnload();
//
//                    //create this item in dest_store, with transfer qty and subtract transfer qty from current store
//                }
//                $m_transfer_log = $this->add('Model_TransferLog');
//                $m_transfer_log->set('user_id', $this->api->auth->model->id);
//                $m_transfer_log->set('from_stores_id', $store);
//                $m_transfer_log->set('to_stores_id', $to_store);
//                $m_transfer_log->set('time', date("Y-m-d H:i:s"));
//                $m_transfer_log->set('system_comment', $qty . '- Item(s) transfered from Store ' . $store . ' to Transit Store ' . $to_store . ' - TN :' . $tn_code . ' Locator :' . $locator);
//                $m_transfer_log->set('items_id', $id);
//                $m_transfer_log->saveAndUnload();
//
//                $this->addCondition('parts_catalogue_id', $pc)->addCondition('stores_id', $store);
//                $id = $this->loadAny()->id;
//                $this->set('qty', -$qty);
//                $this->set('locators_id', $locator);
//                $this->saveAndUnload();