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
class Salore_Salon_Block_Admin_Menu_New extends Mage_Core_Block_Template {
    /**
     * get menu data of table menu from mongodb
     * @return array
     */
    public function getMenu() {
        $menuId = Mage::app()->getRequest()->getParam('id');
        $menuMongo = Mage::getModel('salon/menu');
        if (isset($menuId) && $menuId) {
            return $menuMongo->load($menuId, 'entity_id');
        }
        return $menuMongo;
    }
    /**
     * get action save in template form menu
     * @return string url
     */
    public function getAction() {
        return Mage::helper('salon')->getUrl('admin/menu/save');
    }
    /**
     * get link default of menu when add new menu
     * @return array
     */
    public function getDefaultLinks() {
        return array('service' => 'Service', 'product' => 'Product', 'contact' => 'Contact','gallery' => 'Gallery', 'reservation' => 'Reservation');
    }
    /**
     * get pagedata of table page from mongodb
     * @return object
     */
    public function getPages() {
        return Mage::getModel('salon/page')->getCollection();
    }
}