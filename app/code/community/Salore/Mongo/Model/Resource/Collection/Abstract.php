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

class Salore_Mongo_Model_Resource_Collection_Abstract extends Varien_Data_Collection
{
    /**
     * DB connection
     *
     * @var Salore_Mongo_Adapter_Abstract
     */
    protected $_conn;
    const ASC    =  1;
    const DESC   = -1;
    protected $filterQuery = array();
    protected $limit =0;

    public function __construct($conn=null) {
        parent::__construct();

        if (!is_null($conn)) {
            $this->setConnection($conn);
        }
    }
    public function getLimit() {
        return $this->limit;
    }
    public function setLimit($limit) {
        $this->limit = $limit;
    }
    
    /**
     * Set select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orders[$field] = $direction === 'ASC' ? self::ASC : self::DESC;
        return $this;
    }
    
    /**
     * Set database connection adapter
     *
     * @param Salore_Mongo_Adapter_Abstract $conn
     * @return Varien_Data_Collection
     */
    public function setConnection($conn) {
        if (!$conn instanceof Salore_Mongo_Adapter_Abstract) {
            throw new Zend_Exception('dbModel read resource does not implement Salore_Mongo_Adapter_Abstract');
        }

        $this->_conn = $conn;
        return $this;
    }

    /**
     * Retrieve connection object
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function getMongoAdapter() {
        return $this->_conn;
    }

    public function addFilterQuery($filter) {
        $this->filterQuery = $filter;
    }

    public function truncate() {
        $collectionName = $this->_conn->getCollectionName();
        $collection = $this->getMongoAdapter()->getCollection($collectionName);
        $collection->remove();
    }
    
    public function findOne($filterQuery = array()) {
        if ($this->isLoaded()) {
            if (isset($this->_items[0])) {
                return $this->_items[0];
            }
            return false;
        }
    
        $collectionName = $this->_conn->getCollectionName();
        $collection = $this->getMongoAdapter()->getCollection($collectionName);
    
        if ( is_array($filterQuery) && !empty($filterQuery) ) {
            $mongoCollection = $collection->getCollection();
            $document = $mongoCollection->findOne($filterQuery);
            $items[0] = $this->getNewEmptyItem()->setData($document);
                
            $this->_items = $items;
                
            $this->_setIsLoaded();
                
            return $this->_items[0];
        }
        return false;
    }

    public function load($printQuery = false, $logQuery = false) {
        if ($this->isLoaded()) {
            return $this;
        }

        $collectionName = $this->_conn->getCollectionName();
        $collection = $this->getMongoAdapter()->getCollection($collectionName);

        $documents = array();

        $filterQuery = $this->filterQuery;

        $items = array();
        if ( is_array($filterQuery) && !empty($filterQuery) ) {
            $mongoCollection = $collection->getCollection();
            $cursor = $mongoCollection->find($filterQuery)->sort($this->_orders);
            $documents = iterator_to_array( $cursor );
        }
        else {
            $mongoCollection = $collection->getCollection();
            $cursor = $mongoCollection->find()->sort($this->_orders);
            $documents = iterator_to_array( $cursor );
        }

        if (isset($filterQuery['entity_id'])) {
            $orders = $filterQuery['entity_id']['$in'];

            $orders = array_flip($orders);

            $index = 1;
            foreach ($documents as $v) {

                $object = $this->getNewEmptyItem()
                ->setData($v);

                $items[$orders[$object->getId()]] = $object;
                $index++;
            }

            ksort($items);
        }
        else {
            foreach ($documents as $v) {

                $object = $this->getNewEmptyItem()
                ->setData($v);

                $items[] = $object;
            }
        }
        $this->_items = $items;
        $this->_setIsLoaded();
        return $this;
    }
}