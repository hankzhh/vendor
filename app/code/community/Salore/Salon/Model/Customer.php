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
class Salore_Salon_Model_Customer extends Salore_Mongo_Model_Abstract {
    protected function _construct() {
        $this->_init('salon/customer');
    }
    public function setSalonUrl($salonUrl) {
        $this->_getResource()->setSalonUrl($salonUrl);
    }
    public function checkSalonEmailExists($email) {
        $collection = $this->getCollection();
        $filterQuery = array( 'email' => trim($email) );
        $salon = $collection->findOne( $filterQuery );
        if ($salon->getEmail()) {
            return $salon;
        }
        return false;
    }
}