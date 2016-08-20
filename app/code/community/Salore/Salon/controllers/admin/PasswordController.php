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

class Salore_Salon_Admin_PasswordController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * change password in salon admin page
     */
    public function changePasswordAction() {
        $formData = $this->getRequest()->getParams();
        if (isset($formData['current_password']) && $formData['current_password'] && isset($formData['password']) && $formData['password'] && isset($formData['confirmation']) && $formData['confirmation']) {
            if ($formData['password'] === $formData['confirmation']) 
            {
                $pattern = '/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/';
                //save new password
                try {
                    $customer = $this->_getSession()->getCustomer();
                    $currPass   = $this->getRequest()->getPost('current_password');
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');
                
                    $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                    if ( Mage::helper('core/string')->strpos($oldPass, ':')) {
                        list($_salt, $salt) = explode(':', $oldPass);
                    } else {
                        $salt = false;
                    }
                    if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                        if (strlen($newPass)) {
                            /**
                             * Set entered password and its confirmation - they
                             * will be validated later to match each other and be of right length
                             */
                            $customer->setPassword($newPass);
                            $customer->setConfirmation(null);
                            $customer->save();
                            Mage::getSingleton('core/session')->addSuccess('Password have changed successfully!');
                            $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/login'));
                        } else {
                            Mage::getSingleton('core/session')->addError($this->__('New password field cannot be empty.'));
                            $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/password'));
                        }
                    } else {
                        Mage::getSingleton('core/session')->addError($this->__('Invalid current password'));
                        $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/password'));
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                    $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/password'));
                }
            }
            else {
                Mage::getSingleton('core/session')->addError($this->__('Password does not match the confirm password!'));
                $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/password'));
            }
        }
        else {
            Mage::getSingleton('core/session')->addError($this->__('Please enter your new password! '));
            $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/password'));
        }
    }
}