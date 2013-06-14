<?php
class Page_PickList_CollectItem extends Page{
    function init() {
        parent::init();
                $f = $this->add('Form');
        $f->addField('Hidden', 'sel_store')->set($_GET['sel_store']);
        $f->addField('ReadOnlySave', 'tn_code')->set($_GET['tn_code']);
        //die(var_dump($f->getElement('tn_code')->get()));
        if (!$_GET['tn_code']) {
            $f->getElement('tn_code')->set(strtoupper(uniqid("CN" . date("Ymd") . '_')));
        }
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
        $dest_stores->addCondition('store_type',array('Warehouse','Regional Store'));
        $out = array();
        foreach($dest_stores as $dest_store){
            //if($dest_store['parent_store_id']!=$_GET['sel_store'])
                $out[$dest_store['id']]=$dest_store['store_name'];
        }
   
        
        $dd = $f->addField('Dropdown','Destination Store');
        
        $f->addSubmit('AddItem')->js('click',$dd->js(true)->attr('disabled', false));
        $dd->setValueList($out);
        $dd->set($_GET['Destination_Store']);
        if($_GET['Destination_Store'])
            $dd->js(true)->attr('disabled', true);
//        $f->addField('Line', 'purchase_order')->set($_GET['purchase_order']);
//        $f->addField('Line', 'delivery_note')->set($_GET['delivery_note']);
        $f->addSeparator();
        
        $m_transfer_notes = $this->add('Model_ItemTransferForm');
        $m_items = $f->setModel('ItemsTrf');
        
        //$f->getElement('parts_catalogue')->js(true)->focus();
        $f->getElement('stores_id')->js(true)->closest('.atk-form-row-dropdown')->hide();
        
        $item_list = $this->add('Grid');
        $m_item_list = $this->add('Model_ItemTrfList');
        $tn_code_id=$m_transfer_notes->getTransferFormId($f->getElement('tn_code')->get());
        $m_item_list->addCondition('transfer_notes_id', $tn_code_id);
        $item_list->setModel($m_item_list);
        $pc_info = $f->add('View_PartsCatalogueInfo');
        $f->add('Order')->move($pc_info, 'after', 'parts_catalogue')->now();

        $item_list->addClass("zebra bordered");
        $item_list->addPaginator(5);
        
        $locator = $f->getElement('locators');
        $locator->validateNotNull();
//        $locator->addHook('validate', function() use ($f, $serial, $part_catalogue_id) {
//            $locator->displayFieldError
//        });
        $this->add('Button')->set('Save&Close Transfer Form')->js('click', $this->js()->trigger('reloadpage'))->univ()->closeDialog();
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('tn_code');
        $this->api->stickyGet('Destination_Store');
        ///Validation
        $part_catalogue_id = $f->getElement('parts_catalogue_id');
        $serial = $f->getElement('serial');
        $serial->addHook('validate', function() use ($f, $serial, $part_catalogue_id) {
                    $pc = $f->add('Model_PartsCatalogue');

                    $pc->load($part_catalogue_id->get());
                    if ($pc->get('serialized')) {
                        if ($serial->get() == null) {
                            $serial->displayFieldError('Serial Required for Serialized Part Numbers');
                        } else {
                            $item = $f->add('Model_Items')->addCondition('serial', $serial->get())->addCondition('parts_catalogue_id', $part_catalogue_id->get())->addCondition('stores_id', $f->get('sel_store'));
                            $item->tryLoadAny();
                            if (!$item->loaded()) {
                                $serial->displayFieldError('Item with this serial does not exist in your Transit store');
                            }
                        }
                    } else {
                        if ($serial->get() != null)
                            $serial->displayFieldError('Serial not required for Non Serialized Items');
                        else {
                            $item = $f->add('Model_Items')->addCondition('parts_catalogue_id', $part_catalogue_id->get())->addCondition('stores_id', $f->get('sel_store'));
                            $item->tryLoadAny();
                            if ($item->loaded()) {
                                if ($item->get('qty') < $f->getElement('qty')->get()) {
                                    $f->getElement('qty')->displayFieldError('There arent that many items to collect from Transit Store');
                                }
                            } else {
                                $f->getElement('parts_catalogue')->displayFieldError('This item does not exist in your Transit store');
                            }
                        }
                    }
                });

        ///End of Validation
        if ($f->isSubmitted()) {
              $js = array();
            $orig_qty = $f->getElement('qty')->get();
            $item_id=$f->model->setStore($f->getElement('sel_store')->get(),$f->getElement('parts_catalogue_id')->get(),$f->getElement('serial')->get(),$f->getElement('Destination_Store')->get(),$f->getElement('qty')->get(),$f->getElement('tn_code')->get(),$f->getElement('locators_id')->get());
            //$f->update();
            $m_transfer_notes->tryLoadBy('tn_code', $f->getElement('tn_code')->get());
            $id = null;
            if ($m_transfer_notes->loaded()) {
                $id = $m_transfer_notes->id;
//                $m_transfer_notes->set('purchase_order',$f->getElement('purchase_order')->get());
//                $m_transfer_notes->set('delivery_note',$f->getElement('delivery_note')->get());
                $m_transfer_notes->set('date_changed', date("Y-m-d H:i:s"));
                $m_transfer_notes->save();
            } else {
                $m_transfer_notes->set('tn_code', $f->getElement('tn_code')->get());
//                $m_transfer_notes->set('purchase_order', $f->getElement('purchase_order')->get());
//                $m_transfer_notes->set('delivery_note', $f->getElement('delivery_note')->get());
                $m_transfer_notes->set('to_stores_id', $f->getElement('Destination_Store')->get());
                $m_transfer_notes->set('from_stores_id', $f->getElement('sel_store')->get());
                $m_transfer_notes->set('date_created', date("Y-m-d H:i:s"));
                $m_transfer_notes->set('date_changed', date("Y-m-d H:i:s"));
                $m_transfer_notes->save();
                $id = $m_transfer_notes->id;
            }
            
            $m_item_list->set('transfer_notes_id', $id);
            $m_item_list->set('items_id',$item_id);

            $m_item_list->saveAndUnload();

            $m_transfer_log = $this->add('Model_TransferLog');
            $m_transfer_log->set('user_id', $this->api->auth->model->id);
            $m_transfer_log->set('from_stores_id', $f->getElement('sel_store')->get());
            $m_transfer_log->set('to_stores_id', $f->getElement('Destination_Store')->get());
            $m_transfer_log->set('time', date("Y-m-d H:i:s"));
            $m_transfer_log->set('system_comment', $orig_qty . ' Item(s) collected from Transit Store '.$f->getElement('sel_store')->get().' to Store '.$f->getElement('Destination_Store')->get().' - TN :' . $f->getElement('tn_code')->get().' - Status :'.$f->getElement('part_status')->get());
            $m_transfer_log->set('items_id', $item_id);
            $m_transfer_log->saveAndUnload();
            $js[] = $f->js()->reload(array('Destination_Store' => $f->getElement('Destination_Store')->get(),'tn_code' => $f->getElement('tn_code')->get(), 'sel_store' => $f->getElement('sel_store')->get()));
            $js[] = $item_list->js()->reload(array('tn_code' => $f->getElement('tn_code')->get(), 'sel_store' => $f->getElement('sel_store')->get()));
            $f->js(true, $js)->univ()->successMessage('Item Added')->execute();
        }
        $js = array();
        $js[] = $pc_info->js()->reload(array('part_num' => $f->getElement('parts_catalogue_id')->js()->val(), 'serial_fld' => $f->getElement('serial'), 'qty_fld' => $f->getElement('qty')));
        $this->js('myFunc', $js);
    }
}