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
class Salore_Salon_Block_Admin_Customer_List extends Mage_Core_Block_Template {
    public function getCustomerCollection() {
        $collection = Mage::getModel('salon/customer')->getCollection();
        return $collection;
    }
}