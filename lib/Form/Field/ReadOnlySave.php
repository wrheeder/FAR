<?php
class Form_Field_ReadOnlySave extends Form_Field_Line{
    function init() {
        parent::init();
        $this->js(true)->attr('readonly', true);
    }
}