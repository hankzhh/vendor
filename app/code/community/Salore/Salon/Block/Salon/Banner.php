<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Salon_Banner extends Mage_Core_Block_Template {
    /**
     * get image banner data of table banner from mongodb
     * @return object
     */
    public function getBanner() {
        $salonId = Mage::registry('currentsalon')->getEntityId();
        $bannerObj = Mage::getModel('salon/banner')->getCollection();
        $bannerObj->addFilterQuery(array('salon_id' => $salonId));
        return $bannerObj;
    }
}