<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Solr to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Solr
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Solr_Model_Resource_Collection_Abstract extends Varien_Data_Collection {
    /**
     * DB connection
     *
     * @var Salore_Solr_Adapter_Abstract
     */
    protected $_conn;

    protected $filterQuery = array();
    
    protected $facetFields = array();
    
    protected $facetData = array();
    
    protected $query = '*:*';

    public function __construct($conn=null) {
        parent::__construct();

        if (!is_null($conn)) {
            $this->setConnection($conn);
        }
    }

    /**
     * Set database connection adapter
     *
     * @param Salore_Solr_Adapter_Abstract $conn
     * @return Varien_Data_Collection
     */
    public function setConnection($conn) {
        if (!$conn instanceof Salore_Solr_Adapter_Abstract) {
            throw new Zend_Exception('dbModel read resource does not implement Salore_Solr_Adapter_Abstract');
        }

        $this->_conn = $conn;
        return $this;
    }

    /**
     * Retrieve solr connection object
     *
     * @return Salore_Solr_Adapter_Solr
     */
    public function getAdapter() {
        return $this->_conn->getConnection();
    }

    public function addFilterQuery($key, $value) {
        $this->filterQuery[$key] = $value;
    }
    
    public function addFacetField($field) {
        if (!in_array($field, $this->facetFields)) {
            $this->facetFields[] = $field;
        }
    }
    
    public function getFacetData() {
        return $this->facetData;
    }
    
    public function getFacetFieldString() {
        if (!empty($this->facetFields)) {
            return 'facet.field='.@implode('&facet.field=', $this->facetFields);
        }
        return false;
    }
    
    public function prepareQuery()  {
        if (!isset($this->filterQuery['q']) || empty($this->filterQuery['q'])) {
            $this->addFilterQuery('q', '*:*');
        }
        
        $this->addFilterQuery('wt', 'json');
        $this->addFilterQuery('json.nl', 'map');
    }
    
    public function prepareFacet(&$queryUrl) {
        //Prepare facets
        if (!empty($this->facetFields)) {
            $this->addFilterQuery('facet', 'true');
            $facetString = '';
            foreach ($this->facetFields as $field) {
                $facetString .= '&facet.field='.$field;
            }
            $facetString = trim($facetString, '&');
            if (!empty($facetString)) {
                $queryUrl .= '?'.$facetString;
            }
        }
    }

    public function load($printQuery = false, $logQuery = false) {
        if ($this->isLoaded()) {
            return $this;
        }
        
        $this->prepareQuery();
        $queryUrl = $this->getAdapter()->getQueryUrl();
        $this->prepareFacet($queryUrl);
        
        $response = $this->getAdapter()->request($queryUrl, $this->filterQuery);
        
        if (isset($response['facet_counts']['facet_fields']) && !empty($response['facet_counts']['facet_fields'])) {
            $this->facetData = $response['facet_counts']['facet_fields'];
        }

        if ( isset($response['response']['numFound']) && $response['response']['numFound'] > 0 && isset($response['response']['docs']) )  {
            foreach ($response['response']['docs'] as $document) {
                $object = $this->getNewEmptyItem()->setData($document);
                $this->addItem($object);
            }
        }

        $this->_setIsLoaded();
        
        return $this;
    }
}