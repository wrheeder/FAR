<?php

class Page_AddItem extends Page {

    public $booking_nr = null;

    function init() {

        parent::init();
        $f = $this->add('Form');
        
        $f->addSubmit('AddItem');
        
        $f->addField('Hidden', 'sel_store')->set($_GET['sel_store']);
        $f->addField('ReadOnlySave', 'booking_nr')->set($_GET['booking_nr']);
        $f->addField('Line', 'purchase_order')->set($_GET['purchase_order']);
        $f->addField('Line', 'delivery_note')->set($_GET['delivery_note']);
        $f->addSeparator();
        
        $m_booking_form = $this->add('Model_ItemBookingForm');
        
        $m_items = $f->setModel('Items');
        $f->getElement('parts_catalogue')->js(true)->focus();
        $f->getElement('stores_id')->js(true)->closest('.atk-form-row-dropdown')->hide();
        $item_list = $this->add('Grid');
        $filter = $this->add('Filter');
        $m_item_list = $this->add('Model_ItemBookingList');
        $m_item_list->addCondition('item_booking_form_id', $m_booking_form->getBookingFormId($f->getElement('booking_nr')->get()));
        $filter->useWith($m_item_list);
        $item_list->setModel($m_item_list);
        $pc_info = $f->add('View_PartsCatalogueInfo');
        $f->add('Order')->move($pc_info, 'after', 'parts_catalogue')->now();

        $item_list->addClass("zebra bordered");
        $item_list->addPaginator(5);


        if ($f->getElement('booking_nr')->get() == null) {
            $f->getElement('booking_nr')->set(strtoupper(uniqid("BN" . date("Ymd") . '_')));
        }
        $this->add('Button')->set('Save&Close Booking Form')->js('click', $this->js()->trigger('reloadpage'))->univ()->closeDialog();
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('booking_nr');
        $this->api->stickyGet('purchase_order');
        $this->api->stickyGet('delivery_note');
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
                $m_booking_form->set('purchase_order',$f->getElement('purchase_order')->get());
                $m_booking_form->set('delivery_note',$f->getElement('delivery_note')->get());
                $m_booking_form->set('date_changed', date("Y-m-d H:i:s"));
                $m_booking_form->save();
            } else {
                $m_booking_form->set('booking_nr', $f->getElement('booking_nr')->get());
                $m_booking_form->set('purchase_order', $f->getElement('purchase_order')->get());
                $m_booking_form->set('delivery_note', $f->getElement('delivery_note')->get());
                $m_booking_form->set('stores_id', $f->getElement('sel_store')->get());
                $m_booking_form->set('date_created', date("Y-m-d H:i:s"));
                $m_booking_form->set('date_changed', date("Y-m-d H:i:s"));
                $m_booking_form->save();
                $id = $m_booking_form->id;
            }
            $m_item_list->set('item_booking_form_id', $id);
            $m_item_list->set('items_id', $f->model->id);

            $m_item_list->saveAndUnload();

            $m_transfer_log = $this->add('Model_TransferLog');
            $m_transfer_log->set('user_id', $this->api->auth->model->id);
            $m_transfer_log->set('from_stores_id', $f->getElement('sel_store')->get());
            $m_transfer_log->set('to_stores_id', $f->getElement('sel_store')->get());
            $m_transfer_log->set('time', date("Y-m-d H:i:s"));
            $m_transfer_log->set('system_comment', $orig_qty . ' Item(s) added to Store '.$f->getElement('sel_store')->get().' - BN :' . $f->getElement('booking_nr')->get().' Locator :'.$f->getElement('Locator')->get().' - Status :'.$f->getElement('part_status')->get());
            $m_transfer_log->set('items_id', $f->model->id);
            $m_transfer_log->saveAndUnload();
            $js[] = $f->js()->reload(array('purchase_order'=>$f->getElement('purchase_order')->get(),'delivery_note'=>$f->getElement('delivery_note')->get(),'booking_nr' => $f->getElement('booking_nr')->get(), 'sel_store' => $f->getElement('sel_store')->get()));
            $js[] = $item_list->js()->reload(array('booking_nr' => $f->getElement('booking_nr')->get(), 'sel_store' => $f->getElement('sel_store')->get()));
            $f->js(true, $js)->univ()->successMessage('Item Added')->execute();
        }
        $js = array();
        $js[] = $pc_info->js()->reload(array('part_num' => $f->getElement('parts_catalogue_id')->js()->val(), 'serial_fld' => $f->getElement('serial'), 'qty_fld' => $f->getElement('qty'),'purchase_order'=>$f->getElement('purchase_order')->get(),'delivery_note'=>$f->getElement('delivery_note')->get()));
        $this->js('myFunc', $js);
    }

}