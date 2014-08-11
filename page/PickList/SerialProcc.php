<?php

class Page_PickList_SerialProcc extends Page_ApplicationPage {

    function init() {
        parent::init();
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('booking_nr');
        $this->api->stickyGet('part_num');
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('qty');
        $this->api->stickyGet('notes');
        $this->api->stickyGet('purchase_order');
        $this->api->stickyGet('delivery_note');

        $first_load = $_GET['first_load'] ? false : true;

        $f = $this->add('Form');
        $pn = $f->addField('Hidden', 'part_num');




        $m_pc = $this->add('Model_PartsCatalogue');

        if ($first_load) {
            $pc = str_replace('.val()', '', $_GET['part_num']);
            $pc = str_replace("$('", '', $pc);
            $pc = str_replace("')", '', $pc);
            $js = array();
            $js[] = $this->js()->reload(array('first_load' => false, 'part_num' => $this->js(true)->_selector($pc)->val(), 'qty' => $_GET['qty']));
            $pn->js(true, $js)->val($this->js(true)->_selector($pc)->val());
        } else {
            $pn->set($_GET['part_num']);
            $m_pc->load($_GET['part_num']);
            $pn_desc = $f->addField('ReadOnly', 'part_num_desc')->set($m_pc->get('part_number'));
            $pc_info = $f->add('View_PartsCatalogueInfo');
        }
//        echo $this->js(true)->_selector($pc)->val();

        $qty = $f->addField('line', 'qty')->set($_GET['qty'] ? $_GET['qty'] : 1);
        $qty->js('change', $this->js()->reload(array('first_load' => false, 'part_num' => $pn->get(), 'qty' => $qty->js()->val())));

        $col_view = $f->add('View_Columns');
        $col1 = $col_view->addColumn(6);
        $col2 = $col_view->addColumn(6);
        $v = $qty->get();
        for ($i = 1; $i <= $v; $i++) {

            ${"ser" . $i} = $f->addField('line', 'serial' . $i);
            
            $col1->add(${"ser" . $i});
            ${"loc" . $i} = $f->addField('dropdown', 'locator' . $i);
            $col2->add(${"loc" . $i});
            $loc_m = ${"loc" . $i}->setModel('locators');
            $loc_m->addCondition('stores_id', $_GET['sel_store']);
//            $f->add('View_serial');
//            $sep1 = $f->addSeparator('span6');
//            $col1->add($sep1);
//            $sep2 = $f->addSeparator('span6');
//            $col2->add($sep2);
        }
        $trf = $f->addSubmit('Transfer');

        if ($f->isSubmitted()) {
//            die(var_dump($ser2->get()));
            //die(var_dump());
            $v = $_GET['qty'];
            $serials = array();
            $locators = array();
            for ($i = 1; $i <= $v; $i++) {
                $serials[$i] = ${"ser" . $i}->get();
                $locators[$i] = ${"loc" . $i}->get();
                if (${"ser" . $i}->get() == "") {
                    ${"ser" . $i}->displayFieldError('Serial' . $i . ' cant be empty');
                }
                $item = $this->add('Model_Items')->addCondition('serial', ${"ser" . $i}->get())->addCondition('parts_catalogue_id', $_GET['part_num']);
                    $item->tryLoadAny();
                    if ($item->loaded()) {
                        ${"ser" . $i}->displayFieldError('Item with this serial already exists');
                    }
//                
            }
            if (count($serials) != count(array_unique($serials))) {
                $arr = array_count_values($serials);
//                var_dump($serials);
                for ($i = 1; $i <= $v; $i++) {
                    if ($arr[$serials[$i]] > 1) {
                        ${"ser" . $i}->displayFieldError('You have duplicate serial numbers in list');
//                        break;
                    }
                }
            }

            $m_booking_form = $this->add('Model_ItemBookingForm');
            $m_booking_form->tryLoadBy('booking_nr', $_GET['booking_nr']);

            $id = null;
            if ($m_booking_form->loaded()) {
                $id = $m_booking_form->id;
                $m_booking_form->set('purchase_order', $_GET['purchase_order']);
                $m_booking_form->set('delivery_note', $_GET['delivery_note']);
                $m_booking_form->set('date_changed', date("Y-m-d H:i:s"));
                $m_booking_form->set('notes', $_GET['notes']);
                $m_booking_form->save();
            } else {
                $m_booking_form->set('booking_nr', $_GET['booking_nr']);
                $m_booking_form->set('purchase_order', $_GET['purchase_order']);
                $m_booking_form->set('delivery_note', $_GET['delivery_note']);
                $m_booking_form->set('stores_id', $_GET['sel_store']);
                $m_booking_form->set('date_created', date("Y-m-d H:i:s"));
                $m_booking_form->set('date_changed', date("Y-m-d H:i:s"));
                $m_booking_form->set('notes', $_GET['notes']);
                $m_booking_form->save();
                $id = $m_booking_form->id;
            }
            
            $m_items = $this->add('Model_Items');
            $m_item_list = $this->add('Model_ItemBookingList');
            for ($i = 1; $i <= $v; $i++) {
                $m_items->set('parts_catalogue_id',$_GET['part_num']);
                $m_items->set('serial',$serials[$i]);
                $m_items->set('qty',1);
                $m_items->set('stores',$_GET['sel_store']);
                $m_items->set('partstatus',1); //new
                $m_items->set('locators',$locators[$i]); //new
                
                $item_id=$m_items->save()->id;
                $m_items->unload();
                
                
                
                $m_item_list->set('item_booking_form_id', $id);
                $m_item_list->set('items_id', $item_id);
                $m_item_list->set('comment', $_GET['comments']);
                $m_item_list->set('qty', 1);
                $m_item_list->set('part_status_id',1); //new
                
                $m_item_list->saveAndUnload();
                
                $m_transfer_log = $this->add('Model_TransferLog');
                $m_transfer_log->set('user_id', $this->api->auth->model->id);
                $m_transfer_log->set('from_stores_id', $_GET['sel_store']);
                $m_transfer_log->set('to_stores_id', $_GET['sel_store']);
                $m_transfer_log->set('time', date("Y-m-d H:i:s"));
                $m_transfer_log->set('system_comment', 1 . ' Item(s) added to [' . $_GET['sel_store'] . '] - BN :' . $_GET['booking_nr'] . ' Locator : [' . $locators[$i] . '] - Status -> ' . 'NEW');
                $m_transfer_log->set('items_id', $item_id);
                $m_transfer_log->saveAndUnload();
            }
            $js = array();
            $js[] = $this->js()->_selector('#FAR_AddItem_grid_buttonset_gbtn3')->click();
            $this->js(true,$js)->univ()->closeDialog()->execute();
        }
//        $pc_info = $f->add('View_PartsCatalogueInfo');
    }

}
