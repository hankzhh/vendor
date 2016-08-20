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

class Salore_Salon_Admin_IndexController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        
        $adminUrl = Mage::helper('salon')->getUrl('admin/salon/setting');
        
        $this->_redirectUrl($adminUrl);
        
        return;
    }
}