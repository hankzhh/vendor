<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    Salore
 * @package       Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Model_Posts extends Salore_Mongo_Model_Abstract {
    protected function _construct() {
        $this->_init('classified/posts');
    }

}