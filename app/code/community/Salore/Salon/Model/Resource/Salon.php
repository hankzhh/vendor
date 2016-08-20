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
class Salore_Salon_Model_Resource_Salon extends Salore_Mongo_Model_Resource_Abstract {
    protected $_collectionName;

    public function __construct() {
        $this->_collectionName = $this->getCollectionName('salon/salon');
    }
    /**
     * Save object
     *
     * @param Cm_Mongo_Model_Abstract|Mage_Core_Model_Abstract $object
     * @throws Mage_Core_Exception
     * @throws MongoCursorException
     * @return  Cm_Mongo_Model_Resource_Abstract
     */
    public function save( Mage_Core_Model_Abstract $object ) {
        $object->setData( $this->getIdFieldName(), $object->getData('entity_id') );
    
        if (!$object->getData('entity_id')) {
            $object->setData('entity_id', uniqid());
        }
    
        $collection = $this->getCollection()->getCollection();
    
        $data = $object->getData();
    
        $data['unique_id'] = $object->getData('entity_id');
        $data['_id'] = $data['unique_id'];
    
        try {
            $collection->update( array('unique_id' => $data['unique_id'] ), $data, array('upsert' => true, 'w' => true) );
        }catch (Exception $e) {
            echo $e->getMessage();
            return ;
        }
        return $this;
    }
}