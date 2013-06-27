<?php
class Page_partscatalogue extends Page_ApplicationPage{
    function init() {
        parent::init();
        $this->add('View_Info')->set('Type in Search field to search Part Number/Description and Alt PN');
        $g=$this->add('Grid');
        $m=$this->add('Model_PartsCatalogue');
        
        $g->setModel($m);
        $g->addQuickSearch(array('part_number','description','alternative_part_number'));
        $g->addPaginator(15);
    }
}