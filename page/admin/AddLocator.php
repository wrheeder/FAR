<?php
class Page_admin_AddLocator extends Page{
    function init(){
        parent::init();
//        $f=$this->add('Form');
        
       
        $m_crud = $this->add('Model_Locators')->addCondition('stores_id',$_GET['stores_id']);
        $this->api->stickyGet('stores_id');
        $this->api->stickyGet('id');
        $crud = $this->add('CRUD');
        $crud->setModel($m_crud);
        if($crud->grid){
            $crud->grid->addPaginator(10);
            $crud->grid->addQuickSearch(array('locator'));
        }
        
    }
        
}