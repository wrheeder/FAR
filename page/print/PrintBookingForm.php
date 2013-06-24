<?php
class page_print_PrintBookingForm extends Page{
    function init() {
        parent::init();
        $this->api->stickyGet('booking_nr');
        $this->api->stickyGet('sel_store');
        
        
        
        $m_b_f = $this->add('Model_ItemBookingForm');
        $m_b_l=$m_b_f->load($_GET['booking_nr'])->ref('ItemBookingList');
        $m_i=$m_b_l->join('items','items_id');
        //die(var_dump($m_i->addField('qty')->get()));
        $m_i->addField('serial');
        $p_s=$m_i->join('part_status');
        $p_s->addField('status');
        $m_p_cat = $m_i->join('parts_catalogue','parts_catalogue_id');
        $m_p_cat->addField('description');
        $m_p_cat->addField('warrantee');
        $locators=$m_i->join('locators');
        $locators->addField('locator');
        $store= $m_i->join('stores','stores_id');
        $store->addField('store_name');
        $uom=$m_p_cat->join('unit_of_measure','unit_of_measure_id');
        $uom->addField('uom');
       // die(var_dump($m_b_l->getRows()))
        ;
        
        $lister=$this->add('MyLister',null,null,array('BookingInForm'));
        $lister->setModel($m_b_l);
        $lister->template->set('purchase_order',$_GET['purchase_order']);
        $lister->template->set('bn_number',$m_b_f->get('booking_nr'));
        $lister->template->set('date_created',$m_b_f->get('date_created'));
        $lister->template->set('purchase_order',$m_b_f->get('purchase_order'));
        $lister->template->set('delivery_note',$m_b_f->get('delivery_note'));
        $lister->template->set('store_name',$m_b_l->loadAny()->get('store_name'));
        $lister->template->set('date_changed',$m_b_f->get('date_changed'));
        $lister->template->set('notes',$m_b_f->get('notes'));
        $lister->template->set('user',$m_b_f->get('user'));
        $this->add('Html')->set('<script>window.print();</script>');
    }
//    public function defaultTemplate() {
//        return array('BookingInForm');
//    }
}

