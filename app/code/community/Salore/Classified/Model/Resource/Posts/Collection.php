<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Classified to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Model_Resource_Posts_Collection extends Salore_Mongo_Model_Resource_Collection_Abstract {
    
    const ASC    =  1;
    const DESC   = -1;
    const SORT_ORDER_ASC = 'ASC';
    const SORT_ORDER_DESC = 'DESC';
    public $sort = array();
    
    public function setSort ($field, $dir = 'asc') {
    
        $direction =  ( strtoupper( $dir ) == self::SORT_ORDER_ASC ) ? self::ASC : self::DESC;
        $this->sort[$field] = (int)$direction;
    }
    
    public function getSort(){
        
        if( empty( $this->sort ) ) {
            return array('entity_id');
        }
        return $this->sort;
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
            
            $dataSort = $this->getSort();
            
            $cursor = $mongoCollection->find($filterQuery)->sort( $dataSort );
            
            $documents = iterator_to_array( $cursor );
        }
        else {
            
            $mongoCollection = $collection->getCollection();
            
            $dataSort = $this->getSort();
            
            $cursor = $mongoCollection->find()->sort( $dataSort );
            
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