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

class Salore_Mongo_Adapter_Mongo extends Salore_Mongo_Adapter_Abstract
{

    protected $_config = array();

    protected $_connection = null;
    
    protected $_db = null;

    public function __construct($config)
    {
        $this->_config = $config;

        // Setup connection options merged over the defaults and store the connection
        //$options = array('connect' => TRUE, 'password' => 'orange', 'username' => 'apple', 'db' => 'solrbridge');
        $options = array('connect' => TRUE);

        if ( isset($config['options']) )
        {
            $options = array_merge($options, $config['options']);
        }

        $server = isset($config['server']) ? $config['server'] : "mongodb://" . ini_get('mongo.default_host') . ":" . ini_get('mongo.default_port');

        try {
            $this->_connection = new MongoClient($server, $options);
        }catch (Exception $e)
        {
            $this->_connection = new Mongo($server, $options);
        }

        $this->_db = $config['database'];
    }

    public function getConnection()
    {
        if (is_null($this->_db))
        {
            $this->_db = $config['database'];
        }
        $this->_connection->selectDB($this->_db);
        return $this->_connection;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function getDatabaseName()
    {
        return $this->_db;
    }
    
    public function getDatabase()
    {
        return $this->_connection->selectDB($this->_db);
    }

    /**
     * get Mongo query object
     *
     * @return Salore_Mongo_Adapter_Query
     */
    public function getCollection($collectionName)
    {
        return new Salore_Mongo_Adapter_Collection($this, $collectionName);
    }
}