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
class Salore_Solr_Model_Resource_Abstract extends Salore_Solr_Adapter_Abstract {
    protected $_collectionName;

    /**
     * Resource initialization
     */
    protected function _construct() {

    }
    /**
     * Retrieve connection for read data
    */
    protected function _getReadAdapter(){
        return Mage::getSingleton('core/resource')->getConnection('solr_read');
    }

    /**
     * Retrieve connection for write data
    */
    protected function _getWriteAdapter(){
        return Mage::getSingleton('core/resource')->getConnection('solr_write');
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
        /*
        if (!$alias && !empty($this->_collectionName))
        {
            return $this->_collectionName;
        }
        return Mage::getSingleton('core/resource')->getTableName($alias);
        */
    }


    /**
     * get MongoBridge Collection object
     * @return SolrBridge_Core_Adapter_Collection
     */
    public function getCollection( $collectionName  = null ) {
        /*
        if (!$collectionName) {
            $collectionName = $this->getCollectionName();
        }
        return $this->getConnection()->getCollection( $collectionName );
        */
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
        $data = $object->toArray();
        
        if ( $this->getConnection()->ping() ) {
            $this->getConnection()->saveDocument($data);
             
            $this->getConnection()->commit();
        }

        return $this;
    }
    
    /**
     * Delete the object
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function delete($object, $where = '') {
        $data = $object->toArray();
         
        if ( isset($data['entity_id']) && $this->getConnection()->ping()) {
            $this->getConnection()->deleteDocument($data['entity_id']);
         
            $this->getConnection()->commit();
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

        if ( !empty($value) && $this->getConnection()->ping() ) {
            $data = $this->getConnection()->loadDocument($value);
            
            if ($data) {
                $object->setData($data);
            }
        }
        
        $this->_afterLoad($object);

        return $this;
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

    public function addCommitCallback(){}

    public function commit() {
        $this->getConnection()->commit();
    }
    public function truncate() {
        if ( $this->getConnection()->ping() ) {
            $this->getConnection()->truncate();
        }
    }
}
