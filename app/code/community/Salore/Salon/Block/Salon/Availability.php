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
class Salore_Salon_Block_Salon_Availability extends Mage_Core_Block_Template {
    public function getCurrentDate() {
        $currentTimestamp = Mage::app()->getLocale()->storeTimeStamp(Mage::app()->getStore()->getId());
        return date('d-m-Y', $currentTimestamp);
    }
    
    public function getServices() {
        $collection = Mage::getModel('salon/service')->getCollection();
        return $collection;
    }
    public function getStaffs() {
        return Mage::getModel('salon/staff')->getCollection();
    }
    protected function _prepareLayout() {
        $this->getLayout()->getBlock( 'head')
        ->addCss( 'css/datepicker.css' )
        ->addItem( 'skin_js', 'js/datepicker.js' );
        return parent::_prepareLayout();
    }
}