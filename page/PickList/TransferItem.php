<?php

class Page_PickList_TransferItem extends Page {

    public $is_serialized = true;

    function init() {
        parent::init();
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('tn_code');
        $this->api->stickyGet('Destination_Store');
        $this->api->stickyGet('home_store');
        $this->api->stickyGet('home_store_type');
        $this->api->stickyGet('notes');
        $this->api->stickyGet('store_type');
        $f = $this->add('Form');
        $f->addField('Hidden', 'sel_store')->set($_GET['sel_store']);
        $f->addField('ReadOnlySave', 'tn_code')->set($_GET['tn_code']);
        $f->addField('Text', 'notes')->set($_GET['notes']);
        if (!$_GET['tn_code']) {
            $f->getElement('tn_code')->set(strtoupper(uniqid("TN" . date("ymd") . '_')));
        }
        $m_usr = $this->add("Model_User")->addCondition('id', $this->api->auth->model->id);
        $m_usr->loadAny();
        $m_reg_store = $m_usr->ref("UserStores");
        $available_stores = $m_reg_store->getRows();
        $dest_store_ids = array();
        foreach ($available_stores as $store_option) {
            $dest_store_ids[] = $store_option['stores_id'];
        }
        if ($_GET['store_type'] == 'Regional Stock Van') {
            $m_stores = $this->add('Model_Stores');
            $m_stores->addCondition('store_type', 'Site');
            $m_stores->setOrder('store_name', 'asc');
            $sites = $m_stores->getRows();
            foreach ($sites as $cur_site) {
                $dest_store_ids[] = $cur_site['id'];
            }
        }
        $dest_stores = $this->add('Model_Stores');
        $dest_stores->addCondition('id', 'in', $dest_store_ids);
        if ($_GET['store_type'] == 'Regional Stock Van') {
            $dest_stores->addCondition('store_type', array('Regional Transit Store', 'Site'));
        } else {
            $dest_stores->addCondition('store_type', 'Regional Transit Store');
        }
        $out = array();

        $m_locaters = $this->add('model_locators');
        if ($_GET['Destination_Store'] && !$_GET['dd_change']) {
            foreach ($dest_stores as $dest_store) {
                if ($dest_store['id'] == $_GET['Destination_Store']) {
                    $out[$dest_store['id']] = $dest_store['store_name'];
                }
            }
//             $m_locaters->addCondition('stores_id',$_GET['Destination_Store']);
        } else {
            foreach ($dest_stores as $dest_store) {
                // if ($dest_store['parent_store_id'] != $_GET['sel_store']) {
                $out[$dest_store['id']] = $dest_store['store_name'];
                // }
            }
//           $m_locaters->addCondition('stores_id',$_GET['Destination_Store']);
        }
        //loading sites
        $m_stores = $this->add('Model_Stores');
        $m_stores->unload();
        if ($_GET['store_type'] == 'Regional Stock Van') {
            $m_home_store = $m_stores->load($_GET['home_store']);
            $m_home_transit_store = $m_stores->load($m_home_store->get('parent_store_id'));
            $m_region_warehouse = $m_stores->load($m_home_transit_store->get('parent_store_id'));
            $tmp_id = $m_region_warehouse->id;
            $m_stores->unload();
            $m_stores->addCondition('parent_store_id', $tmp_id);
            $m_stores->addCondition('store_type', 'Site');
            $sites = $m_stores->getRows();
            foreach ($sites as $site) {
                $out[$site['id']] = $site['store_name'];
            }
        }

        //
        //
        
        $dd = $f->addField('Dropdown', 'Destination Store');
        $dd->setValueList($out);
        if ($_GET['Destination_Store']) {
            $dd->set($_GET['Destination_Store']);
            $m_locaters->addCondition('stores_id', $_GET['Destination_Store']);
        } else {
            reset($out);
            $_GET['Destination_Store'] = key($out);
            $m_locaters->addCondition('stores_id', $_GET['Destination_Store']);
        }


//if ($_GET['Destination_Store'])
        //$dd->js(true)->attr('disabled', true);
//        $f->addField('Line', 'purchase_order')->set($_GET['purchase_order']);
//        $f->addField('Line', 'delivery_note')->set($_GET['delivery_note']);
        $f->addSeparator();
        $m_transfer_notes = $this->add('Model_ItemTransferForm');
        $m_items = $f->setModel('ItemsTrf');
        $f->addField('Text', 'Comment');
        //$f->getElement('parts_catalogue')->js(true)->focus();
        $f->getElement('stores_id')->js(true)->closest('.atk-form-row-dropdown')->hide();
        $f->getElement('warrantee')->js(true)->closest('.atk-form-row')->hide();
        $f->getElement('version_fw')->js(true)->closest('.atk-form-row')->hide();

//        $f->getElement('locators')->js(true)->closest('.atk-form-row-line');
        $item_list = $this->add('Grid');
        $m_item_list = $this->add('Model_ItemTrfList');
        $tn_code_id = $m_transfer_notes->getTransferFormId($f->getElement('tn_code')->get());
        $m_item_list->addCondition('transfer_notes_id', $tn_code_id);
        $m_i = $m_item_list->join('items');
        $m_i->addField('serial');
        $item_list->setModel($m_item_list);
        $pc_info = $f->add('View_PartsCatalogueInfo');

        $f_status_dd = $f->addField('DropDown', 'Destination Status');
        $m_status = $f_status_dd->setModel('PartStatus', array('id', 'status'));
        $f->add('Order')->move($pc_info, 'after', 'parts_catalogue')->now();

        $item_list->addClass("zebra bordered");
        $item_list->addPaginator(5);


        $this->add('Button')->set('Save&Close Transfer Form')->js('click', $this->js()->trigger('reloadpage'))->univ()->closeDialog();
        $dd->js('change', $f->js()->reload(array($this->api->url(), 'Destination_Store' => $dd->js()->val(), 'dd_change' => true)));

        $icon = $f->getElement('qty')
                ->add('Icon', null, 'after_field')
                ->set('basic-group')
                ->setStyle('margin-left', '10px')
                ->setColor('red');


        $icon->js(true)->hide();
        $icon->js('click')->univ()->alert('test');
        ///Validation
        $locator = $f->getElement('locators');
        $locator->validateNotNull();
//       

        $locator_id = $f->getElement('locators_id');
        $locator_id->set($m_locaters->tryLoadAny()->get('id'));

        $part_catalogue_id = $f->getElement('parts_catalogue_id');
        $serial = $f->getElement('serial');
        $serial->addHook('validate', function() use ($f, $serial, $part_catalogue_id) {
            $pc = $f->add('Model_PartsCatalogue');

            $pc->load($part_catalogue_id->get());
            if ($pc->get('serialized')) {
                if ($serial->get() == null) {
                    $serial->displayFieldError('Serial Required for Serialized Part Numbers');
                } else {
                    $item = $f->add('Model_Items')->addCondition('serial', $serial->get())->addCondition('parts_catalogue_id', $part_catalogue_id->get())->addCondition('stores_id', $f->get('sel_store'))->addCondition('part_status_id', $f->get('part_status_id'));
                    $item->tryLoadAny();
                    if (!$item->loaded()) {
                        $serial->displayFieldError('Item with this serial or status does not exist in your store');
                    }
                }
            } else {
                if ($serial->get() != null)
                    $serial->displayFieldError('Serial not required for Non Serialized Items');
                else {
                    $item = $f->add('Model_Items')->addCondition('parts_catalogue_id', $part_catalogue_id->get())->addCondition('stores_id', $f->get('sel_store'))->addCondition('part_status_id', $f->get('part_status_id'));
                    $item->tryLoadAny();
                    if ($item->loaded()) {
                        if ($item->get('qty') < $f->getElement('qty')->get()) {
                            $f->getElement('qty')->displayFieldError('Not enough of this item in Stock');
                        }
                    } else {
                        $f->getElement('parts_catalogue')->displayFieldError('This item does not exist in your store');
                    }
                }
            }
        });

        ///End of Validation
//        if(!$_GET['Destination_Store']){
//            $_GET['Destination_Store']=$dd->get();
//            $this->js()->reload();
//        }
        $f->addSubmit('AddItem');
        if ($f->isSubmitted()) {
            //->js('click', $dd->js(true)->attr('disabled', false));
            $js = array();
            $orig_qty = $f->getElement('qty')->get();
            $item_id = $f->model->setStore($f->getElement('sel_store')->get(), $f->getElement('parts_catalogue_id')->get(), $f->getElement('serial')->get(), $f->getElement('Destination_Store')->get(), $f->getElement('qty')->get(), $f->getElement('tn_code')->get(), $f->getElement('locators_id')->get(), $f->getElement('locators')->get(), $f->getElement('part_status_id')->get(), $f_status_dd->get());
            //$f->update();
//            $m_transfer_notes->debug();
            $m_transfer_notes->tryLoadBy('tn_code', $f->getElement('tn_code')->get());
            $id = null;
            if ($m_transfer_notes->loaded()) {
                $id = $m_transfer_notes->id;
//                $m_transfer_notes->set('purchase_order',$f->getElement('purchase_order')->get());
//                $m_transfer_notes->set('delivery_note',$f->getElement('delivery_note')->get());
                $m_transfer_notes->set('date_changed', date("Y-m-d H:i:s"));
                $m_transfer_notes->set('date_changed', date("Y-m-d H:i:s"));
                $m_transfer_notes->set('notes', $f->getElement('notes')->get());
                $m_transfer_notes->save();
            } else {
                $m_transfer_notes->set('tn_code', $f->getElement('tn_code')->get());
//                $m_transfer_notes->set('purchase_order', $f->getElement('purchase_order')->get());
//                $m_transfer_notes->set('delivery_note', $f->getElement('delivery_note')->get());
                $m_transfer_notes->set('to_stores_id', $f->getElement('Destination_Store')->get());
                $m_transfer_notes->set('from_stores_id', $f->getElement('sel_store')->get());
                $m_transfer_notes->set('date_created', date("Y-m-d H:i:s"));
                $m_transfer_notes->set('date_changed', date("Y-m-d H:i:s"));
                $m_transfer_notes->set('notes', $f->getElement('notes')->get());
                $m_transfer_notes->save();
                $id = $m_transfer_notes->id;
            }

            $m_item_list->set('transfer_notes_id', $id);
            $m_item_list->set('items_id', $item_id);
            $m_item_list->set('comment', $f->getElement('Comment')->get());
            $m_item_list->set('qty', $orig_qty);
            $m_item_list->set('from_part_status_id', $f->getElement('part_status_id')->get());
            $m_item_list->set('to_part_status_id', $f_status_dd->get());
            $m_item_list->saveAndUnload();

//            $m_transfer_log = $this->add('Model_TransferLog');
//            $m_transfer_log->set('user_id', $this->api->auth->model->id);
//            $m_transfer_log->set('from_stores_id', $f->getElement('sel_store')->get());
//            $m_transfer_log->set('to_stores_id', $f->getElement('Destination_Store')->get());
//            $m_transfer_log->set('time', date("Y-m-d H:i:s"));
//            $m_transfer_log->set('system_comment', $orig_qty . ' Item(s) transfered from Store ' . $f->getElement('sel_store')->get() . ' to Transit Store ' . $f->getElement('Destination_Store')->get() . ' - TN :' . $f->getElement('tn_code')->get());
//            $m_transfer_log->set('items_id', $item_id);
//            $m_transfer_log->saveAndUnload();
            $js[] = $f->js()->reload(array('Destination_Store' => $f->getElement('Destination_Store')->get(), 'tn_code' => $f->getElement('tn_code')->get(), 'sel_store' => $f->getElement('sel_store')->get(), 'notes' => $f->getElement('notes')->get()));
            $js[] = $item_list->js()->reload(array('tn_code' => $f->getElement('tn_code')->get(), 'sel_store' => $f->getElement('sel_store')->get()));
            $f->js(true, $js)->univ()->successMessage('Item Added')->execute();
        }
        $js = array();
        $js[] = $pc_info->js()->reload(array('Destination_Store' => $f->getElement('Destination_Store')->get(), 'part_num' => $f->getElement('parts_catalogue_id')->js()->val(), 'serial_fld' => $f->getElement('serial'), 'qty_fld' => $f->getElement('qty'), 'store_type' => $_GET['store_type'], 'home_store' => $_GET['home_store'], 'home_store_type' => $_GET['home_store_type'], 'icn_fld' => $icon));
        $this->js('myFunc', $js);
    }

}
