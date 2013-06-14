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
        $_GET['booking_nr']=$m_itembooking_form->get('booking_nr');
        $this->api->stickyGet('booking_nr');
        $pg = $this->api->getDestinationURL('print_PrintBookingForm', array('cut_page'=>1));
        $grid->addColumn('template','Print')->setTemplate('<a href="'.$pg.'&booking_nr=<?$id?>" target="_blank">Print Booking Form</a>');
        $this->js("reloadpage", $this->js()->reload())->_selector("body");
        $grid->addQuickSearch(array('Booking_Nr'));
//        $grid->getColumn('booking')->makeSortable();
//        //$grid->dq->order('booking_nr asc');
        $grid->addClass("zebra bordered");
        $grid->addPaginator(10);
        if ($_GET['edit']) {
            $m_itembooking_form->tryLoad($_GET['edit']);
            $f->js()->univ()->frameURL('Book In Item', $this->api->getDestinationURL('AddItem', array('booking_nr' => $m_itembooking_form->get('booking_nr'),'purchase_order' => $m_itembooking_form->get('purchase_order'),'delivery_note' => $m_itembooking_form->get('delivery_note'),'notes' => $m_itembooking_form->get('notes'))))->execute();
        }
        if ($_GET['print']) {
            $m_itembooking_form->tryLoad($_GET['print']);
            $f->js()->univ()->frameURL('Book In Item', $this->api->getDestinationURL('print_PrintBookingForm', array('cut_page'=>1,'booking_nr' => $m_itembooking_form->get('booking_nr'),'purchase_order' => $m_itembooking_form->get('purchase_order'),'delivery_note' => $m_itembooking_form->get('delivery_note'))))->execute();
        }
    }
}
