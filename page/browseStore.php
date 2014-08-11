<?php

class Page_BrowseStore extends Page_ApplicationPage {

    function init() {
        parent::init();
        $items = $this->add('Grid');
        $items->addClass("zebra bordered");
        $this->api->stickyGet('home_transit');
        $this->api->stickyGet('home_store_type');
        $this->api->stickyGet('store_type');
        $this->api->stickyGet('home_store');
        $this->api->stickyGet('col_sel_prep');
        $this->api->stickyGet('sel_store');
        
        
        
        $f = $this->add('Form');
        
        $m_items = $this->add('Model_Items');
        
        $m_items->addCondition('stores_id', $_GET['sel_store']);
        
        
        
//        echo $this->js(true)->_selector('#FAR_index_form_sel_store')->val();
        

        if ($_GET['store_type'] == 'Regional Transit Store' || $_GET['store_type'] == 'Site' || $_GET['store_type'] == 'Regional Stock Van') {
            $m_items->addCondition('qty', '>', 0);
        }
        $pc = $m_items->join('parts_catalogue');
        $pc->addField('description');
        $pc->addField('serialized')->type('boolean');
        $pc->addField('alternative_part_number');
        // $pc->addColumn('Description');
        $items->setModel($m_items);

        $items->addOrder()->move('description', 'after', 'parts_catalogue')->now();
        $items->addOrder()->move('serialized', 'after', 'description')->now();
        $items->addOrder()->move('alternative_part_number', 'after', 'description')->now();
        
        
        
        
        $sel = $f->addField('line', 'col_sel');
        $bulk_sel = $f->addField('line', 'bulk_sel');
        $sel->js(true)->closest('.atk-form-row')->hide();
        $bulk_sel->js(true)->closest('.atk-form-row')->hide();
        
        if ((($_GET['sel_store'] == $_GET['home_transit']) || ($_GET['home_store_type'] == 'Regional Stock Van') && ($_GET['store_type'] == 'Regional Transit Store'))) {
        $act=$items->addButton('Act/De-Act Bulk Collect');
        $act->js('click',array($bulk_sel->js()->val('1'),$this->js()->reload(array('bulk_sel'=>$_GET['bulk_sel']==0?1:0))));
        
        $ref = $items->addButton('Refresh')->js('click',array($sel->js()->val(''),$items->js()->reload(array('bulk_sel'=>$_GET['bulk_sel']))));
        }
        $this->api->stickyGet('col_sel');
        $this->api->stickyGet('bulk_sel');
        
        $bulkColBut = $f->addSubmit('Bulk Collect');
        $bulkColBut->js(true)->hide();
        
        
        if ((($_GET['sel_store'] == $_GET['home_transit']) || ($_GET['home_store_type'] == 'Regional Stock Van') && ($_GET['store_type'] == 'Regional Transit Store')) && $_GET['bulk_sel']) {

             
            $items->addSelectable($sel);
            
            
            
            $col_sel = $sel->js(true)->val();
            $url_bulkCollect = $this->api->url('PickList/BulkCollect', array('sel_store'=>$_GET['sel_store'],'col_sel' => $col_sel,'col_sel_prep' =>json_encode(array())));
            

            $js = array();
            if ($sel->js()->val() === "[]" || $sel->js()->val() === "") {
                $js[] = $bulkColBut->js()->hide();
            } else {
                $js[] = $bulkColBut->js()->show();
            }
            $sel->js('autochange_manual', $js);
        } else {
            $exp = $items->addColumn('expander', 'TransferLog');    
            $items->addPaginator(25);
        }
        $this->api->stickyGet('slected');
        $items->removeColumn('stores');
        //$this->js("reloadpage", $this->js()->reload())->_selector('body');
        $items->js("reloadgrid", $items->js()->reload(array('sel_store'=>$_GET['sel_store'])));
        if ($f->isSubmitted()) {
            $bulkColBut->js()->univ()->frameURL('Bulk Collect', $this->api->url('PickList/BulkCollect', array('sel_store'=>$_GET['sel_store'],'col_sel' => $sel->get(),'col_sel_prep' =>json_encode(array()))))->execute();
        }
        $items->addQuickSearch(array('parts_catalogue', 'serial', 'description'), 'QuickSearch',array('sel_store'=>$_GET['sel_store']));
    }

}
