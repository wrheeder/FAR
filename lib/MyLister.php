<?php

class MyLister extends CompleteLister {
    public $i=0;
    function init(){
        parent::init();
    }
    function formatRow() {
        parent::formatRow();
        $this->i++;
        $this->current_row['row_cnt']=$this->i;
    }

}