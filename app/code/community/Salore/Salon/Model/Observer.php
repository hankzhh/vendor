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

class Salore_Salon_Model_Observer {
    public $data = array ();
    /**
     * Checking if current salon used a separated domain
     * @param unknown $observer
     */
    public function initFrontController($observer) {
        $domainName = Mage::helper('core/http')->getHttpHost();
        //Search if any salon map to this domain
        $salon = Mage::getModel('salon/salon')->getCollection()->findOne(array('domain' => $domainName));
        if ($salon && $salon->getData('domain') == $domainName) {
            Mage::register('currentsalon', $salon);
            Mage::app()->setCurrentStore($salon->getData('salon_url'));
        }
    }
    
    public function initRouters( $observer ) {
        $observer->getEvent()->getFront()
        ->addRouter('salon', new Salore_Salon_Controller_Router());
    }
    
    public function handleLayoutRender($observer) {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $controllerName = strtolower($controllerName);
        $moduleName = Mage::app()->getRequest()->getModuleName();
        $currentSalon = Mage::registry('currentsalon');
        if ($currentSalon) {
            $controllerName = Mage::app()->getRequest()->getControllerName();
            $controllerName = strtolower($controllerName);
            
            if ( $currentSalon->getData('approve') < 1 ) {
                Mage::getDesign()->setArea('frontend')
                ->setPackageName('salore')
                ->setTheme('default');
            }
            else if ( strpos($controllerName, 'admin_') !== false ) {
                Mage::getDesign()->setArea('frontend')
                ->setPackageName('salore')
                ->setTheme('admin');
            }
            else {
                Mage::getDesign()->setArea('frontend')
                //->setPackageName('salon')
                ->setPackageName('salore')
                ->setTheme('default');
            }
        }
        // add default salon layout handler
        $routeName =  Mage::app()->getRequest()->getRouteName();
        if($routeName == 'salon') {
            $layoutUpdate = Mage::getSingleton('core/layout')->getUpdate();
            $layoutUpdate->addHandle('salon_layout_handle');
        }
    }
    public function getSalonUrlArr($reservationArr) {
        $dataReturn = array();
        foreach($reservationArr as $reservation) {
            $dataReturn[$reservation['id']] = $reservation['salon_url'];
        }
        return $dataReturn;
    }
    public function paymentAfterHandle($observer) {
        //recode 
        //not exist website_id, remove websiteId inside controller reservation
        $checkoutSession = array();
        $order = $observer->getEvent()->getOrder();
        $reservationSession = Mage::getSingleton('checkout/session')->getData('salon_reservation');
        $salonUrlArr = $this->getSalonUrlArr($reservationSession);
        $checkoutSession = Mage::getSingleton('checkout/session')->getData('reservation_combo_arr');
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $cartItems = $quote->getAllVisibleItems();
        Mage::getSingleton('checkout/session')->unsCheckoutType();
        foreach ($cartItems as $item)
        {
            $serviceId = $item->getProductId();
            if( isset($checkoutSession[$serviceId]) && isset($salonUrlArr[$serviceId]) && array_key_exists($serviceId, $salonUrlArr) )
            {
                foreach ($checkoutSession[$serviceId] as $dkey => $staffArr)
                {
                    foreach ($staffArr as $staffId => $timeFrame)
                    {
                        $reservationTimestamp = Mage::getModel('core/date')->timestamp($dkey.' 00:00:00');
                        $dateBooking = Mage::getModel('core/date')->date('d-m-Y', $reservationTimestamp);
                        $modelObj = Mage::getModel('salon/reservation');
                        $modelObj->setSalonUrl($salonUrlArr[$serviceId]);
                        $modelObj->setData('entity_id', uniqid(Mage::helper('salon')->generatePassword()));
                        $modelObj->setData('service_id', $serviceId);
                        $modelObj->setData('order_id', $order->getId());
                        $modelObj->setData('customer_id', $order->getCustomerId());
                        $modelObj->setData('customer_name', $order->getCustomerName());
                        $modelObj->setData('customer_email', $order->getCustomerEmail());
                        $modelObj->setData('status', $order->getStatus());
                        $modelObj->setData('service_name', $item->getName());
                        $modelObj->setData('date_booking', $dateBooking);
                        $modelObj->setData('time_stamp', $reservationTimestamp);
                        $modelObj->setData('staff_id', $staffId);
                        $modelObj->setData('time_frame', $timeFrame);
                        $modelObj->setData('price', $item->getPrice());
                        $qty = substr_count($timeFrame, ',') + 1;
                        $modelObj->setData('qty', $qty);
                        $modelObj->setData('subtotal', (float)$item->getPrice()*$qty);
                        try {
                            $modelObj->save();
                            $myreservation = Mage::getModel('salon/myreservation');
                            $myreservation->setData($modelObj->getData());
                            $myreservation->setData('salon_url', $salonUrlArr[$serviceId]);
                            $myreservation->save();
                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return ;
                        }                        
                    }
                }
            }
            else {
                $modelObj = Mage::getModel('salon/order');
                $modelObj->setData('entity_id', uniqid('order_'));
                $modelObj->setData('product_id', $item->getProductId());
                $modelObj->setData('order_id', $order->getId());
                $modelObj->setData('customer_id', $order->getCustomerId());
                $modelObj->setData('customer_name', $order->getCustomerName());
                $modelObj->setData('customer_email', $order->getCustomerEmail());
                $modelObj->setData('status', $order->getStatus());
                $modelObj->setData('product_name', $item->getName());
                $modelObj->setData('price', $item->getPrice());
                $modelObj->setData('qty', $item->getQty());
                $modelObj->setData('subtotal', (float)$item->getPrice()*(float)$item->getQty());
                try {
                    $modelObj->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    return ;
                }
            }
        }
        Mage::getSingleton('checkout/session')->unsSalonReservation();
        Mage::getSingleton('checkout/session')->unsReservationComboArr();
    }
    public function setDataFromOrder(&$modelObj, $order) {
        $modelObj->setData('order_id', $order->getId());
        $modelObj->setData('customer_id', $order->getCustomerId());
        $modelObj->setData('customer_name', $order->getCustomerName());
        $modelObj->setData('customer_email', $order->getCustomerEmail());
        $modelObj->setData('status', $order->getStatus());
    }
    public function setDataAfterCheckout(&$modelObj, $checkoutType, $item) {
        if(isset($checkoutType) && $checkoutType == 'reservation') {
            if (isset($item['id']) && $item['id']) {
                $modelObj->setData('service_id', $item['id']);
            }
            if (isset($item['serviceName']) && $item['serviceName']) {
                $modelObj->setData('service_name', $item['serviceName']);
            }
            if (isset($item['timeframe']) && $item['timeframe']) {
                $modelObj->setData('time_frame', $item['timeframe']);
            }
            if (isset($item['price']) && $item['price']) {
                $modelObj->setData('price', $item['price']);
            }
        }
        else {
            if ($item->getName()) {
                $modelObj->setData('product_name', $item->getName());
            }
            if ($item->getPrice()) {
                $modelObj->setData('price', $item->getPrice());
            }
            if ($item->getQty()) {
                $modelObj->setData('qty', $item->getQty());
            }
            $modelObj->setData('total_price', (float)$modelObj->getPrice()*(float)$modelObj->getQty());
        }
    }
    public function productSaveBefore($observer) {
        //set default website id for all product for multiple website checkout purpose
        $product = $observer->getEvent()->getProduct();
        $websiteIds = $product->getWebsiteIds();
        $websiteIds[] = 1;
        $product->setWebsiteIds($websiteIds);
    }
}