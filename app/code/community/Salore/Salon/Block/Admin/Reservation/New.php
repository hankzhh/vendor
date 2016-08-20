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
class Salore_Salon_Block_Admin_Reservation_New extends Mage_Core_Block_Template {
    public function getReservation() {
        $id = $this->getRequest()->getParam('id');
        $reservationModel = Mage::getModel('salon/reservation');
        if(isset($id) && $id) {
            return $reservationModel->load($id, 'entity_id');
        }
        return $reservationModel;
        
    }
    public function getCustomerCollection() {
        return Mage::getModel('salon/customer')->getCollection();
    }
    public function getServiceCollection() {
        return Mage::getModel('salon/service')->getCollection();
    }
    public function getStaffCollection() {
        return Mage::getModel('salon/staff')->getCollection();
    }
}