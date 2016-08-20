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
class Salore_Salon_LoginController extends Mage_Core_Controller_Front_Action
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
        $email = $login['username'];
        $salonMongo = Mage::getModel('salon/salon')->load($email , 'email');
        $adminUrl = Mage::getUrl('salon/login');
        if(!$salonMongo->getEntityId()) {
            Mage::getSingleton('core/session')->addError($this->__('<p>Your account has not had any salon yet!</p><p>Please click <a href="/salon/new">Open new salon</a> to create a new salon or <a href="/">Home page<a>!<p>'));
            $this->_redirectUrl($adminUrl);
            return;
        }
        if ($this->_getSession()->isLoggedIn()) {
            if($salonMongo->getSalonUrl() && $salonMongo->getApprove()) {
                $adminUrl = Mage::getUrl().$salonMongo->getSalonUrl().'/admin/salon/setting';
    
            }
            $this->_redirectUrl($adminUrl);
            return;
        }
        $session = $this->_getSession();
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        if($salonMongo->getSalonUrl() && $salonMongo->getApprove()) {
                            $adminUrl = Mage::getUrl().$salonMongo->getSalonUrl().'/admin/salon/setting';
                        }
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
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
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
        $login = $this->getRequest()->getPost('login');
        $email = $login['username'];
        $session = $this->_getSession();
        $adminUrl = Mage::getUrl('salon/login');
        $salonMongo = Mage::getModel('salon/salon')->load($email , 'email');
        if ($session->isLoggedIn()) {
            if($salonMongo->getSalonUrl() && $salonMongo->getApprove()) {
                $adminUrl = Mage::getUrl().$salonMongo->getSalonUrl().'/admin/salon/setting';
            }
        }
        $this->_redirectUrl($adminUrl);
    }
} 