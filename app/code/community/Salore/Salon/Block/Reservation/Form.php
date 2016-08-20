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
class Salore_Salon_Block_Reservation_Form extends Mage_Core_Block_Template {
    public $_dateSelected = null;
    protected $_salonId;
    public function __construct() {
        $this->_salonId = Mage::registry('currentsalon')->getEntityId();
        if($this->getRequest()->getParam('dateSelected')) {
            $this->_dateSelected = $this->getRequest()->getParam('dateSelected');
        }
    }
    /**
     * build html service list
     * @return html
     */
    public function getServicesListHtml() {
        $titleBlockHtml = $this->getChildHtml('reservation.list');
        return $titleBlockHtml;
    }
    /**
     * get Salon Id
     */
    public function getSalonId() {
        return $this->_salonId;
    }
    /**
     * get Action Checkout
     */
    public function getAction() {
        return Mage::getUrl('salon/checkout/redirect');
    }
    /**
     * get all servicedata from mongodb
     * @return object
     */
    public function getServiceCollection() {
        $_serviceCollection = Mage::getModel('salon/service')->getCollection();
        $_serviceCollection->addFilterQuery(array('salon_id'=> $this->_salonId));
        return $_serviceCollection;
    }
    /**
     * get all staffdata from mongodb
     * @return object
     */
    public function getStaffCollection() {
        return Mage::getModel('salon/staff')->getCollection();
    }
}