<?php
class Page_PickList extends Page{
    function init() {
        parent::init();
        die(var_dump($_GET));
    }
}