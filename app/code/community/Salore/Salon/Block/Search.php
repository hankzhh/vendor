<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Search extends Mage_Core_Block_Template {
    public $pageNum = null;
    public $limit = null;
    protected $collection = null;
    protected $solrModel = null;
    protected $filterQuery = array();
    
    public function __construct() {
        parent::__construct();
        $this->solrModel = Mage::getModel('solr/document');
        $collection = $this->getSalonResultCollection();
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = ($this->getRequest()->getParam('mode') == 'list') ? 5 : 12; 
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if('grid' === $this->getRequest()->getParam('mode')) {
            $this->getLayout()->getBlock('head')->addItem('skin_css', 'css/index.css');
        }
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'salon.search.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('salon_search_pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    /**
     * get result salon from solr
     */
    public function getSalonResultCollection() {
        if ($this->collection !== null) {
            return $this->collection;
        }
        $queryText = Mage::app()->getRequest()->getParam('q');
        $collection = Mage::getModel('solr/document')->getCollection();
        $collection->addFilterQuery('q', $queryText);
        $filterString = $this->getFilterString($this->getFilterQuery());
        if ($filterString) {
            $collection->addFilterQuery('fq', $filterString);
        }
        $collection->addFacetField('category_facet');
        $collection->addFacetField('city_facet');
        $collection->addFacetField('state_facet');
        $collection->addFilterQuery('facet.limit', '1');
        $collection->load();
        Mage::register('salore_solr_search_result_facet_data', $collection->getFacetData());
        $this->collection = $collection;
        return $this->collection;
    }
    
    public function getSalonResultArr() {
        $salonArr = array();
        $collection = $this->getSalonResultCollection();
        foreach($collection as $salon) {
            $salonArr[] = $salon->getData();
        }
        return $salonArr;
    }
    /**
     * get list salon
     * @return array
     */
    public function getSalonByList() {
        $dataReturn = array();
        $i = 1;
        $collection = $this->getCollection();
        $size = $collection->getSize();
        $pages = ceil($size / $this->limit);
        $offset = ($this->pageNum - 1)  * $this->limit;
        $start = $offset + 1;
        foreach ($collection as $row) {
            if($start <= $i) {
                if ($i <= min(($offset + $this->limit), $size)) {
                    $dataReturn[] = $row->getData();
                }
            }
            $i++;
        }
        return $dataReturn;
    }
    /**
     * get all salondata from mongodb by id
     * @param unknown $id
     * @return array|NULL
     */
    public function getSalonById($id) {
        $salonMongo = Mage::getModel('salon/salon')->load($id, 'entity_id');
        if($salonMongo->getEntityId())
        {
            return $salonMongo;
        }
        return null;
    }
    /**
     *
     * @return multitype:
     */
    protected function getFilterQuery() {
        if (!$this->filterQuery) {
            $this->filterQuery = $this->solrModel->getStandardFilterQuery();
        }
        return $this->filterQuery;
    }
    /**
     *
     * @param unknown $paramsArr
     * @return string
     */
    public function getFilterString($paramsArr) {
        $filterString = null;
        if(isset($paramsArr) && !empty($paramsArr)) {
            foreach ($paramsArr as $key=>$valArr) {
                if(is_array($valArr) && !empty($valArr)) {
                    foreach ($valArr as $val) {
                        $filterString.= '(' . $key . ':' . urldecode($val) . ')+AND+';
                    }
                }
            }
            if ($filterString) {
                $filterString = trim($filterString, '+AND+');
            }
        }
        return $filterString;
    }
}