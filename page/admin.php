<?php

class page_admin extends Page_ApplicationPage {

    function init() {
        parent::init();
        if ($this->api->auth->isAdmin()) {
            $tabs = $this->add('Tabs');
            $stores = $tabs->addTab('Stores')->add("CRUD");
            $store_types = $tabs->addTab('Store Types')->add("CRUD");
            $user = $tabs->addTab('Users')->add("CRUD");
            $parts_catalogue = $tabs->addTab('Parts Catalogue')->add("CRUD");

            $m_parts_cat = $parts_catalogue->setModel('PartsCatalogue');
            $parts_status = $tabs->addTab('Parts Status')->add("CRUD");

            $m_parts_status = $parts_status->setModel('PartStatus');
            $unit_of_measure = $tabs->addTab('Unit Of Measure')->add('CRUD');
            $primary_cat = $tabs->addTab('Primary Category')->add("CRUD");
            $m_pri_cat = $primary_cat->setModel('PrimaryCategory');
            $secondary_cat = $tabs->addTab('Second Category')->add("CRUD");
            $m_sec_cat = $secondary_cat->setModel('SecondaryCategory');

            $tertiary_cat = $tabs->addTab('Tertiary Category')->add("CRUD");
            $m_ter_cat = $tertiary_cat->setModel('TertiaryCategory');
            $supplier = $tabs->addTab('Supplier')->add("CRUD");
            $m_supplier = $supplier->setModel('supplier');
            $m_uom = $unit_of_measure->setModel('UnitOfMeasure');
            $m_usr = $user->setModel('User', array('username', 'email', 'isAdmin', 'store_search'));
            $m_str = $stores->setModel('Stores');
            $m_strtype = $store_types->setModel('StoreType');
            $this->api->stickyGet('id');
            if ($user->grid) {
                $user->grid->addQuickSearch(array('Username', 'email'));
                $user->grid->getColumn('username')->makeSortable();
                $user->grid->dq->order('username asc');
                $user->grid->addClass("zebra bordered");
                $user->grid->addPaginator(10);

                $user->grid->addColumn('button', 'changePassword');
                if ($_GET['changePassword']) {

                    // Get the name of currently selected member
                    $name = $user->grid->model->load($_GET['changePassword'])->get('username');

                    // Open frame with member's name in the title. Load content through AJAX from subpage
                    $this->js()->univ()->frameURL('Change Password for ' . $name, $this->api->url('admin/changePassword', array('id' => $_GET['changePassword'])))
                            ->execute();
                }

                $user->grid->addColumn('expander', 'UserStore');
            }
            
            $user->grid->addColumn('expander','UserStore');
        }
        if ($user->form) {
            //$user->form->addField('password','password');
            if ($user->form->isSubmitted()) {
                $m = $user->form->getModel();
                if ($m['password'] == null || $m['password'] == '')
                    $m->set('password', $this->api->auth->encryptPassword('tempPW1234'));
                $m->save();
            }
        }
        if ($stores->grid) {
            
           $stores->grid->addQuickSearch(array('store_name','store_type'));
            $stores->grid->getColumn('store_name')->makeSortable();
            $stores->grid->getColumn('store_type')->makeSortable();
            $stores->grid->dq->order('store_name asc');
            $stores->grid->addClass("zebra bordered");
            $stores->grid->addPaginator(15);

            $stores->grid->addColumn('expander', 'AddLocator');
            if($stores->model['store_type']){
                
            }
//            if ($_GET['Add_Locator']) {
//                $this->api->stickyGet('id');
//                $stores->grid->js()->univ()->dialogURL('Add Locator', $this->api->getDestinationURL(
//                                        'admin_AddLocator', array(
//                                    'store_id' => $_GET['Add_Locator'],
//                                    'cut_object' => 'form'
//                        )))
//                        ->execute();
//            }
            }
            if ($store_types->grid) {
                $store_types->grid->getColumn('type')->makeSortable();
                $store_types->grid->dq->order('type asc');
                $store_types->grid->addClass("zebra bordered");
                $store_types->grid->addPaginator(15);
            }
            if ($parts_catalogue->grid) {
                $parts_catalogue->grid->addQuickSearch(array('part_number', 'description', 'primary_category', 'secondary_category', 'tertiary_category', 'supplier', 'alternative_part_number'));
                $parts_catalogue->grid->getColumn('part_number')->makeSortable();
                $parts_catalogue->grid->dq->order('part_number asc');
                $parts_catalogue->grid->addClass("zebra bordered");
                $parts_catalogue->grid->addPaginator(25);
            }

            if ($unit_of_measure->grid) {
                $unit_of_measure->grid->getColumn('uom')->makeSortable();
                $unit_of_measure->grid->dq->order('uom asc');
                $unit_of_measure->grid->addClass("zebra bordered");
                $unit_of_measure->grid->addPaginator(25);
            }

            $movement = $tabs->addTab('Item Movement Log')->add('Grid');
            $movement->setModel('TransferLog');
            if ($primary_cat->grid) {
                $primary_cat->grid->getColumn('category')->makeSortable();
                $primary_cat->grid->dq->order('category asc');
                $primary_cat->grid->addClass("zebra bordered");
                $primary_cat->grid->addPaginator(25);
            }
            if ($secondary_cat->grid) {
                $secondary_cat->grid->getColumn('category')->makeSortable();
                $secondary_cat->grid->dq->order('category asc');
                $secondary_cat->grid->addClass("zebra bordered");
                $secondary_cat->grid->addPaginator(25);
            }
            if ($tertiary_cat->grid) {
                $tertiary_cat->grid->getColumn('category')->makeSortable();
                $tertiary_cat->grid->dq->order('category asc');
                $tertiary_cat->grid->addClass("zebra bordered");
                $tertiary_cat->grid->addPaginator(25);
            }
            if ($supplier->grid) {
                $supplier->grid->getColumn('supplier_name')->makeSortable();
                $supplier->grid->dq->order('supplier_name asc');
                $supplier->grid->addClass("zebra bordered");
                $supplier->grid->addPaginator(25);
            }
            if ($parts_status->grid) {
                $parts_status->grid->getColumn('status')->makeSortable();
                $parts_status->grid->dq->order('status asc');
                $parts_status->grid->addClass("zebra bordered");
                $parts_status->grid->addPaginator(25);
            }
            if ($movement) {
                $movement->addQuickSearch(array('items', 'user', 'to_stores', 'from_stores', 'time'));
                $movement->getColumn('items')->makeSortable();
                $movement->dq->order('items asc');
                $movement->addClass("zebra bordered");
                $movement->addPaginator(25);
            }
        }
        else
            $this->api->redirect('index');
    }

}