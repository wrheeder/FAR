$other_frm = $this->add("Form");
        $this->api->stickyGet('sel_store');
        $info = $other_frm->add('text', 'info')->set('Click on Store to continue ...');
        $tree = $this->add('jsTree/jsTree');
        $warehouse_frm = $this->add("Form");
        $crud = $warehouse_frm->add('Grid');


        $warehouse_frm->addSubmit();
        //$warehouse_frm->js(true)->hide();

        $this->api->jui->addStaticInclude('myJSFuncs');
        //die(var_dump($this->api->auth));
        $m_usr = $this->add("Model_User")->addCondition('username', $this->api->auth->model['username']);
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
            if ($str['rel'] == 'Warehouse' || $str['rel'] == 'Regional Store' || $str['rel'] == 'Customer Care') {
                $home_store_id = $str['ids'];
                $home_offset = $i;
            }
            $i++;
        }

        foreach ($src as &$str) {
            if ($str['ids'] != $home_store_id && $str['parent_id'] != $home_store_id) {
                $str['parent_id'] = null;
            } else {
                if ($str['ids'] == $home_store_id)
                    $str['parent_id'] = 9999;
            }
        }

        foreach ($src as &$str) {
            if ($str['ids'] != $home_store_id && $str['ids'] != 9999 && $str['ids'] != 9998 && $str['parent_id'] != $home_store_id) {
                $str['parent_id'] = 9998;
            }
        }
        $f = $this->add("Form");

        //$f->js(true)->hide();
        $sel_store = $f->addField('line', 'sel_store');
        if($_GET['sel_store'])
            $sel_store->set($_GET['sel_store']);
        else
            $sel_store->set('nothing');
        $subm = $f->addSubmit();

        $m_spare = $this->add('model_spare');
        $m_spare->addCondition('stores_id', $sel_store->get());
        $crud->setModel($m_spare);

        $tree->setSource($src, $tree, $sel_store, $this->opts);
        if ($f->isSubmitted()) {
            $js = array();
            if ($sel_store->get() != 'nothing') {
                $js[] = $other_frm->js(true)->hide();
                $m_stores->tryload($sel_store->get());
                $cur_store = $m_stores->get();
                if ($cur_store['store_type'] == "Warehouse" || $m_stores['store_type'] == "Regional Store") {
                    $this->api->stickyGet('sel_store');

                    $m_spare->addCondition('stores_id', $sel_store->get());
                    $crud->setModel($m_spare);
                    
                    $js[] = $warehouse_frm->js(true)->reload();
                    $js[] = $warehouse_frm->js(true)->show();
                    $js[] = $this->js()->univ()->location($this->api->url('index'), array('test' => 'working'));
                } else {
                    $m_spare->addCondition('id', $sel_store->get());
                    $crud->setModel($m_spare);
                    // $js[]=$warehouse_frm->js(true)->hide();
                    $js[] =
                            $warehouse_frm->js(true)->reload();
                }
            }
            if ($f->get('sel_store') == 2) {
                $this->js(true, $js)->univ()->successMessage('Submitted!')->execute();
            } else {
                $this->js(true, $js)->univ()->successMessage('Submitted')->execute();
            }
     