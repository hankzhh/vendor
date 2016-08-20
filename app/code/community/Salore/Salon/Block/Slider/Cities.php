<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class Salore_Salon_Block_Slider_Cities extends Mage_Core_Block_Template {
    /**
     * get all citydata from solr
     * @return json
     */
    public function getTopCities() {
        $data = Mage::getModel('solr/document')->getTopCities();
        return $data;
    }
    /**
     * get path  image city by city
     * @param string $cityName
     * @return string
     */
    public function getSrcByCity($cityName) {
        return Mage::getModel('salon/city')->load($cityName, 'city_name')->getData('img_path_resize');
    }
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->addItem( 'skin_js', 'js/slider/jssor.js')
                                            ->addItem('skin_css', 'css/slider.css')
                                            ->addItem( 'skin_js', 'js/slider/jssor.slider.min.js' );
        return parent:: _prepareLayout();
    }
}