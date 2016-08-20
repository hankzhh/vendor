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
class Salore_Salon_Model_Resource_Favourite extends Salore_Salon_Model_Resource_Abstract {
    protected $_collectionName;
    
    protected $_customerId = null;

    public function __construct() {
        if($this->getCollectionName('salon/favourite')) {
            $this->_collectionName = $this->getCollectionName('salon/favourite');
        }
    }
    /**
     * Retreive table name
     *
     * @param string $alias
     * @return string
     */
    public function getCollectionName($alias = null) {
        if (!$alias && !empty($this->_collectionName))
        {
            return $this->_collectionName;
        }
        $customerId = $this->getCustomerId();
        if ( $customerId )
        {
                return Mage::getSingleton('core/resource')->getTableName($alias) . '_customer_' . $customerId;
        }
        return null;
    }
    public function setCustomerId($customerId) {
        $this->_customerId = $customerId;
        $this->_collectionName = $this->getCollectionName('salon/favourite');
    }
    public function getCustomerId() {
        if($this->_customerId !== null) {
            return $this->_customerId;
        }
        return $this->_customerId;
    }
}