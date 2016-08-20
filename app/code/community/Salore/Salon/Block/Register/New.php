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
class Salore_Salon_Block_Register_New extends Mage_Core_Block_Template {
    /**
     * @desc get form data when submiting was errored
     * @return array
     */
    public function getSessionFormData() {
        $formData = array();
         $customer = Mage::getSingleton('customer/session')->getCustomer();
        if($customer->getId()) {
                $address = $customer->getDefaultBillingAddress();
                if(isset($address) && $address) {
                    $addres = implode(',', $address->getStreet());
                    $formData['address'] = $addres ;
                    $formData['city']    = $address->getCity();
                    $formData['postcode'] = $address->getPostcode();
                    $formData['telephone'] = $address->getTelephone();
                    $formData['region_id'] = $address->getRegionId();
                }
            
            $formData['firstname'] = $customer->getFirstname();
            $formData['lastname'] = $customer->getLastname();
            $formData['email'] = $customer->getEmail();
             
        } 
        if(Mage::getSingleton('core/session')->getSessionFormData()) {
            $formData = Mage::getSingleton('core/session')->getSessionFormData();
            Mage::getSingleton('core/session')->unsSessionFormData();
        }
        return $formData;
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
    public function getCategoryMongo() {
        return Mage::getModel('salon/category')->getCollection();
    }
}


