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
class Salore_Salon_Block_Admin_Profile_View extends Mage_Core_Block_Template {
    /**
     * get salon data of table salon from mongodb
     * @return array
     */
    public function getSalon() {
        $id = Mage::registry('currentsalon')->getEntityId();
        $salonInformation  = Mage::getModel('salon/salon')->load($id ,'_id');
        return $salonInformation;
    }
    public function getCategoryMongo() {
        return Mage::getModel('salon/category')->getCollection();
    }
}