<?php
class Salore_Salon_Model_Resource_Customer extends Salore_Salon_Model_Resource_Abstract {
    protected $_collectionName;
    
    protected $_salonUrl = null;

    public function __construct() {
        if($this->getCollectionName('salon/customer')) {
            $this->_collectionName = $this->getCollectionName('salon/customer');
        }
    }
    /**
     * Retreive table name
     *
     * @param string $alias
     * @return string
     */
    public function getCollectionName($alias = null) {
        if (!$alias && !empty($this->_collectionName)) {
            return $this->_collectionName;
        }
        $salonUrl = $this->getSalonUrl();
        if ( $salonUrl ) {
                return $salonUrl .'_'. Mage::getSingleton('core/resource')->getTableName($alias);
        }
        return null;
    }
    public function setSalonUrl($salonUrl) {
        $this->_salonUrl = $salonUrl;
        $this->_collectionName = $this->getCollectionName('salon/customer');
    }
    public function getSalonUrl() {
        if($this->_salonUrl !== null) {
            return $this->_salonUrl;
        }
        $salon = Mage::registry('currentsalon');
        if ( $salon && $salon->getData('salon_url') ) {
            $this->_salonUrl = $salon->getData('salon_url');
        }
        return $this->_salonUrl;
    }
}