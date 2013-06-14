<?php

class Page_PickList_Collect extends Page {

    function init() {
        parent::init();
        $f = $this->add('Form');
        $this->api->stickyGet('sel_store');
        $f->add('Button')->set('Add Collection Form')->js('click')->univ()->frameURL('Collection Form', $this->api->getDestinationURL('PickList_CollectItem'));
        $grid = $this->add('Grid');

        $m_itembooking_form = $grid->setModel('ItemCollectionForm')->addCondition('from_stores_id', $_GET['sel_store']);
        $grid->addColumn('button', 'edit', 'Edit Collection');

        $this->js("reloadpage", $this->js()->reload())->_selector("body");
        $grid->addQuickSearch(array('tn_code'));
//        $grid->getColumn('booking')->makeSortable();
//        //$grid->dq->order('booking_nr asc');
        $grid->addClass("zebra bordered");
        $grid->addPaginator(10);
        if ($_GET['edit']) {
            $m_itembooking_form->tryLoad($_GET['edit']);
            $f->js()->univ()->frameURL('Collection Form', $this->api->getDestinationURL('PickList_CollectItem', array('sel_store'=>$_GET['sel_store'],'tn_code' => $m_itembooking_form->get('tn_code'), 'Destination_Store' => $m_itembooking_form->get('to_stores_id'))))->execute();
        }
    }

}