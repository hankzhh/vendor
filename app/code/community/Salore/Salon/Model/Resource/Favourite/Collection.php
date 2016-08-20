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
class Salore_Salon_Model_Resource_Favourite_Collection extends Salore_Mongo_Model_Resource_Collection_Abstract {


    public function load($printQuery = false, $logQuery = false) {
        if ($this->isLoaded()) {
            return $this;
        }
        $collectionName = $this->_conn->getCollectionName();
        $collection = $this->getMongoAdapter()->getCollection($collectionName);
        $customers = array();
        $filterQuery = $this->filterQuery;
        $items = array();
        if ( is_array($filterQuery) && !empty($filterQuery) ) {
            $mongoCollection = $collection->getCollection();
            $cursor = $mongoCollection->find($filterQuery);
            $customers = iterator_to_array( $cursor );
        }
        else {
            $mongoCollection = $collection->getCollection();
            $cursor = $mongoCollection->find();//->sort(array('entity_id'));
            $customers = iterator_to_array( $cursor );
        }

        foreach ($customers as $v) {
            $object = $this->getNewEmptyItem()
            ->setData($v);
            $items[] = $object;
        }
        $this->_items = $items;
        $this->_setIsLoaded();

        return $this;
    }

}