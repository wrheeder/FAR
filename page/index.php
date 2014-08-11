<?php

class page_index extends Page_ApplicationPage {
    PUBLIC $isStockvan = false;
    public $home_store = null;
    public $home_store_type = null;
    public $transit_store = null;
    public $sites_array = array();
    public $opts = array("plugins" => array("html_data", "types", "context_menu", "ui"),
        "types" => array(
            "max_depth" => -2,
            "max_children" => -2,
            "valid_children" => array("drive"),
            "types" => array(
                "root" => array(
                    "valid_children" => array("Warehouse", "Regional Store", "Regional Transit Store", "Regional Stock Van"),
                    "icon" => array(
                        "image" => "./wr-addons/jsTree/js/themes/default/earth.png"
                    )
                ),
                "Warehouse" => array(
                    "valid_children" => "none",
                    "icon" => array(
                        "image" => "./wr-addons/jsTree/js/themes/default/warehouse-star.png"
                    )
                ),
                "Regional Store" => array(
                    "valid_children" => "none",
                    "icon" => array(
                        "image" => "./wr-addons/jsTree/js/themes/default/warehouse-star.png"
                    )
                ),
                "Regional Transit Store" => array(
                    "valid_children" => "none",
                    "icon" => array(
                        "image" => "./wr-addons/jsTree/js/themes/default/transit-store.png"
                    )
                ),
                "Regional Stock Van" => array(
                    "valid_children" => "none",
                    "icon" => array(
                        "image" => "./wr-addons/jsTree/js/themes/default/van-store.png"
                    )
                ),
                "Site" => array(
                    "valid_children" => "none",
                    "icon" => array(
                        "image" => "./wr-addons/jsTree/js/themes/default/site.png"
                    )
                )
            )
        ),
        "progressive_render" => true
    );

    function init() {
        parent::init();
        $this->api->jui->addStaticInclude('myJSFuncs');
        $tabs = $this->add('Tabs');

        $this->api->stickyGet('search_fld');
        $this->api->stickyGet('home_store');
        
//        $this->api->stickyGet('sel_store');    



        $form = $this->add('Form');
        $form->js(true)->hide();
        $sel_store = $form->addField('line', 'sel_store')->set($_GET['sel_store']);
        $search_fld_frm = $this->api->add('Form', null, 'search_fld');
    
        $m_stores = $this->add('Model_Stores');
        $tree = $search_fld_frm->add('jsTree/jsTree');
        $search_fld = $search_fld_frm->addField('line', 'search_fld')->set($_GET['search_fld'] ? $_GET['search_fld'] : 'None');
        $filter_subm=$search_fld_frm->addSubmit('Filter Sites');
        $this->loadTree($tree, $sel_store, $_GET['search_fld'] ? $_GET['search_fld'] : 'None');
        if(!$this->isStockvan){
          $search_fld->js(true)->closest('.atk-form-row')->hide();  
          $filter_subm->js(true)->hide();  
        }
        //die($this->transit_store);
        if ($_GET['sel_store']) {
            $sel_store->set($_GET['sel_store']);
            $m_stores->tryload($sel_store->get());
            $browse_tab = $tabs->addTabURL($this->api->url('browseStore', array('sel_store' =>  $sel_store->get(), 'store_type' => $m_stores->get('store_type'), 'home_store' => $this->home_store, 'home_store_type' => $this->home_store_type,'home_transit'=>$this->transit_store)), 'Store Browser');
            $cur_store = $m_stores->get();
            if ($sel_store->get() == '9999' || $sel_store->get() == '9998' || $sel_store->get() == '9997') {
                $tabs->js(true)->hide();
            } else {
                $tabs->js(true)->show();
            }
            //$this->echo('$this->home_store_type');
            if ($_GET['sel_store'] == $this->home_store) {
                $pick_list_tab = $tabs->addTabURL($this->api->url('PickList_Transfer', array('sel_store' => $sel_store->get(), 'store_type' => $m_stores->get('store_type'), 'home_store' => $this->home_store, 'home_store_type' => $this->home_store_type)), 'Transfer Equipment/Items');
                if ($this->home_store_type != 'Regional Stock Van' && $this->home_store_type != 'National Stock Van') {
                    $add_item_tab = $tabs->addTabURL($this->api->url('BookInEquipmentItems', array('sel_store' => $sel_store->get(), 'store_type' => $m_stores->get('store_type'), 'home_store' => $this->home_store, 'home_store_type' => $this->home_store_type)), 'Book In Equipment/Items');
                }
            } elseif ($_GET['sel_store'] == $this->transit_store) {
                $pick_list_tab = $tabs->addTabURL($this->api->url('PickList_Collect', array('sel_store' => $sel_store->get(), 'store_type' => $m_stores->get('store_type'), 'home_store' => $this->home_store, 'home_store_type' => $this->home_store_type)), 'Collection PickList');
            } elseif ($cur_store['store_type'] == 'Site') {
                $pick_list_tab = $tabs->addTabURL($this->api->url('PickList_Collect', array('sel_store' => $sel_store->get(), 'store_type' => 'Site', 'home_store' => $this->home_store, 'home_store_type' => $this->home_store_type)), 'Collection Equipment/Items');
                // $pick_list_tab = $tabs->addTabURL($this->api->url('PickList_Transfer',array('sel_store'=>$_GET['sel_store'],'store_type'=>'Site','home_store'=>$this->home_store,'home_store_type'=>$this->home_store_type)), 'Transfer PickList');
                $add_item_tab = $tabs->addTabURL($this->api->url('BookInEquipmentItems', array('sel_store' => $sel_store->get(), 'store_type' => $m_stores->get('store_type'), 'home_store' => $this->home_store, 'home_store_type' => $this->home_store_type)), 'Book In Equipment/Items');
            }
        } else {
            // $items->js(true)->hide();
        }


        $form->addSubmit();

//        if ($_GET['Transfer']) {
//            $items->js()->univ()->dialogURL('Transfer Item/Equipment', $this->api->getDestinationURL(
//                                    'SingleTransfer', array(
//                                'spare_id' => $_GET['Transfer'],
//                                'cut_object' => 'form'
//                    )))
//                    ->execute();
//        }
//        if ($_GET['Collect']) {
//            $items->js()->univ()->dialogURL('Collect Item/Equipment', $this->api->getDestinationURL(
//                                    'SingleCollect', array(
//                                'spare_id' => $_GET['Collect'],
//                                'cut_object' => 'form'
//                    )))
//                    ->execute();
//        }
        if ($form->isSubmitted()) {
            $js = array();
//            if ($sel_store->get() == '9999' || $sel_store->get() == '9998') {
//                $js[] = $items->js(true)->hide();
//            } else {
//                $js[] = $items->js(true)->show();
//            }
            $js[] = $this->js()->reload(array('sel_store' => $sel_store->js()->val()));//, 'search_fld' => $search_fld->get()
            $this->js(true, $js)->univ()->successMessage('Submitted')->execute();
        }
        if ($search_fld_frm->isSubmitted()) {
             $js = array();
            $js[] = $search_fld_frm->js()->reload(array('sel_store' => $sel_store->js()->val()));//, 'search_fld' => $search_fld->get()
            $search_fld_frm->js(true, $js)->univ()->successMessage('Submitted')->execute();
        }
    }

    function loadTree($tree, $sel_store, $search_fld = 'None') {
        $m_usr = $this->add("Model_User")->addCondition('id', $this->api->auth->model->id);
        $m_usr->loadAny();
        $m_reg_store = $m_usr->ref("UserStores");
        $m_stores = $this->add('Model_Stores');
        
        $src = array();
        $src[] = array('ids' => 9999, 'name' => 'Your Store', 'rel' => 'root', 'parent_id' => null);
        $src[] = array('ids' => 9998, 'name' => 'Transit Stores', 'rel' => 'root', 'parent_id' => null);

        foreach ($m_reg_store as $store) {
            $m_stores->load($store['stores_id']);
            $cur_store = $m_stores->get();
            $src[] = array('ids' => $cur_store['id'], 'name' => $cur_store['store_name'], 'rel' => $cur_store['store_type'], 'parent_id' => $cur_store['parent_store_id']);
        }
        $i = 0;
        $home_offset = 0;
        foreach ($src as $str) {
            if ($str['rel'] == 'Warehouse' || $str['rel'] == 'Regional Store' || $str['rel'] == 'Customer Care' || $str['rel'] == 'Regional Stock Van') {
                $home_store_id = $str['ids'];
                $this->home_store = $home_store_id;
                $this->home_store_type = $str['rel'];
                $home_offset = $i;
            }
            $i++;
        }

        if ($this->home_store_type == 'Regional Stock Van') {
            $this->isStockvan=true;
            $src[] = array('ids' => 9997, 'name' => 'Sites', 'rel' => 'root', 'parent_id' => null);
            $m_stores->unload();
//            $m_stores->debug();
            if ($search_fld != "None")
                
                $m_stores->addCondition('store_type', 'Site'); 
                $m_stores->addCondition('store_name', 'like', '%'.$search_fld.'%');
//                $m_stores->debug();
                $m_stores->setOrder('store_name', 'asc');
//            $m_home_store=$m_stores->load($this->home_store);
//            $m_home_transit_store=$m_stores->load($m_home_store->get('parent_store_id'));
//            $m_region_warehouse = $m_stores->load($m_home_transit_store->get('parent_store_id'));
//            $tmp_id=$m_region_warehouse->id;
//            $m_stores->unload();
//            $m_stores->addCondition('parent_store_id',$tmp_id);
//            $m_stores->addCondition('store_type','Site');
                $sites = $m_stores->getRows();
                //die(var_dump($sites));
            
//            $this->sites_array = $sites;
//            //die(var_dump($sites));
            foreach ($sites as $cur_site) {
                $src[] = array('ids' => $cur_site['id'], 'name' => $cur_site['store_name'], 'rel' => $cur_site['store_type'], 'parent_id' => 9997);
            }
            
        }
        foreach ($src as &$str) {
            if ($str['ids'] != $home_store_id && $str['parent_id'] != $home_store_id && $str['rel'] != 'Site') {
                $str['parent_id'] = null;
            } else {
                if ($str['ids'] == $home_store_id)
                    $str['parent_id'] = 9999;
                else {
                    if ($str['rel'] != 'Site')
                        $this->transit_store = $str['ids'];
                    else {
                        $str['parent_id'] = 9997;
                    }
                }
            }
        }
        foreach ($src as &$str) {
            if ($str['ids'] != $home_store_id && $str['ids'] != 9999 && $str['ids'] != 9998 && $str['parent_id'] != $home_store_id && $str['ids'] != 9997 && $str['rel'] != 'Site') {
                $str['parent_id'] = 9998;
            }
        }
        $tree->setSource($src, $tree, $sel_store, $this->opts);
    }

}