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
class Salore_Salon_Model_Resource_Reservation extends Salore_Salon_Model_Resource_Abstract {
    protected $_collectionName;
    
    protected $_salonUrl = null;

    public function __construct()  {
        if($this->getCollectionName('salon/reservation'))
        {
            $this->_collectionName = $this->getCollectionName('salon/reservation');
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
        $this->_collectionName = $this->getCollectionName('salon/reservation');
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