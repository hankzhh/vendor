<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Solr
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Solr_Model_Resource_Document extends Salore_Solr_Model_Resource_Abstract {
    protected function _construct() {
        $this->_init('solr/document');
    }
    public function getTopCities() {
        $queryUrl = $this->getConnection()->getQueryUrl();
        $queryUrl .= '/?q=*:*&facet=true&facet.field=city_facet&wt=json&json.nl=map&facet.mincount=1&rows=-1';
        $jsonData = $this->getConnection()->request($queryUrl);
        return $jsonData;
    }
    public function getCategories() {
        $queryUrl = $this->getConnection()->getQueryUrl();
        $queryUrl .= '/?q=*:*&facet=true&facet.field=category_facet&fl=id&rows=-1&wt=json';
        $jsonData = $this->getConnection()->request($queryUrl);
        return $jsonData;
    }
}