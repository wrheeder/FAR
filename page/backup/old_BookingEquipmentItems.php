<?php

class Page_BookInEquipmentItems extends Page {

    function init() {
        parent::init();
        $this->js('myFunc')->reload();
        $this->api->stickyGet('sel_store');
        //$f = $this->add('Form');
        $crud = $this->add('CRUD');
        $m_book_form = $crud->setModel('ItemBookingForm')->addCondition('stores_id', $_GET['sel_store']);
        $crud->js('custom_r')->reload();
        if ($crud->form) {
            if (!$crud->form->getElement('booking_nr')->get()) {
                $booking_nr = $crud->form->getElement('booking_nr')->set(strtoupper(uniqid("BN" . date("Ymd") . '_')));
            } else {
                $booking_nr = $crud->form->getElement('booking_nr');
            }

            $store_id = $crud->form->getElement('stores_id')->set($_GET['sel_store']);
            $m_bookl = $this->add('Model_ItemBookingList');
            $m_bookl->addCondition('item_booking_form_id', $_GET['FAR_BookInEquipmentItems_crud_virtualpage_id']);
            $_GET['booking_nr'] = $booking_nr->get();
            $filter = $crud->form->add('Filter');
            $booking_l = $crud->form->add('Grid');
            $this->js(true)->univ()->setInterval(
                    $booking_l->js(null, array(
                        $booking_l->js()->reload(),
                        $booking_l->js()->univ()->successMessage('Reloaded...')
                    ))->_enclose()
                    , 3000);
//            $booking_l->js('myFunc')->reload();
            $booking_l->setModel($m_bookl);
            $filter->useWith($booking_l);
            // $crud->form->getElement('stores_id')->disable();
//            $booking_list=$crud->form->add('CRUD');
//            $m_book_list = $this->add('Model_ItemBookingList');
//            $booking_list->setModel($m_book_list);
            $this->api->stickyGet('booking_nr');
            $this->api->stickyGet('id');
            if ($_GET['FAR_BookInEquipmentItems_crud_virtualpage'] != 'add') {
                $js[] = $crud->form->js()->univ()->frameURL('Book in Item/Equipment', $this->api->getDestinationURL(
                                'AddItem', array(
                            'cut_object' => 'form'
                )));
                $js[] = $this->js(true)->trigger('myFunc');
                $js[] = $this->js()->univ()->getFrameOpener()->closest('.atk4_grid')->atk4_grid('reload');
                $crud->form->addButton('Add Items/Equipment')->js('click', $js);
            }

            $this->api->stickyGet('booking_form_id');



//            if ($crud->form->isSubmitted()) {
////                $crud->form->model->unload();
//                $js[] = $crud->form->js()->univ()->closeDialog();
//                $crud->form->js(true)->univ()->successMessage('Updated Booking Form')->execute();
//            }
        }
    }

}