<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class Salore_Salon_Block_Slider_Category extends Mage_Core_Block_Template {
    /**
     * get all categorydata from solr
     * @return json
     */
    public function getCategories() {
        $data = Mage::getModel('solr/document')->getCategories();
        return $data;
    }
    /**
     * get path  image city by city
     * @param string $cityName
     * @return string
     */
    public function getSrcByCategory($categoryName) {
        return Mage::getModel('salon/category')->load($categoryName, 'category_name')->getData('img_path_resize');
    }
    public function getPageTotalSalon()
    {
        return Salore_Salon_Block_Salon_Index::$pageTotal;
    }
}