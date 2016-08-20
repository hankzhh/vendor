<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Search_Filter extends Mage_Core_Block_Template {
    protected $solrModel = null;
    protected $filterQuery = array();
    public function __construct() {
        $this->solrModel = Mage::getModel('solr/document');
    }
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->addItem('skin_css', 'css/search/filter.css');
        return parent::_prepareLayout();
    }
    protected function getFilterQuery() {
        if (!$this->filterQuery) {
            $this->filterQuery = $this->solrModel->getStandardFilterQuery();
        }
        return $this->filterQuery;
    }
    public function getFilterData() {
        return Mage::registry('salore_solr_search_result_facet_data');
    }
    
    public function getFacetLabel($facetKey) {
        $end = strrpos($facetKey, '_');
        return substr($facetKey, 0, $end);
    }
    public function getFacesUrl($params=array()) {
    
        $paramss = $this->getRequest()->getParams();
        foreach ($params as $key=>$item) {
            $key = trim($key);
    
            if( in_array($key, array('min', 'max')) ) {
                if (isset($paramss[$key])) {
                    unset($paramss[$key]);
                    $finalParams = array_merge_recursive($params, $paramss);
                }
            }
    
            if ($key == 'fq') {
                foreach ($item as $k=>$v) {
                    if (isset($paramss[$key][$k]) && $v == $paramss[$key][$k]){
    
                    }else{
                        if( $k == 'price' && isset($paramss[$key][$k]) || $k == 'category' || $k == 'category_id'){
                            unset($paramss[$key][$k]);
                        }
                        $finalParams = array_merge_recursive($params, $paramss);
                    }
                }
            }
        }
    
        if (isset($finalParams['p'])) {
            $finalParams['p'] = 1;
        }
    
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        if (isset($finalParams)) {
            if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
                if (isset($finalParams['q'])) {
                    unset($finalParams['q']);
                }
                if (isset($finalParams['id'])) {
                    unset($finalParams['id']);
                }
            }
    
            $urlParams['_query']    = $finalParams;
        }
        return $this->getUrl('*/*/*', $urlParams);
    }
    public function isSelectedFacetActive() {
        $filterQuery = $this->solrModel->getStandardFilterQuery();
    
        $this->filterQuery = $filterQuery;
    
        $isFacetActived = false;
        foreach($filterQuery as $key=>$value) {
            if(is_array($value) && count($value) > 0) {
                $isFacetActived = true;
            }
        }
        return $isFacetActived;
    }
    public function facetFormat($text) {
        $returnText = $text;
        if (strrpos($text, '_._._') > -1) {
            $returnText = str_replace('_._._', '/', $text);
        }
        return $this->htmlEscape($returnText);
    }
    public function getRemoveAllUrl(){
        $_solrDataArray = $this->getSolrData();
    
        $paramss = $this->getRequest()->getParams();
    
        if(!isset($paramss['q'])){
            if( isset($_solrDataArray['responseHeader']['params']['q']) && !empty($_solrDataArray['responseHeader']['params']['q']) ) {
                if (isset($paramss['q']) && $paramss['q'] != $_solrDataArray['responseHeader']['params']['q']) {
                    $paramss['q'] = $_solrDataArray['responseHeader']['params']['q'];
                }
            }
        }
    
        $finalParams = array();
        if(isset($paramss['q'])) {
            $finalParams['q'] = $paramss['q'];
        }
    
        $urlParams = array();
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
    
        if (isset($finalParams)) {
    
            if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
                if (isset($finalParams['q'])) {
                    unset($finalParams['q']);
                }
                if (isset($finalParams['id'])) {
                    unset($finalParams['id']);
                }
            }
    
            $urlParams['_query']    = $finalParams;
        }
    
        return Mage::getUrl('*/*', $urlParams);
    }
    public function getRemoveFacesUrl($key,$value) {
        $paramss = $this->getRequest()->getParams();
    
        $finalParams = $paramss;
    
        if (is_array($key) && is_array($value) && count($key) == count($value)){
            $index = 0;
            foreach ($key as $item)
            {
                if (isset($finalParams['fq'][$item]) && !is_array($finalParams['fq'][$item]) && !empty($finalParams['fq'][$item])) {
                    unset($finalParams['fq'][$item]);
                    if ($item == 'category' && isset($finalParams['fq'][$item.'_id'])) {
                        unset($finalParams['fq'][$item.'_id']);
                    }
                }else if (isset($finalParams['fq'][$item]) && is_array($finalParams['fq'][$item]) && count($finalParams['fq'][$item]) > 0) {
                    foreach ($finalParams['fq'][$item] as $k=>$v) {
                        if ($v == $value) {
                            unset($finalParams['fq'][$item][$k]);
                            if ($item == 'category' && isset($finalParams['fq'][$item.'_id']) && isset($finalParams['fq'][$item.'_id'][$k])) {
                                unset($finalParams['fq'][$item.'_id'][$k]);
                            }
                        }
                    }
                }
    
                $index++;
            }
        }else{
            if (isset($finalParams['fq'][$key]) && !is_array($finalParams['fq'][$key]) && !empty($finalParams['fq'][$key])) {
                unset($finalParams['fq'][$key]);
                if ($key == 'category' && isset($finalParams['fq'][$key.'_id'])) {
                    unset($finalParams['fq'][$key.'_id']);
                }
            }else if (isset($finalParams['fq'][$key]) && is_array($finalParams['fq'][$key]) && count($finalParams['fq'][$key]) > 0) {
                foreach ($finalParams['fq'][$key] as $k=>$v) {
                    if ($v == $value) {
                        unset($finalParams['fq'][$key][$k]);
                        if ($key == 'category' && isset($finalParams['fq'][$key.'_id']) && isset($finalParams['fq'][$key.'_id'][$k])) {
                            unset($finalParams['fq'][$key.'_id'][$k]);
                        }
                    }
                }
            }
        }
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        if (isset($finalParams)) {
    
            if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
                if (isset($finalParams['q'])) {
                    unset($finalParams['q']);
                }
                if (isset($finalParams['id'])) {
                    unset($finalParams['id']);
                }
            }
            $urlParams['_query']    = $finalParams;
        }
        return Mage::getUrl('*/*/*', $urlParams);
    }
}