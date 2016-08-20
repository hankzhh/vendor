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
class Salore_Salon_Block_Salon_Contact extends Mage_Core_Block_Template {
    public function showSessionWhenFormError() {
        $sessionData = Mage::getSingleton('core/session')->getData('salon_contact_data');
        Mage::getSingleton('core/session')->unsetData('salon_contact_data');
        return !empty($sessionData) ? $sessionData : array();
    }
}