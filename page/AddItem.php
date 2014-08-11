<?php

class Page_addItem extends Page {

    function init() {
        parent::init();
        $this->api->stickyGet('notes');
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('booking_nr');
        $this->api->stickyGet('purchase_order');
        $this->api->stickyGet('delivery_note');

        $f = $this->add('Form');

        $f->addSubmit('AddItem');

        $f->addField('Hidden', 'sel_store')->set($_GET['sel_store']);
        $f->addField('ReadOnlySave', 'booking_nr')->set($_GET['booking_nr']);
        $f->addField('Line', 'purchase_order')->set($_GET['purchase_order']);
        $f->addField('Line', 'delivery_note')->set($_GET['delivery_note']);
        $f->addField('Text', 'notes')->set($_GET['notes']);
        $f->addSeparator();
        if ($f->getElement('booking_nr')->get() == null) {
            $f->getElement('booking_nr')->set(strtoupper(uniqid("BN" . date("ymd") . '_')));
        }

        $m_booking_form = $this->add('Model_ItemBookingForm');

        $m_items = $f->setModel('Items');
        
        $this->api->stickyGet('parts_catalogue');
        
        $f->addField('Text', 'Comments');
        $f->getElement('stores_id')->js(true)->closest('.atk-form-row-dropdown')->hide();
        $item_list = $this->add('Grid');
        
        //$this->js('reload_il',$item_list->js()->reload())->_selector('body');
//        $filter = $this->add('Filter');
        $m_item_list = $this->add('Model_ItemBookingList');
        $m_i = $m_item_list->join('items');
        $m_i->addField('serial');
        $m_item_list->addCondition('item_booking_form_id', $m_booking_form->getBookingFormId($f->getElement('booking_nr')->get()));
//        $filter->useWith($m_item_list);
        
        
        $icon = $f->getElement('qty')
                ->add('Icon', null, 'after_field')
                ->set('basic-group')
                ->setStyle('margin-left', '10px')
                ->setColor('red');
        
        
        $icon->js(true)->hide();
        $icon->js('click',$this->js()->univ()->frameURL('Multiple Serial Scanner', $this->api->url('PickList/SerialProcc',array('purchase_order'=>$f->getElement('purchase_order')->get(),'delivery_note'=>$f->getElement('delivery_note')->get(),'notes'=>$f->getElement('notes')->get(),'qty'=>1,'booking_nr'=>$f->getElement('booking_nr')->get(),'sel_store'=>$_GET['sel_store'],'part_num'=>$f->getElement('parts_catalogue_id')->js()->val()))));
       
        $item_list->setModel($m_item_list);
        $pc_info = $f->add('View_PartsCatalogueInfo');
 
        $item_list->addButton("Refresh")->js('click',$item_list->js()->reload(array('booking_nr'=>$f->getElement('booking_nr')->get())));
        $f->add('Order')->move($pc_info, 'after', 'parts_catalogue')->now();

        $item_list->addClass("zebra bordered");
        $item_list->addPaginator(5);

        $this->add('Button')->set('Save&Close Booking Form')->js('click', $this->js()->trigger('reloadpage'))->univ()->closeDialog();

//        if($_GET['cut_object']=='FAR_AddItem_form_view_partscatalogueinfo')
//        {
//           $this->js()->trigger('myfunc');
//        }
        ///Validation
        $locator = $f->getElement('locators');
        $locator->validateNotNull();
        $part_catalogue_id = $f->getElement('parts_catalogue_id');
        $serial = $f->getElement('serial');
        $serial->addHook('validate', function() use ($f, $serial, $part_catalogue_id) {
            $pc = $f->add('Model_PartsCatalogue');

            $pc->load($part_catalogue_id->get());
            if ($pc->get('serialized')) {
                if ($serial->get() == null) {
                    $serial->displayFieldError('Serial Required for Serialized Part Numbers');
                } else {
                    $item = $f->add('Model_Items')->addCondition('serial', $serial->get())->addCondition('parts_catalogue_id', $part_catalogue_id->get());
                    $item->tryLoadAny();
                    if ($item->loaded()) {
                        $serial->displayFieldError('Item with this serial already exists');
                    }
                }
            } else {
                if ($serial->get() != null)
                    $serial->displayFieldError('Serial not required for Non Serialized Items');
            }
        });
        ///End of Validation
        if ($f->isSubmitted()) {
            $js = array();
            $orig_qty = $f->getElement('qty')->get();
            $f->update();
            $m_booking_form->tryLoadBy('booking_nr', $f->getElement('booking_nr')->get());

            $id = null;
            if ($m_booking_form->loaded()) {
                $id = $m_booking_form->id;
                $m_booking_form->set('purchase_order', $f->getElement('purchase_order')->get());
                $m_booking_form->set('delivery_note', $f->getElement('delivery_note')->get());
                $m_booking_form->set('date_changed', date("Y-m-d H:i:s"));
                $m_booking_form->set('notes', $f->getElement('notes')->get());
                $m_booking_form->save();
            } else {
                $m_booking_form->set('booking_nr', $f->getElement('booking_nr')->get());
                $m_booking_form->set('purchase_order', $f->getElement('purchase_order')->get());
                $m_booking_form->set('delivery_note', $f->getElement('delivery_note')->get());
                $m_booking_form->set('stores_id', $f->getElement('sel_store')->get());
                $m_booking_form->set('date_created', date("Y-m-d H:i:s"));
                $m_booking_form->set('date_changed', date("Y-m-d H:i:s"));
                $m_booking_form->set('notes', $f->getElement('notes')->get());
                $m_booking_form->save();
                $id = $m_booking_form->id;
            }
            $m_item_list->set('item_booking_form_id', $id);
            $m_item_list->set('items_id', $f->model->id);
            $m_item_list->set('comment', $f->getElement('Comments')->get());
            $m_item_list->set('qty', $orig_qty);
            $m_item_list->set('part_status_id', $f->getElement('part_status_id')->get());

            $m_item_list->saveAndUnload();
            $m_stores = $this->add('Model_Stores')->load($f->getElement('sel_store')->get());
            $m_transfer_log = $this->add('Model_TransferLog');
            $m_transfer_log->set('user_id', $this->api->auth->model->id);
            $m_transfer_log->set('from_stores_id', $f->getElement('sel_store')->get());
            $m_transfer_log->set('to_stores_id', $f->getElement('sel_store')->get());
            $m_transfer_log->set('time', date("Y-m-d H:i:s"));
            $m_transfer_log->set('system_comment', $orig_qty . ' Item(s) added to [' . $m_stores->get('store_name') . '] - BN :' . $f->getElement('booking_nr')->get() . ' Locator : [' . $f->getElement('locators')->get() . '] - Status -> ' . $f->model->get('part_status'));
            $m_transfer_log->set('items_id', $f->model->id);
            $m_transfer_log->saveAndUnload();
            $js[] = $f->js()->reload(array('purchase_order' => $f->getElement('purchase_order')->get(), 'delivery_note' => $f->getElement('delivery_note')->get(), 'booking_nr' => $f->getElement('booking_nr')->get(), 'sel_store' => $f->getElement('sel_store')->get(), 'notes' => $f->getElement('notes')->get()));
            $js[] = $item_list->js()->reload(array('booking_nr' => $f->getElement('booking_nr')->get(), 'sel_store' => $f->getElement('sel_store')->get()));
            $f->js(true, $js)->univ()->successMessage('Item Added')->execute();
        }
        $js = array();
        $js[] = $pc_info->js()->reload(array('part_num' => $f->getElement('parts_catalogue_id')->js()->val(), 'serial_fld' => $f->getElement('serial'), 'qty_fld' => $f->getElement('qty'), 'purchase_order' => $f->getElement('purchase_order')->get(), 'delivery_note' => $f->getElement('delivery_note')->get(),'icn_fld' => $icon));
                  
        $this->js('myFunc', $js);
    }

}
