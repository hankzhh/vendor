<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Model_Salon extends Salore_Mongo_Model_Abstract {
    protected $_eventPrefix = 'mongo_salon';
    protected function _construct() {
        $this->_init('salon/salon');
    }
    public function checkSalonUrlExists($salonUrl) {
        $collection = $this->getCollection();
        $filterQuery = array( 'salon_url' => trim($salonUrl) );
        $salon = $collection->findOne( $filterQuery );
        if ($salon->getSalonUrl()) {
            return $salon;
        }
        return false;
    }
    public function checkSalonEmailExists($email) {
        $collection = $this->getCollection();
        $filterQuery = array( 'email' => trim($email) );
        $salon = $collection->findOne( $filterQuery );
        if ($salon->getEmail()) {
            return $salon;
        }
        return false;
    }
    
    /**
     * Find all mongo collections belong to $salon
     * @param unknown $salon
     */
    public function dropSalonCollections( $salon ) {
        //Define salon key
        $salonKey = $salon->getData('salon_url');
        $salonCollections = array();
        if ( !empty($salonKey) ) {
            $adapter = Mage::getSingleton('core/resource')->getConnection('mongo_read');
            $mongoDb = $adapter->getDatabase();
            $databaseName = $adapter->getDatabaseName();
            $collectionNames = $mongoDb->getCollectionNames();
            foreach ($collectionNames as $collectionName) {
                if ( strpos($collectionName, $salonKey.'_') !== false ) {
                    $collection = $adapter->getConnection()->selectCollection($databaseName, $collectionName);
                    $collection->drop();
                    $salonCollections[] = $collectionName;
                }
            }
        }
        return $salonCollections;
    }
}