<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Model_Resource_Category extends Salore_Mongo_Model_Resource_Abstract {
    /**
     * Retreive table name
     *
     * @param string $alias
     * @return string
     */
    protected $_collectionName;

    public function __construct() {
        //This is the same as getTableName()
        $this->_collectionName = $this->getCollectionName('classified/category');
    }
}