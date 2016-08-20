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
class Salore_Salon_Model_Menu extends Salore_Mongo_Model_Abstract {
    protected function _construct() {
        $this->_init('salon/menu');
    }
    
    public function generateDefaultMenu($aboutusPageId) {
        $menuItems = array(
                array('title' => 'About us', 'path' => 'page/view/id/'.$aboutusPageId, 'active' => '1', 'system' => 1),
                array('title' => 'Products', 'path' => 'product', 'active' => '1', 'system' => 1),
                array('title' => 'Services', 'path' => 'service', 'active' => '1', 'system' => 1),
                array('title' => 'Reservation', 'path' => 'reservation', 'active' => '1', 'system' => 1),
                array('title' => 'Gallery', 'path' => 'gallery', 'active' => '1', 'system' => 1),
                array('title' => 'Contact', 'path' => 'contact', 'active' => '1', 'system' => 1),
        );
        $menu = Mage::getModel('salon/menu');
        foreach ($menuItems as $menuData) {
            $menu->setData($menuData);
            $menu->save();
            $menu->clearInstance();
        }
         
    }
}