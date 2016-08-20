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
class Salore_Solr_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getDefaultStore() {
        return Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStore();
    }
    
    public function getSalonSolrDocumentId($salon) {
        return $this->getDefaultStore()->getId().'SALON'.$salon->getEntityId();
    }
    
    public function getAutocompleteUrl() {
        $url = trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '/').'/sb.php';
        return $url;
    }
    
    public function getQueryFields() {
        return 'textSearchStandard^80 textSearch^40 textSearchText^10 textSearchGeneral^1';
    }
    public function getCategoryNameById($categoryId)
    {
        return Mage::getModel('salon/category')->load($categoryId, 'entity_id')->getCategoryName();
    }
}