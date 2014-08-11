<?php

class Page_PickList_BulkCollect extends Page_ApplicationPage {

    function init() {
        parent::init();

        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('tn_code');
        $this->api->stickyGet('notes');
        $this->api->stickyGet('Destination_Store');
        $this->api->stickyGet('home_store');
        $this->api->stickyGet('col_sel');
        $this->api->stickyGet('col_sel_prep');

        $this->api->stickyGet('sel');
        $this->api->stickyGet('id_dropdown');

        $f = $this->add('Form');
        $f->addField('Hidden', 'sel_store')->set($_GET['sel_store']);
        $f->addField('ReadOnlySave', 'tn_code')->set($_GET['tn_code']);

        $f->addField('Text', 'notes')->set($_GET['notes']);
        //die(var_dump($f->getElement('tn_code')->get()));
        if (!$_GET['tn_code']) {
            $f->getElement('tn_code')->set(strtoupper(uniqid("CN" . date("ymd") . '_')));
        }
        $m_items = $this->add('Model_Items');
        $m_item_list = $this->add('Model_ItemTrfList');


        $m_items->addCondition('id', 'in', json_decode($_GET['col_sel'], true));

        $g = $f->add('Grid');

        $g->setModel($m_items);
        $loc = $g->addColumn('grid/dropdown', 'locator');

        $loc_m = $loc->setModel('locators');
        $loc_m->addCondition('stores_id', $_GET['home_store']);
        $loc->editFields(array('locator'));

        $subm = $f->addSubmit('Submit');

        $col_sel = array();
        $col_sel = json_decode($_GET['col_sel'], true);

        $col_sel_prep = $_GET['col_sel_prep'] ? json_decode($_GET['col_sel_prep'], true) : array();

        if ($f->isSubmitted()) {

            $loc = $g->getColumn('locator');
            foreach ($_POST as $key => $value) {
                foreach ($col_sel as $id) {
                    if (strpos($key, $id . '_locator') > 0) {
                        $col_sel_prep[$id] = $value;
                    } else {
                        $col_sel_prep[$id] = $col_sel_prep[$id] != false ? $col_sel_prep[$id] : false;
                    }
                }
            }
            $_GET['col_sel_prep'] = json_encode($col_sel_prep);

            if (in_array(false, $col_sel_prep, true)) {
                return $f->getElement('tn_code')->displayFieldError('Go Back and select all locators!');
            }
            
            $m_transfer_notes = $this->add('Model_ItemTransferForm');
            $m_transfer_notes->tryLoadBy('tn_code', $f->getElement('tn_code')->get());
            $id = null;
            $m_transfer_notes->set('tn_code', $f->getElement('tn_code')->get());
//                $m_transfer_notes->set('purchase_order', $f->getElement('purchase_order')->get());
//                $m_transfer_notes->set('delivery_note', $f->getElement('delivery_note')->get());
            $m_transfer_notes->set('to_stores_id', $_GET['home_store']);
            $m_transfer_notes->set('from_stores_id', $_GET['sel_store']);
            $m_transfer_notes->set('date_created', date("Y-m-d H:i:s"));
            $m_transfer_notes->set('date_changed', date("Y-m-d H:i:s"));
            $m_transfer_notes->set('notes', $f->getElement('notes')->get());
            $m_transfer_notes->save();
            $id = $m_transfer_notes->id;
            
            foreach ($m_items->getRows() as $item) {
                $m_item_list->set('items_id', $item['id']);
                $m_item_list->set('from_part_status_id', $item['part_status_id']);
                $m_item_list->set('to_part_status_id', $item['part_status_id']);
                $m_item_list->set('qty', $item['qty']);
                $m_item_list->set('transfer_notes_id', $id);
                $m_item_list->saveAndUnload();
            }
            
            $m_items_trf = $this->add('Model_ItemsTrf');
            $m_items_trf->addCondition('id', 'in', json_decode($_GET['col_sel'], true));
             foreach ($m_items_trf as $item) {
                $loc_name = $this->api->db->dsql()->table('locators')->field('locator')->where('id', $col_sel_prep[$item['id']])->do_getOne();
                $m_items_trf->setStore($_GET['sel_store'], $item['parts_catalogue_id'], $item['serial'], $_GET['home_store'], $item['qty'], $f->getElement('tn_code')->get(), $col_sel_prep[$item['id']], $loc_name, $item['part_status_id'], $item['part_status_id']);
            }
            $js = array();
            $js[] = $this->js()->_selector('#FAR_browseStore_grid_buttonset_gbtn6_2')->click();
            $this->js(true, $js)->univ()->closeDialog()->execute();
        }
        $_GET['col_sel_prep'] = json_encode($col_sel_prep, true);
        $this->api->stickyGet('col_sel_prep');
    }

}
