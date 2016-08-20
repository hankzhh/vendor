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
class Salore_Salon_Block_Onepage_Link extends Mage_Checkout_Block_Onepage_Link {
    /**
     * Get Link CheckOut Product on Cart template and My Appointment
     * @return string
     */
    public function getCheckoutUrl() {
        return Mage::helper('salon')->getUrl('onepage', array('_secure'=>true));
    }
    /**
     *
     * @return boolean
     */
    public function isDisabled() {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }
    /**
     * Check status Checkout of product
     */
    public function isPossibleOnepageCheckout() {
       return $this->helper('checkout')->canOnepageCheckout();
    }
}
