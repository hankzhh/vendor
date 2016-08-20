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

class Salore_Mongo_Adapter_Collection
{
    /**
     * Salore_Mongo_Adapter_Abstract object.
     *
     * @var Salore_Mongo_Adapter_Abstract
     */
    protected $_adapter;

    protected $_collectionName;

    protected $_filterQuery = array();

    /**
     * Class constructor
     *
     * @param Salore_Mongo_Adapter_Abstract $adapter
     */
    public function __construct(Salore_Mongo_Adapter_Abstract $adapter, $collectionName)
    {
        $this->_adapter = $adapter;
        $this->_collectionName = $collectionName;
    }

    public function getCollectionName()
    {
        return $this->_collectionName;
    }

    public function getCollection()
    {
        $databaseName = $this->_adapter->getDatabaseName();

        $collectionName = $this->getCollectionName();

        $mongoDb = $this->_adapter->getConnection();
        $collection = $mongoDb->selectCollection($databaseName, $collectionName);

        return $collection;
    }

    public function remove()
    {
        $collection = $this->getCollection();
        $collection->remove();
    }

    public function addFieldToFilter($filters)
    {
        if (!is_array($filters)){
            throw new Exception(Mage::helper('salore')->__('Filter param must be an array'));
        }
        $this->_filterQuery = array_merge($this->_filterQuery, $filters);
    }

    public function getFilterQuery()
    {
        return $this->_filterQuery;
    }


    public function findAll()
    {
        $collection = $this->getCollection();

        $cursor = $collection->find();

        $array = iterator_to_array( $cursor );

        return $array;
    }
}