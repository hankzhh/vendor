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
class Salore_Salon_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function reservationAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
}