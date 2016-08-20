<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Model_Resource_Service extends Salore_Salon_Model_Resource_Abstract {
    protected $_collectionName;

    public function __construct() {
        $this->_collectionName = $this->getCollectionName('salon/service');
    }
}