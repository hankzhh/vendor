<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Mongo to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */

class Salore_Mongo_Model_Resource_Abstract extends Salore_Mongo_Adapter_Abstract
{
    protected $_collectionName;

    /**
     * Resource initialization
     */
    protected function _construct() {

    }
    /**
     * Retrieve connection for read data
    */
    protected function _getReadAdapter() {
        return Mage::getSingleton('core/resource')->getConnection('mongo_read');
    }

    /**
     * Retrieve connection for write data
    */
    protected function _getWriteAdapter() {
        return Mage::getSingleton('core/resource')->getConnection('mongo_write');
    }

    public function getConnection() {
        return $this->_getReadAdapter();
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
        return Mage::getSingleton('core/resource')->getTableName($alias);
    }


    /**
     * get SolrBridge Collection object
     * @return SolrBridge_Mongo_Adapter_Collection
     */
    public function getCollection( $collectionName  = null ) {
        if (!$collectionName) {
            $collectionName = $this->getCollectionName();
        }

        return $this->getConnection()->getCollection( $collectionName );
    }

    /**
     * In Mongo, there is always an _id column
     *
     * @return string
     */
    public function getIdFieldName() {
        return '_id';
    }

    public function getDatabaseName() {
        return $this->_getWriteAdapter()->getDatabaseName();
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
        //Get collection / table name
        $object->setData( $this->getIdFieldName(), $object->getData('entity_id') );
        
        if (!$object->getData('store_id')) {
            $object->setData('store_id', Mage::app()->getStore()->getId());
        }
        
        if (!$object->getData('entity_id')) {
            $object->setData('entity_id', uniqid());
        }

        $collection = $this->getCollection()->getCollection();

        $data = $object->getData();
        if ( isset($data['_cache_editable_attributes']) ) {
            unset($data['_cache_editable_attributes']);
        }

        if (isset($data['stock_item'])) {
            unset($data['stock_item']);
        }
        
        $data['unique_id'] = $object->getData('entity_id');
        $data['_id'] = $data['unique_id'];

        try {
            $collection->update( array('unique_id' => $data['unique_id'] ), $data, array('upsert' => true, 'w' => true) );
        }catch (Exception $e){
            echo $e->getMessage();
            print_r(array_keys($data));
            return ;
        }
        return $this;
    }

    /**
     * Load an object
     *
     * @param Mage_Core_Model_Abstract $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = 'entity_id') {
        if (is_null($field)) {
            $field = $this->getIdFieldName();
        }

        $mongoCollection = $this->getCollection()->getCollection();

        if ($mongoCollection && !is_null($value)) {
            $data = $mongoCollection->findOne( array($field => (string)$value) );

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;
    }
    public function delete($object, $where = '') {
        $mongoCollection = $this->getCollection()->getCollection();
        $mongoCollection->remove(array('_id' =>  $object->getId()));
        return $this;
    }
    public function truncate($filter = array()) {
        $collection = $this->getCollection()->getCollection();
        $collection->remove($filter);
    }
    /**
     * After load
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function afterLoad(Mage_Core_Model_Abstract $object) {
        $this->_afterLoad($object);
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        return $this;
    }

    public function addCommitCallback() {

    }

    public function commit() {

    }
}
