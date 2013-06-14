<?php

class Page_BookInEquipmentItems extends Page {

    function init() {
        parent::init();
        $f = $this->add('Form');
        $this->api->stickyGet('sel_store');
        $f->add('Button')->set('Add Booking Form')->js('click')->univ()->frameURL('Booking Form', $this->api->getDestinationURL('AddItem'));
        $grid = $this->add('Grid');

        $m_itembooking_form = $grid->setModel('ItemBookingForm')->addCondition('stores_id', $_GET['sel_store']);
        $grid->addColumn('button', 'edit', 'Edit Booking');
        $grid->addColumn('button', 'print','Print Booking');
        $this->js("reloadpage", $this->js()->reload())->_selector("body");
        $grid->addQuickSearch(array('Booking_Nr'));
//        $grid->getColumn('booking')->makeSortable();
//        //$grid->dq->order('booking_nr asc');
        $grid->addClass("zebra bordered");
        $grid->addPaginator(10);
        if ($_GET['edit']) {
            $m_itembooking_form->tryLoad($_GET['edit']);
            $f->js()->univ()->frameURL('Book In Item', $this->api->getDestinationURL('AddItem', array('booking_nr' => $m_itembooking_form->get('booking_nr'),'purchase_order' => $m_itembooking_form->get('purchase_order'),'delivery_note' => $m_itembooking_form->get('delivery_note'))))->execute();
        }
        if ($_GET['print']) {
            $m_itembooking_form->tryLoad($_GET['print']);
            $f->js()->univ()->frameURL('Book In Item', $this->api->getDestinationURL('printBookingForm', array('cut_page'=>1,'booking_nr' => $m_itembooking_form->get('booking_nr'),'purchase_order' => $m_itembooking_form->get('purchase_order'),'delivery_note' => $m_itembooking_form->get('delivery_note'))))->execute();
        }
        //     $f->add('Button')->set('Add Booking Form')->js('click')->univ()->frameURL('Title',$this->api->getDestinationURL('AddItem',array('test'=>'case')));
//        $this->js(true)->univ()->setInterval(
//                    $g->js(null, array(
//                        $g->js()->reload(),
//                        $g->js()->univ()->successMessage('Reloaded...')
//                    ))->_enclose()
//                    , 3000);
    }

}
