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

class Salore_Salon_Admin_LogoutController extends Salore_Salon_Admin_BaseController
{
    /**
     * Customer logout action
     */
    public function indexAction() {
        $this->_getSession()->logout()
            ->renewSession();
        $loginUrl = Mage::helper('salon')->getUrl('admin/login');
        $this->_redirectUrl($loginUrl);
    }
}