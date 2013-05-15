<?php

class page_admin extends Page_ApplicationPage {

    function init() {
        parent::init();
        $tabs = $this->add('Tabs');
        $stores = $tabs->addTab('Stores')->add("CRUD");
        $store_types = $tabs->addTab('Store Types')->add("CRUD");
        $user = $tabs->addTab('Users')->add("CRUD");
        $m_usr = $user->setModel('User', array('username', 'email', 'isAdmin'));
        $m_str = $stores->setModel('Stores');
        $m_strtype = $store_types->setModel('StoreType');
        $this->api->stickyGet('id');
        if ($user->grid) {
            $user->grid->addQuickSearch(array('Username', 'email', 'subco'));
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
            $stores->grid->dq->order('store_name asc');
            $stores->grid->addClass("zebra bordered");
            $stores->grid->addPaginator(15);

            $stores->grid->addColumn('button', 'Add_Locator');
            if($stores->model['store_type']){
                
            }
            if ($_GET['Add_Locator']) {
                $stores->grid->js()->univ()->dialogURL('Add Locator', $this->api->getDestinationURL(
                                        'AddLocator', array(
                                    'store_id' => $_GET['Add_Locator'],
                                    'cut_object' => 'form'
                        )))
                        ->execute();
            }
        }
        if($stores->form){
          $stores->form->getElement('store_type_id')->js('change')->hide();
        }
    }

}