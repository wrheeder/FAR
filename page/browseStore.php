<?php
class Page_BrowseStore extends Page{
    function init() {
        parent::init();
        $items = $this->add('Grid');
        $m_items = $this->add('Model_Items');
        $m_items->addCondition('stores_id', $_GET['sel_store']);
        
        if($_GET['store_type']=='Regional Transit Store' || $_GET['store_type']=='Site' || $_GET['store_type']=='Regional Stock Van'){
            $m_items->addCondition('qty','>',0);
        }
        $pc=$m_items->join('parts_catalogue');
        $pc->addField('description');
        $pc->addField('serialized')->type('boolean');
        $pc->addField('alternative_part_number');
       // $pc->addColumn('Description');
         $items->setModel($m_items);
         
        $items->addOrder()->move('description', 'after', 'parts_catalogue')->now();
        $items->addOrder()->move('serialized', 'after', 'description')->now();
        $items->addOrder()->move('alternative_part_number', 'after', 'description')->now();
        $items->removeColumn('stores');
        $this->api->stickyGet('sel_store');
        $items->addColumn('expander', 'TransferLog');
        $items->addQuickSearch(array('parts_catalogue','serial','description'),'QuickSearch',array('sel_store'=>$_GET['sel_store']));

    }
}