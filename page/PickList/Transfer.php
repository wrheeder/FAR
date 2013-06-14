<?php

class Page_PickList_Transfer extends Page {

    function init() {
        parent::init();
        $f = $this->add('Form');
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('store_type');
        $this->api->stickyGet('home_store');
        $this->api->stickyGet('home_type');
        $f->add('Button')->set('Add Transfer Form')->js('click')->univ()->frameURL('Transfer Form', $this->api->getDestinationURL('PickList_TransferItem',array('store_type'=>$_GET['store_type'],'home_store'=>$_GET['home_store'])));
        $grid = $this->add('Grid');

        $m_itembooking_form = $grid->setModel('ItemTransferForm')->addCondition('from_stores_id', $_GET['sel_store']);
        $grid->addColumn('button', 'edit', 'Edit Transfer');
        $pg = $this->api->getDestinationURL('print_PrintTransferForm', array('cut_page'=>1));
        $grid->addColumn('template','print')->setTemplate('<a href="'.$pg.'&tn_code=<?$id?>" target="_blank">Print Booking Form</a>');
        $this->js("reloadpage", $this->js()->reload())->_selector("body");
        $grid->addQuickSearch(array('tn_code'));
//        $grid->getColumn('booking')->makeSortable();
//        //$grid->dq->order('booking_nr asc');
        $grid->addClass("zebra bordered");
        $grid->addPaginator(10);
        if ($_GET['edit']) {
            $m_itembooking_form->tryLoad($_GET['edit']);
            $f->js()->univ()->frameURL('Transfer Form', $this->api->getDestinationURL('PickList_TransferItem', array('tn_code' => $m_itembooking_form->get('tn_code'),'Destination_Store' => $m_itembooking_form->get('to_stores_id'),'store_type'=>$_GET['store_type'],'home_store'=>$_GET['home_store'],'home_store_type'=>$_GET['home_store_type'],'notes'=>$m_itembooking_form->get('notes'))))->execute();
        }
        if ($_GET['print']) {
            die(var_dump($_GET));
            $m_itembooking_form->tryLoad($_GET['Print']);
            
            $f->js()->univ()->frameURL('Transfer Form', $this->api->getDestinationURL('print_PrintTransgerForm', array('cut_page'=>1,'tn_code' => $m_itembooking_form->id,'Destination_Store' => $m_itembooking_form->get('to_stores_id'),'store_type'=>$_GET['store_type'],'home_store'=>$_GET['home_store'],'home_store_type'=>$_GET['home_store_type'],'notes'=>$m_itembooking_form->get('notes'))))->execute();
        }
    }

}
