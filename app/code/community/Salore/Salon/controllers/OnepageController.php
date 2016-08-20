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
require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'OnepageController.php';
class Salore_Salon_OnepageController extends Mage_Checkout_OnepageController {
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax() {
        return false;
    }
}