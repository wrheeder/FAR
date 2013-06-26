<?php
class Page_search extends Page_ApplicationPage{
    function init() {
        parent::init();
        $this->add('View_Info')->set('Type in Search field to search Serial/Description/Alt PN/Stores/Locators/Part Status and Types');
        $g=$this->add('Grid');
        $m=$this->add('Model_Items');
        $m->addCondition('qty','>',0);
        $pc=$m->join('parts_catalogue');
        $pc->addField('description')->type('text');
        $pc->addField('alternative_part_number')->type('text');
        $pc->hasOne('UnitOfMeasure',null,'uom');
        $pc->addField('finance_track')->type('boolean');
        $pc->addField('serialized')->type('boolean');
        //$pc->addField('warrantee1');
        $pc->addField('asset_tag_managed')->type('boolean');
        $pc->hasOne('PrimaryCategory',null,'category_code');
        $pc->hasOne('SecondaryCategory',null,'category_code');
        $pc->hasOne('TertiaryCategory',null,'category_code');
        $pc->hasOne('Supplier',null,'supplier_name');
        $store=$m->join('stores');
        $store_type=$store->join('store_type');
        $store_type->addField('type')->type('text');
        $g->setModel($m);
        $g->addQuickSearch(array('serial','description','alternative_part_number','stores','locators','part_status','type'));
        $g->addPaginator(15);
    }
}