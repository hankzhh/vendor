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
require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';
class Salore_Salon_CartController extends Mage_Checkout_CartController {
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function updatePostAction() {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }
    
        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');
        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }
    
        $this->_goBack();
    }
    public function _emptyShoppingCart() {
        try {
            Mage::getSingleton('checkout/cart')->truncate()->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $exception) {
            Mage::getSingleton('checkout/session')->addError($exception->getMessage());
        } catch (Exception $exception) {
            Mage::getSingleton('checkout/session')->addException($exception, $this->__('Cannot update shopping cart.'));
        }
    }
    public function _goBack() {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {
    
            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }
    
            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
                && !$this->getRequest()->getParam('in_cart')
                && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            //$this->_redirect('cart');
            $url = Mage::helper('salon')->getUrl('cart');
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
        }
        return $this;
    }
}