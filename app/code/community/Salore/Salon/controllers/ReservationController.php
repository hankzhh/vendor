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
class Salore_Salon_ReservationController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * render service list when customer select information to reservation
     * by ajax
     */
    public function ajaxDayCallServiceAction() {
        $this->loadLayout();
        $layout = $this->getLayout();
        $block = $layout->createBlock('salon/reservation_form_list', 'reservation.list')->setTemplate('salore/salon/home/reservation/form/list.phtml');
        echo $block->toHtml();
        return ;
    }
    /**
     * add service to cart
     * by ajax
     */
    public function addToCartAction() {
        $response = array('status' => 'ERROR', 'message' => 'Can not add to cart. The System have a problem.');
         $ajaxParams = $this->getRequest()->getParams();
         $salonUrl = Mage::registry('currentsalon')->getSalonUrl();
         $website_id = Mage::app()->getWebsite()->getId();
         if(isset($ajaxParams['id']) && $ajaxParams['id'] && isset($ajaxParams['type']) && $ajaxParams['type'] && isset($ajaxParams['date']) && $ajaxParams['date'] && isset($ajaxParams['timeframe']) && $ajaxParams['timeframe'] && isset($ajaxParams['price']) && $ajaxParams['price'] && isset($ajaxParams['staffId']) && $ajaxParams['staffId']) {
             //check if exist product, allow add to cart
             $product = $this->prepareProductMagento($ajaxParams['id']);
             if($product)
             {
                if($ajaxParams['type'] === 'add') {
                    $ajaxParams['salon_url'] = $salonUrl;
                    $cart = Mage::helper('checkout/cart')->getCart();
                    $cart->init();
                    $cart->addProduct($product, array('qty' => 1));
                    //prepare data to show reservation list in salon page
                    $reservationData = Mage::getSingleton('checkout/session')->getData('salon_reservation');
                    if (is_array($reservationData)) {
                        $reservationData[md5($ajaxParams['id']. $ajaxParams['date']. $ajaxParams['timeframe'])] = $ajaxParams;
                        Mage::getSingleton('checkout/session')->setData('salon_reservation', $reservationData);
                    }
                    else {
                        $reservationData = array();
                        $reservationData[md5($ajaxParams['id']. $ajaxParams['date']. $ajaxParams['timeframe'])] = $ajaxParams;
                        Mage::getSingleton('checkout/session')->setData('salon_reservation', $reservationData);
                    }
                    //prepare data to save in observcer
                    $reservationComboArr = Mage::getSingleton('checkout/session')->getData('reservation_combo_arr');
                    if (is_array($reservationComboArr)) {
                        if (isset($reservationComboArr[$ajaxParams['id']][$ajaxParams['date']]) && array_key_exists($ajaxParams['staffId'], $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']])) {
                            $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']] .= ',' . $ajaxParams['timeframe'];
                        }
                        else {
                            $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']] = $ajaxParams['timeframe'];
                        }
                        Mage::getSingleton('checkout/session')->setData('reservation_combo_arr', $reservationComboArr);
                    }
                    else {
                        $reservationComboArr = array();
                        $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']] = $ajaxParams['timeframe'];
                        Mage::getSingleton('checkout/session')->setData('reservation_combo_arr', $reservationComboArr);
                    }
                    
                    $cart->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                    $response['status'] = 'SUCCESS';
                    $response['message'] = 'Added to cart successfully';
                }     
                else {
                    try {
                        //remove a service from cart
                        $cartHelper = Mage::helper('checkout/cart');
                        $items = $cartHelper->getCart()->getItems();
                        foreach ($items as $item) {
                            if ($item->getProduct()->getId() == $product->getId()) {
                                if( $item->getQty() == 1 ){
                                    $cartHelper->getCart()->removeItem($item->getItemId())->save();
                                }
                                else if($item->getQty() > 1){
                                    $item->setQty($item->getQty() - 1);
                                    $cartHelper->getCart()->save();
                                }
                                break;
                            }
                        }
                        //section that have got to show in salon page
                        $reservationData = Mage::getSingleton('checkout/session')->getData('salon_reservation');
                        if(is_array($reservationData) ) {
                            if (array_key_exists(md5($ajaxParams['id']. $ajaxParams['date']. $ajaxParams['timeframe']), $reservationData)) {
                                unset($reservationData[md5($ajaxParams['id']. $ajaxParams['date']. $ajaxParams['timeframe'])]);
                                Mage::getSingleton('checkout/session')->setData('salon_reservation', $reservationData);
                            }
                        }
                        //section that have got to save in observer
                        $reservationComboArr = Mage::getSingleton('checkout/session')->getData('reservation_combo_arr');
                        if(is_array($reservationComboArr) ) {
                            if (isset($reservationComboArr[$ajaxParams['id']][$ajaxParams['date']]) && array_key_exists($ajaxParams['staffId'], $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']])) {
                                if($reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']] == $ajaxParams['timeframe']) {
                                    unset($reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']]);
                                    if(count($reservationComboArr[$ajaxParams['id']][$ajaxParams['date']]) == 0) {
                                        unset($reservationComboArr[$ajaxParams['id']][$ajaxParams['date']]);
                                    }
                                    if(count($reservationComboArr[$ajaxParams['id']]) == 0) {
                                        unset($reservationComboArr[$ajaxParams['id']]);
                                    }
                                }
                                else {
                                    $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']] = str_replace($ajaxParams['timeframe'], '', $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']]);
                                    $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']] = trim($reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']], ',');
                                    $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']] = str_replace(',,', ',', $reservationComboArr[$ajaxParams['id']][$ajaxParams['date']][$ajaxParams['staffId']]);
                                }
                                Mage::getSingleton('checkout/session')->setData('reservation_combo_arr', $reservationComboArr);
                            }
                        }
                        $response['status'] = 'SUCCESS';
                        $response['message'] = 'Added to cart successfully';
                    }catch (Exception $e) {
                        throw $e;
                    }
                }
             }
         }
         echo json_encode($response);
         return ;
    }
    public function prepareProductMagento($sku) {
        $salon = Mage::registry('currentsalon');
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        if(is_object($product) && $product->getId()){
            return Mage::getModel('catalog/product')
            ->setStoreId($salon->getData('store_id'))
            ->load($product->getId());
        }    
        //add new product
        
        $service = Mage::getModel('salon/service')->load($sku);
        $attributeSetId = Mage::helper ( 'salon' )->getAttributeSetIdNameDefault ();
        $product = Mage::getModel ( 'catalog/product' );
        $product->setSku ( $sku );
        $product->setCategoryIds (
                array(3, 10)
        );
        $product->setWebsiteIds (
                array($salon->getWebsiteId())
        );
        $product->setStoreId ( $salon->getStoreId() );
        $product->setAttributeSetId ( $attributeSetId );
        $product->setTypeId ( 'simple' );
        $product->setName ( $service->getData('service_name') );
        $product->setDuration ( $service->getData('duration') );
        $product->setPrice ( $service->getData('price') );
        $product->setDescription ( $service->getData('description') );
        $product->setShortDescription ( $service->getData('short_description') );
        $product->setWeight ( 1.0 );
        $product->setVisibility ( 4 );
        $product->setStatus ( 1 );
        $product->setTaxClassId ( 0 );
        $product->setStockData(array(
                                    'manage_stock' => 1,
                                   'is_in_stock' => 1,
                                    'qty' => 1000
                                ));
        $product->setCreatedAt ( $service->getData('created_at') );
        try {
            $product->save ();
            return Mage::getModel('catalog/product')->load($product->getId());
        } catch (Exception $e) {
            throw $e;
        }
        return false;
    }
}