<?php

class Page_ApplicationPage extends Page{
    
    public $dec=5;
    function init(){
        parent::init();
        if($this->api->auth->isLoggedIn()) 
        {
            $this->api->template->set('Welcome','Logged In as '.$this->api->auth->get('username'));
        }
            
        $this->api->template->set('run_time','Page Rendered in ...'.substr(microtime(true) - $_SERVER['REQUEST_TIME'],0,$this->dec).' seconds');
    }
//    function render(){
//        parent::render();
//        
//    }
    function setLimit($dec = 5){
        $this->dec=$dec;
    }
}