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
class Salore_Solr_Model_Observer {
    /**
     * Save data to solr when salon approved
     * @param unknown $observer
     */
    public function salonApprovedAfter($observer) {
        $_salon = $observer->getEvent()->getSalon();
        
        $store = Mage::helper('solr')->getDefaultStore();
        
        $this->updateDataToSolr($_salon);
    }
    /**
     * Delete solr document when salon got deleted or unapproved
     * @param unknown $observer
     */
    public function salonDeleteAfter($observer) {
        $_salon = $observer->getEvent()->getSalon();
        $store = Mage::helper('solr')->getDefaultStore();
        $solrDocumentId = Mage::helper('solr')->getSalonSolrDocumentId($_salon);
        $solrDocument = Mage::getModel('solr/document')->load($solrDocumentId);
        $solrDocument->delete();
    }
    public function salonSaveAfter($observer) {
        $_salon = $observer->getEvent()->getDataObject();
        $this->updateDataToSolr($_salon);
    }
    public function updateDataToSolr($_salon) {
        //Prepare/save solr document
        $solrDocument = Mage::getModel('solr/document');
        
        $solrDocumentId = Mage::helper('solr')->getSalonSolrDocumentId($_salon);
            
        $solrDocument->setData('id', $solrDocumentId);
        $solrDocument->setData('entity_id', $_salon->getEntityId());
        $solrDocument->setData('doctype', 'salon');
            
        $solrDocument->setData('name_varchar', $_salon->getSalonName());
        
        $solrDocument->setData('lat_varchar', $_salon->getLat());
        $solrDocument->setData('lng_varchar', $_salon->getLng());
            
        $textSearch = array();
        $textSearch[] = $_salon->getSalonName();
        $salonAddress = $_salon->getAddress().' '.$_salon->getCity().' '.$_salon->getRegion().' '.$_salon->getPostcode();
        $textSearch[] = $salonAddress;
        $textSearch[] = $_salon->getCategory();
        
        $solrDocument->setData('address_varchar', $salonAddress);
        $solrDocument->setData('url_varchar', $_salon->getSalonUrl());
        $solrDocument->setData('fullname_varchar', $_salon->getFirstname(). ' ' . $_salon->getLastname());
        $solrDocument->setData('city_facet', $_salon->getCity());
        $solrDocument->setData('category_facet', Mage::helper('solr')->getCategoryNameById($_salon->getCategory()));
        $solrDocument->setData('state_facet', $_salon->getRegion());
        $solrDocument->setData('description_varchar', $_salon->getSalonDescription());
        $solrDocument->setData('logo_varchar', $_salon->getLogo());
        $solrDocument->setData('img_represent_varchar', $_salon->getImgRepresent());
        
        $solrDocument->setData('textSearchStandard', $textSearch);
        $solrDocument->setData('textSearch', $textSearch);
        $solrDocument->setData('textSearchText', $textSearch);
        $solrDocument->setData('textSearchGeneral', $textSearch);
            
        $solrDocument->save();
    }
}