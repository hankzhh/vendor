<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Register_Success extends Mage_Core_Block_Template {
    /**
     * Get Notify success when Registered Salon
     * @return string
     */
    public function getSalon() {
        $salon = Mage::getSingleton('core/session')->getSuccessSavedNewSalon();
        Mage::getSingleton('core/session')->unsSuccessSavedNewSalon();
        return $salon;
    }
}