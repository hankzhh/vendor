<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Admin_Customer_New extends Mage_Core_Block_Template {
    public function getCustomerMongo() {
        $customerId = $this->getRequest()->getParam('id');
        $customerMongo = Mage::getModel('salon/customer');
        if(isset($customerId) && $customerId) {
            $customerMongo->load($customerId , 'entity_id');
        }
        return $customerMongo;
    }
    /**
     * get regions by countryId
     * @return array
     */
    public function getRegionsByCountryId($countryId = 'US') {
        $collection = Mage::getModel('directory/region')->getResourceCollection()
        ->addCountryFilter($countryId)
        ->load();
        return $collection;
    }
}
