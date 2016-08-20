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

class Salore_Salon_Admin_LoginController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    public function _getSession() {
        return Mage::getSingleton('customer/session');
    }
    
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Login post action
     */
    public function postAction() {
        $login = $this->getRequest()->getPost('login');
        $salonEmail = Mage::registry('currentsalon')->getEmail();
        if($salonEmail !== $login['username']) {
            Mage::getSingleton('core/session')->addError($this->__('Invalid login or password. '));
            $this->_redirectUrl(Mage::helper('salon')->getUrl('admin'));
            return;
        }
        if ($this->_getSession()->isLoggedIn()) {
            $adminUrl = Mage::helper('salon')->getUrl('admin/salon/setting');
            $this->_redirectUrl($adminUrl);
            return;
        }
        $session = $this->_getSession();
    
        if ($this->getRequest()->isPost()) {
            
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $adminUrl = Mage::helper('salon')->getUrl('admin');
                        $this->_redirectUrl($adminUrl);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    Mage::getSingleton('core/session')->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__('Login and password are required.'));
            }
        }
    
        $this->_loginPostRedirect();
    }
    
    /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect() {
        $session = $this->_getSession();
        $adminUrl = Mage::helper('salon')->getUrl('admin');
        if ($session->isLoggedIn()) {
            $adminUrl = Mage::helper('salon')->getUrl('admin/salon/setting');
        }
        $this->_redirectUrl($adminUrl);
    }
}