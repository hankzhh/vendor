<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Admin_Banner_Banner extends Mage_Core_Block_Template {
    /**
     * get all imagebanner of table banner from mongodb
     * @return object
     */
    public function getBanner() {
        return Mage::getModel('salon/banner')->getCollection();
    }
}