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
class Salore_Favorite_Model_Favorite extends Salore_Mongo_Model_Abstract {
    protected function _construct() {
        $this->_init('favorite/favorite');
    }
}