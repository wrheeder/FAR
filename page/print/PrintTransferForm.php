<?php
class page_print_PrintTransferForm extends Page{
    function init() {
        parent::init();
        $this->api->stickyGet('tn_code');
        $this->api->stickyGet('sel_store');
        
//        die(var_dump($_GET));
        
        $from_store = $this->add('Model_Stores')->load($_GET['sel_store']);
        $m_t_f = $this->add('Model_ItemTransferForm');
        $m_t_l = $m_t_f->load($_GET['tn_code'])->ref('ItemTrfList');
        $m_i=$m_t_l->join('items','items_id');
        $m_i->addField('serial');
        $m_p_cat = $m_i->join('parts_catalogue','parts_catalogue_id');
        $m_p_cat->addField('description');
        $m_p_cat->addField('warrantee');
//        $locators=$m_i->join('locators');
//        $locators->addField('locator');
        $to_store= $m_t_f->join('stores','to_stores_id');
        $to_store->addField('store_name')->Caption('to_store');
        $uom=$m_p_cat->join('unit_of_measure','unit_of_measure_id');
        $uom->addField('uom');
       // die(var_dump($m_b_l->getRows()))
        ;
        
        $lister=$this->add('MyLister',null,null,array('TransferForm'));
        $lister->setModel($m_t_l);
//        $lister->template->set('purchase_order',$_GET['purchase_order']);
        $lister->template->set('tn_code',$m_t_f->get('tn_code'));
        $lister->template->set('operation',$_GET['operation']);
        $lister->template->set('date_created',$m_t_f->get('date_created'));
//        $lister->template->set('purchase_order',$m_t_f->get('purchase_order'));
//        $lister->template->set('delivery_note',$m_t_f->get('delivery_note'));
        $lister->template->set('to_store',$m_t_f->get('to_stores'));
        $lister->template->set('store_name',$from_store->get('store_name'));
        $lister->template->set('date_changed',$m_t_f->get('date_changed'));
        $lister->template->set('notes',$m_t_f->get('notes'));
        $lister->template->set('user',$m_t_f->get('user'));
        $this->add('Html')->set('<script>window.print();</script>');
    }
//    public function defaultTemplate() {
//        return array('BookingInForm');
//    }
}
