<?php
class Page_browseStore_TransferLog extends Page{
    
    function init() {
        parent::init();
        $g = $this->add('grid');
        $g->setModel('TransferLog')->addCondition('items_id',$_GET['items_id']);
    }
}