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

class Salore_Salon_Admin_SalonController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function settingAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function generatemenuAction() {
        try{
        Mage::getModel('salon/menu')->generateDefaultMenu();
        }catch (Exception $e)
        {
            Mage::log($e->getMessage() , null , 'salonmenu.log');    
        }
    }
}