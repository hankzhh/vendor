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
class Salore_Salon_ProductController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function viewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * add product to cart by ajax
     */
    public function addToCartAction() {
        $response = array();
        $ajaxParams = $this->getRequest()->getParams();
        if(isset($ajaxParams) && $ajaxParams) {
            if($ajaxParams['type'] === 'add') {
                $_product = Mage::getModel('catalog/product')->load($ajaxParams['id']);
                $cart = Mage::helper('checkout/cart')->getCart();
                $cart->init();
                $cart->addProduct($_product, array('qty' => 1));
                $cart->save();
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                $response['status'] = 'SUCCESS';
                echo json_encode($response);
                return ;
            }
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = 'Can not add to cart. The System have a problem.';
            echo json_encode($response);
            return ;
        }
    }
}