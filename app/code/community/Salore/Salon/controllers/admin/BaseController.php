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

class Salore_Salon_Admin_BaseController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch() {
        // a brute-force protection here would be nice
        
        parent::preDispatch();
    
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        
        $loginUrl = Mage::helper('salon')->getUrl('admin/login');
        
        if (!$this->_getSession()->authenticate($this, $loginUrl)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
}