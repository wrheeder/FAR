<?php

class View_PartsCatalogueInfo extends View_Hint {

    function init() {
        parent::init();
        //$this->add('hint','pn_hint');
        $this->update($_GET['part_num'], $_GET['serial_fld'], $_GET['qty_fld'],$_GET['icn_fld']);
    }
    
    
    function update($pn, $ser, $qty, $icn) {
        $js = array();
        $pc = $this->add('Model_PartsCatalogue');
        
        if ($pn == null) {
            $pn = 'Parts Catalogue not Yet selected';
            $desc = '-';
            $supp = '-';
            $alt_pn = '-';
            $warantee = '-';
            $ser = '-';
            $this->set('Description :' . $desc . ' | Serialized :' . $ser . ' | Supplier :' . $supp . ' | Alternative PN:' . $alt_pn . ' | Warrantee:' . $warantee);
            $js[] = $this->js()->_selector($ser)->attr('readonly', false);
            $js[] = $this->js()->_selector($qty)->attr('readonly', false);
            $this->js(true, $js);
        } else {
            $this->setTitle('Selected PartNumber');
            $pc->load($pn);
            
            $desc = $pc->get('description');
            $supp = $pc->get('supplier');
            $alt_pn = $pc->get('alternative_part_number');
            $warantee = $pc->get('warrantee');
            $ser = $pc->get('serialized') ? 'Yes' : 'No';
            $this->set('Description :' . $desc . ' | Serialized :' . $ser . ' | Supplier :' . $supp . ' | Alternative PN:' . $alt_pn . ' | Warrantee:' . $warantee);
            if ($ser == "Yes") {
                $js[] = $this->js()->_selector($ser)->removeAttr('readonly');
                $js[] = $this->js()->_selector($qty)->attr('readonly', true);
                $js[] = $this->js()->_selector($qty)->val(1);
                $js[] = $this->js()->_selector($icn)->show();
                $this->js(true, $js);
            } else {
                
                $js[] = $this->js()->_selector($qty)->removeAttr('readonly');
                $js[] = $this->js()->_selector($ser)->attr('readonly', true);
                $js[] = $this->js()->_selector($icn)->hide();
                //var_dump($js);
                $this->js(true, $js);
            }
//           $f=$this->add('Form');
//           $f->setModel($pc);
        }
        if ($pn == 'Parts Catalogue not Yet selected')
            $this->setTitle('Select Parts Catalogue');
    }

}