<?php

class page_index extends Page_ApplicationPage {

    public $opts = array("plugins" => array("html_data","ui", "cookies", "checkbox")
    );

    function init() {
        parent::init();
        $tree = $this->add('jsTree/jsTree');
        $src = array(
            array('ids' => 0, 'name' => 'Western Cape', 'rel' => 'region', 'parent_id' => null),
            array('ids' => 1, 'name' => 'NSB', 'rel' => 'project', 'parent_id' => 0),
            array('ids' => 2, 'name' => '5257', 'rel' => 'site', 'parent_id' => 1),
            array('ids' => 3, 'name' => '5258', 'rel' => 'site', 'parent_id' => 1),
            array('ids' => 4, 'name' => '8423', 'rel' => 'site', 'parent_id' => 1),
            array('ids' => 55, 'name' => '4782', 'rel' => 'site', 'parent_id' => 1)
        );
        $f = $this->add("Form");

        //$f->js(true)->hide();
        $prop_id = $f->addField('line', 'test')->set('nothing');
        $subm = $f->addSubmit('cool');
        $tree->setSource($src, $tree, $subm, $this->opts);
        if ($f->isSubmitted()) {
            if ($f->get('test') == 2) {
                $this->js(true)->univ()->successMessage('Submitted!')->execute();
            } else {
                $this->js(true)->univ()->successMessage('Submitted')->execute();
            }
        }
    }

}