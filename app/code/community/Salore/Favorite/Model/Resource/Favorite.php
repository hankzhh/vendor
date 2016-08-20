<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Favorite to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Favorite
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Favorite_Model_Resource_Favorite extends Salore_Salon_Model_Resource_Abstract {
    protected $_collectionName;
    
    public function __construct() {
        //This is the same as getTableName()
        $this->_collectionName = $this->getCollectionName('favorite/favorite');
    }
}