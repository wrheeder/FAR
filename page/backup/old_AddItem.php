<?php

class Page_AddItem extends Page {

    public $booking_nr = null;

    function init() {

        parent::init();

        if ($_GET['booking_nr'] != null)
            $this->booking_nr = $_GET['booking_nr'];
        $f = $this->add('Form');
        $f->addField('line', 'booking_nr')->set($this->booking_nr);
        //$f->addField('line')
        $hint=$f->add('Hint','part_cat_det');
        $f->setModel('Items');
        $part_catalogue_id=$f->getElement('parts_catalogue');
      //  die($part_catalogue_id);
        $serial=$f->getElement('serial');
        $serial->addHook('validate', function() use ($serial,$part_catalogue_id) {
                
                
                    if ($serial->get() == 'test@example.com')
                        $serial->displayFieldError($part_catalogue_id->get());
                });
        
//        $serial = $f->getElement('serial')->disable();
//        $f->getElement('parts_catalogue')->js(true,);
        $f->addSubmit('Add Item');
        $this->api->stickyGet('sel_store');
        $this->api->stickyGet('booking_nr');

        if ($f->isSubmitted()) {
            $js = array();
            $f->update();

            $log = $this->add('Model_TransferLog');
            $output = array('items_id' => $f->model->id, 'user_id' => $this->api->auth->model->id, 'from_stores_id' => $f->model->get('stores_id'), 'to_stores_id' => $f->model->get('stores_id'), 'time' => date("Y-m-d H:i:s"), 'system_comment' => 'Item added to Store - BN :' . $f->getElement('booking_nr')->get());
            //die(var_dump($output));
            $log->set($output);
            $log->save();


            $booking_form = $this->add('Model_ItemBookingForm')->tryloadBy('booking_nr', $f->getElement('booking_nr')->get());
//            if($booking_form->loaded()){
//            }else
//            {
//                $booking_form['booking_nr']='TBD';//$f->getElement('booking_nr')->get();
//                $booking_form['user_id']=$this->api->auth->model->id;
//                $booking_form['stores_id']=$f->getElement('stores_id')->get();
//                $booking_form->save();
//            }

            $item_booking_list = $this->add('Model_ItemBookingList');
            $item_booking_list['item_booking_form_id'] = $booking_form->id;
            $item_booking_list['items_id'] = $f->model->id;
            $item_booking_list->save();
            $js = array();
            $js[]=$f->js(true, $js)->univ()->closeDialog();
            $js[]=$this->js()->_selector('#FAR_FAR_BookInEquipmentItems_crud_virtualpage_form_grid')->reload();
            $f->js(true,$js)->univ()->successMessage('Item Added')->execute();
        }
    }

}

function isSerialized($fld) {

    return true;
}