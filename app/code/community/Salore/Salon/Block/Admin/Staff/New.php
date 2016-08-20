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
class Salore_Salon_Block_Admin_Staff_New extends Mage_Core_Block_Template {
     /**
      * get staff data of table staff from mongodb
      * @return array
      */
    public function getStaff()  {
        $staffId = $this->getRequest()->getParam('id');
        $staffMongo = Mage::getModel('salon/staff');
        if ( $staffId ) {
            return $staffMongo->load($staffId, 'entity_id');
        }
        return $staffMongo;
    }
    /**
     * get Action Name
     * @return string
     */
    public function getActionName() {
        return Mage::app()->getRequest()->getActionName();
    }
    /**
     * get Action save on template form staff
     * @return string url
     */
    public function getActionForForm() {
        return Mage::helper('salon')->getUrl('admin/staff/save');
    }
}
