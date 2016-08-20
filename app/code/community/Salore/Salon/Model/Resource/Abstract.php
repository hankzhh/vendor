<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Model_Resource_Abstract extends Salore_Mongo_Model_Resource_Abstract {
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
        $salon = Mage::registry('currentsalon');
        if ( $salon && $salon->getData('salon_url') ) {
            return $salon->getData('salon_url') .'_'. Mage::getSingleton('core/resource')->getTableName($alias);
        }
        
        return Mage::getSingleton('core/resource')->getTableName($alias);
    }
}