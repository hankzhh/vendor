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
class Salore_Solr_Model_Document extends Salore_Solr_Model_Abstract {
    protected function _construct() {
        $this->_init('solr/document');
    }
    public function getTopCities() {
        return $this->_getResource()->getTopCities();
    }
    public function getCategories() {
        return $this->_getResource()->getCategories();
    }
    public function getStandardFilterQuery() {
        $params = Mage::helper('salon')->getParams();
        if (isset($params['fq']) && is_array($params['fq'])) {
            $filterQuery = array();
            foreach ($params['fq'] as $key=>$values) {
                if (!empty($key) && !is_array($values) && !empty($values)) {
                    if ($key == 'category_id') {
                        $filterQuery[$key] = array($values);
                    }else {
                        $filterQuery[$key.'_facet'] = array($values);
                    }
                }else if(!empty($key) && is_array($values)) {
                    if ($key == 'category_id') {
                        $filterQuery[$key] = $values;
                    }
                    else {
                        $filterQuery[$key.'_facet'] = $values;
                    }
                }
            }
            return $filterQuery;
        }
        return array();
    }
    protected function prepareFilterQuery() {
        $filterQuery = Mage::getSingleton('core/session')->getSolrFilterQuery();
        $standardFilterQuery = array();
        if ($standardFilterQuery = $this->getStandardFilterQuery()) {
            $filterQuery = $this->getStandardFilterQuery();
        }
    
        if (!is_array($filterQuery) || !isset($filterQuery)) {
            $filterQuery = array();
        }
    
        $filterQueryArray = array();
        $rangeFields = $this->rangeFields;
    
        foreach($filterQuery as $key=>$filterItem){
            if(count($filterItem) > 0){
                $query = '';
                foreach($filterItem as $value){
                    if ($key == 'price_decimal') {
                        $query .= $this->priceFieldName.':['.urlencode(trim($value).'.99999').']+OR+';
                    }else if($key == 'price'){
                        $query .= $this->priceFieldName.':['.urlencode(trim($value).'.99999').']+OR+';
                    }else{
                        $face_key = substr($key, 0, strrpos($key, '_'));
                        if ($key == 'price_facet') {
                            $query .= $this->priceFieldName.':['.urlencode(trim($value).'.99999').']+OR+';
                        }
                        else if(array_key_exists($face_key, $rangeFields))
                        {
                            $query .= $rangeFields[$face_key].':['.urlencode(trim(addslashes($value))).']+OR+';
                        }else{
                            $query .= $key.':%22'.urlencode(trim(addslashes($value))).'%22+OR+';
                        }
                    }
                }
    
                $query = trim($query, '+OR+');
    
                $filterQueryArray[] = $query;
            }
        }
    
        $filterQueryString = '';
    
        if(count($filterQueryArray) > 0) {
            if(count($filterQueryArray) < 2) {
                $filterQueryString .= $filterQueryArray[0];
            }else{
                $filterQueryString .= '%28'.@implode('%29+AND+%28', $filterQueryArray).'%29';
            }
        }
    
        $this->filterQuery = $filterQueryString;
    }
}