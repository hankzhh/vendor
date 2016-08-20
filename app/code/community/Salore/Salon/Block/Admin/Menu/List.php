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
class Salore_Salon_Block_Admin_Menu_List extends Mage_Core_Block_Template {
    /**
     * get menudata of table menu from mongodb
     * @return object
     */
    public function getMenuCollection() {
        $collection = Mage::getModel('salon/menu')->getCollection();
        $collection->setOrder('position' , 'ASC');
        return $collection;
    }
}