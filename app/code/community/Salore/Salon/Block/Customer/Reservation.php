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
class Salore_Salon_Block_Customer_Reservation extends Mage_Core_Block_Template {
    /**
     * Get all Appointment laid of Customer in MongoDB
     * @return array
     */
    public function getMyReservationCollection() {
        $customerEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        $collection = Mage::getModel('salon/myreservation')->getCollection();
        $filterQuery = array('customer_email' => $customerEmail);
        $collection->addFilterQuery($filterQuery);
        $dataReturn = array();
        foreach ($collection as $item) {
            $dataReturn[] = $item->getData();
        }
        return $dataReturn;
    }
    /**
     * Get Name Staff in MongoDB examples Mr . XXX , Ms . XXX
     * @param  $id
     * @param  $salonUrl
     * @return string StaffName or null
     */
    public function getStaffName($id, $salonUrl) {
        $staff = Mage::getModel('salon/staff');
        $staff->setSalonUrl($salonUrl);
        $staff->load($id, 'entity_id');
        if($staff->getEntityId()) {
            return $staff->getName();
        }
        return null;
    }
}