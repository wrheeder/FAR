<?php
class Page_BrowseStore extends Page{
    function init() {
        parent::init();
        $items = $this->add('Grid');
        $m_items = $items->setModel('Items')->addCondition('stores_id', $_GET['sel_store']);
        if($_GET['store_type']=='Regional Transit Store' || $_GET['store_type']=='Site' || $_GET['store_type']=='Regional Stock Van'){
            $m_items->addCondition('qty','>',0);
        }
        $items->removeColumn('stores');
        $this->api->stickyGet('sel_store');
        $items->addColumn('expander', 'TransferLog');
        $items->addQuickSearch(array('parts_catalogue','serial'),'QuickSearch',array('sel_store'=>$_GET['sel_store']));

    }
}