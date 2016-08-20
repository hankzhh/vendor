<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Model_Resource_Staff_Collection extends Salore_Mongo_Model_Resource_Collection_Abstract {
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
            $cursor = $mongoCollection->find()->sort(array('entity_id'));
            if($limit = $this->getLimit()) {
                $cursor->limit($limit);
            }
            $documents = iterator_to_array( $cursor );
        }
        else {
            $mongoCollection = $collection->getCollection();
            $cursor = $mongoCollection->find()->sort(array('entity_id'));
            if($limit = $this->getLimit())
            {
                $cursor->limit($limit);
            }
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