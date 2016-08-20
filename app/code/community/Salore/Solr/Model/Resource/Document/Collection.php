<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Solr to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Solr
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Solr_Model_Resource_Document_Collection extends Salore_Solr_Model_Resource_Collection_Abstract {
    protected $queryFields = 'textSearchStandard^80 textSearch^40 textSearchText^10 textSearchGeneral^1';
    
    public function prepareQuery() {        
        if (!isset($this->filterQuery['q']) || empty($this->filterQuery['q'])) {
            $this->addFilterQuery('q', '*:*');
        } 
        
        $this->addFilterQuery('qf', $this->queryFields);
        $this->addFilterQuery('json.nl', 'map');
        $this->addFilterQuery('defType', 'edismax');
        $this->addFilterQuery('wt', 'json');
    }
}