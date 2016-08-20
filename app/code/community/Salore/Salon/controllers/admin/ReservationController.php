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

class Salore_Salon_Admin_ReservationController extends Salore_Salon_Admin_BaseController
{
    /**
     * initialize register to use in working flow
     */
    protected function _initData() {
        $resId = $this->getRequest()->getParam('id');
        if($this->getRequest()->getParam('type') === 'product_order')
        {
            Mage::register('current_reservation', Mage::getModel('salon/order'));
        }
        else
        {
            Mage::register('current_reservation', Mage::getModel('salon/reservation'));
        }
        if($resId)
        {
            Mage::registry('current_reservation')->load($resId, 'entity_id');
        }
    }
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * confirm reservation status after customer pay
     */
    public function confirmAction() {
        $this->_initData();
        $response = array();
        $reservationObj = Mage::registry('current_reservation');
        if($reservationObj->getEntityId()) {
            if ($reservationObj->getStatus() != 'complete')  {
                $reservationObj->setData('status', 'complete' );
                try {
                    $reservationObj->save();
                } catch (Exception $e) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $e->getMessage();
                    echo json_encode($response);
                    return ;
                }
                $response['status'] = 'SUCCESS';
                echo json_encode($response);
                return ;
            }
            else {
                $response['status'] = 'ERROR';
                $response['message'] = 'This item have already confirmed!';
                echo json_encode($response);
                return ;
            }
        }
        $response['status'] = 'ERROR';
        $response['message'] = Mage::helper('mongobridge_salon')->__('System error. Please check your php log file fore more detail.');
        echo json_encode($response);
        return ;
    }
    /**
     * delete a record of reservation table from mongodb 
     */
    public function deleteAction() {
        $response = array();
        $this->_initData();
        $reservationObj = Mage::registry('current_reservation');
        if ($reservationObj->getEntityId() != null) {
            try {
                $reservationObj->delete();
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $e->getMessage();
                echo json_encode($response);
                return ;
            }
            $response['status'] = 'SUCCESS';
            echo json_encode($response);
            return ;
        }
        $response['status'] = 'ERROR';
        $response['message'] = Mage::helper('mongobridge_salon')->__('System error. Please check your php log file fore more detail.');
        echo json_encode($response);
        return ;
    }
    public function newAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function renderTimeframeOptionAction() {
        $this->loadLayout();
        $layout = $this->getLayout();
        $block = $layout->createBlock('salon/admin_reservation_render_timeframe', 'reservation.timeframe.option')->setTemplate('salore/salon/admin/reservation/render/timeframe.phtml');
        echo $block->toHtml();
        return ;
    }
    public function saveAction() {
        $formData = $this->getRequest()->getParams();
        // Get the Quote to save the order
        $reservation = Mage::getModel('salon/reservation');
        $order = Mage::getModel('sales/order')->getCollection()->getData();
        if($orderId = $this->createOrderNew($formData)) {
                
            //save to mongodb
            if(isset($formData['id']) && !$formData['id']) {
                $reservation->setData('entity_id', uniqid(Mage::helper('salon')->generatePassword()));
            }
            if(isset($formData['customer_id']) && $formData['customer_id']) {
                $customerMogo = Mage::getModel('salon/customer')->load($formData['customer_id'] , 'entity_id');
                $reservation->setData('customer_id' , $formData['customer_id']);
                $reservation->setData('customer_name' , $customerMogo->getFirstname(). ' ' .$customerMogo->getLastname());
                $reservation->setData('customer_email' , $customerMogo->getEmail());
            }
            if(isset($formData['service_id']) && $formData['service_id']) {
                $serviceMongo = Mage::getModel('salon/service')->load($formData['service_id'] , 'entity_id');
                $reservation->setData('service_id' , $formData['service_id']);
                $reservation->setData('service_name' , $serviceMongo->getServiceName());
                $reservation->setData('price' , $serviceMongo->getPrice());
                $reservation->setData('qty' , 1);
                $reservation->setData('subtotal' , $serviceMongo->getPrice());
            }
            if(isset($formData['date_booking']) && $formData['date_booking']) {
                $reservationTimestamp = Mage::getModel('core/date')->timestamp($formData['date_booking'].' 00:00:00');
                $reservation->setData('time_stamp', $reservationTimestamp);
                $reservation->setData('date_booking' , $formData['date_booking']);
            }
            if(isset($formData['staff_id']) && $formData['staff_id']) {
                $reservation->setData('staff_id' , $formData['staff_id']);
            }
            if(isset($formData['time_frame']) && $formData['time_frame']) {
                $reservation->setData('time_frame' , $formData['time_frame']);
            }
            $reservation->setData('order_id' , (int)$orderId);
            $reservation->setData('status' , 'pending');
            if(isset($formData['confirm']) && $formData['confirm']) {
                $reservation->setData('status' , 'complete');
            }
            try {
                $reservation->save();
                $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/reservation'));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
    }
    public function createOrderNew($formData) {
        require_once 'app/Mage.php';
        Mage::app();
        $id= $formData['customer_id']; 
        $_customer_data = Mage::getModel('salon/customer')->load($id, 'entity_id');
        $transaction = Mage::getModel('core/resource_transaction');
        $storeId = Mage::app()->getStore('default')->getId();
        $reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
        $order = Mage::getModel('sales/order')
        ->setIncrementId($reservedOrderId)
        ->setStoreId($storeId)
        ->setQuoteId(0)
        ->setGlobal_currency_code('USD')
        ->setBase_currency_code('USD')
        ->setStore_currency_code('USD')
        ->setOrder_currency_code('USD');
        // set Customer data
        $firstName = $_customer_data->getFirstname(); 
        $lastName = $_customer_data->getLastname(); 
        $address = $_customer_data->getData('address');
        $street = $_customer_data->getData('address');
        $city = $_customer_data->getData('city');
        $postcode = $_customer_data->getData('postcode');
        $phoneNumber = $_customer_data->getData('telephone');
        $countryId = $_customer_data->getData('country_id');
        $regionId = $_customer_data->getData('region_id');
        $region = $_customer_data->getData('region');
        $order->setCustomer_email($_customer_data->getEmail())
        ->setCustomerFirstname($_customer_data->getFirstname())
        ->setCustomerLastname($_customer_data->getLastname())
        ->setCustomer_is_guest(1);
        $billingAddress = Mage::getModel('sales/order_address')
        ->setStoreId($storeId)
        ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
        ->setFirstname($firstName)
        ->setMiddlename('')
        ->setLastname($lastName)
        ->setCompany('')
        ->setStreet($street)
        ->setCity($city)
        ->setCountry_id($countryId)
        ->setRegion($region)
        ->setRegion_id($regionId)
        ->setPostcode($postcode)
        ->setTelephone($phoneNumber);
        $order->setBillingAddress($billingAddress);
        $shippingAddress = Mage::getModel('sales/order_address')
        ->setStoreId($storeId)
        ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
        ->setFirstname($firstName)
        ->setMiddlename('')
        ->setLastname($lastName)
        ->setCompany('')
        ->setStreet($street)
        ->setCity($city)
        ->setCountry_id($countryId)
        ->setRegion($region)
        ->setRegion_id($regionId)
        ->setPostcode($postcode)
        ->setTelephone($phoneNumber);
        $order->setShippingAddress($shippingAddress)
        ->setShipping_method('flatrate_flatrate')
        ->setShippingDescription('flatrate');
        $orderPayment = Mage::getModel('sales/order_payment')
        ->setStoreId($storeId)
        ->setCustomerPaymentId(0)
        ->setMethod('purchaseorder')
        ->setPo_number(' - ');
        $order->setPayment($orderPayment);
        $product_id = $formData['service_id']; 
        $_product = Mage::getModel('catalog/product')->load($product_id);
        $subTotal = 0;
        $rowTotal = $_product->getPrice();
        $orderItem = Mage::getModel('sales/order_item')
        ->setStoreId($storeId)
        ->setQuoteItemId(0)
        ->setQuoteParentItemId(NULL)
        ->setProductId($product_id)
        ->setProductType($_product->getTypeId())
        ->setQtyBackordered(NULL)
        ->setTotalQtyOrdered(1)
        ->setQtyOrdered(1)
        ->setName($_product->getName())
        ->setSku($_product->getSku())
        ->setPrice($_product->getPrice())
        ->setBasePrice($_product->getPrice())
        ->setOriginalPrice($_product->getPrice())
        ->setRowTotal($rowTotal)
        ->setBaseRowTotal($rowTotal);
        $subTotal += $rowTotal;
        $order->addItem($orderItem);
        $order->setSubtotal($subTotal)
        ->setBaseSubtotal($subTotal)
        ->setGrandTotal($subTotal)
        ->setBaseGrandTotal($subTotal);
        try {
            $order->save();
            return $order->getData('entity_id');
        } catch (Exception $e) {
            echo $e->getMessage() ; 
        }
        return false;
    }
    
    public function sortAction(){
        $data = array( 'p' => 1 );
    
        $sort = $this->getRequest()->getParam('sortBy');
    
        $direct = $this->getRequest()->getParam('direct');
    
        $page = $this->getRequest()->getParam('p');
    
        if( !isset($sort) || !isset($direct ) ){
            return $this->_redirect('index');
        }
    
        if( isset( $page ) && $page >= 1 ) {
            $data['p'] = $page;
        }
    
        if( is_string ($sort) ) {
            $data['sort'] = strtolower( trim( $sort) );
        }
    
        if( is_string( $direct ) ) {
            $data['direct'] = strtolower( trim( $direct) );
        }
    
        $this->loadLayout();
    
        $tableHtml = $this->getLayout()->getBlock('salon_reservation_ajax_sort')->setData( $data )->toHtml();
    
        $direct = Mage::helper('salon')->getSort( $direct );
    
        echo json_encode( array(
                'data'        => $tableHtml,
                'direct'    => $direct,
        ) );
            
        return;
    
            
    }
}