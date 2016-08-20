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

class Salore_Salon_Admin_CustomerController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editAction() {
    
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Edit Your Customer!' ) );
        $this->renderLayout ();
    }
    public function newAction() {
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'New Customer!' ) );
        $this->renderLayout ();
    }
    public function prepareCustomerData($customerData, $customer) {
        
        if (isset($customerData['firstname']) && $customerData['firstname']) {
            $customer->setData('firstname', $customerData['firstname']);
        }
    
        if (isset($customerData['lastname']) && $customerData['lastname']) {
            $customer->setData('lastname', $customerData['lastname']);
        }
    
        if (isset($customerData['email']) && $customerData['email']) {
            $customer->setData('email', $customerData['email']);
        }
    
        if (isset($customerData['address']) && $customerData['address']) {
            $customer->setData('address', $customerData['address']);
        }
    
        if (isset($customerData['city']) && $customerData['city']) {
            $customer->setData('city', $customerData['city']);
        }
    
        if (isset($customerData['region_id']) && $customerData['region_id']) {
            $customer->setData('region_id', $customerData['region_id']);
            $region = Mage::getModel('directory/region')->load($customerData['region_id'])->getName();
            $customer->setData('region', $region);
        }
    
        if (isset($customerData['postcode']) && $customerData['postcode']) {
            $customer->setData('postcode', $customerData['postcode']);
        }
        if (isset($customerData['category']) && $customerData['category']) {
            $customer->setData('category', $customerData['category']);
        }
        $customer->setData('country_id', 'US');
    
        if (isset($customerData['telephone']) && $customerData['telephone']) {
            $customer->setData('telephone', $customerData['telephone']);
        }
        $customer->setData('created_at' ,Mage::app()->getLocale()->storeTimeStamp());
        $customer->setData('updated_at' ,Mage::app()->getLocale()->storeTimeStamp());
    }
    public function saveAction() {
        $customerData = Mage::app()->getRequest()->getParams();
        $customer = Mage::getModel('salon/customer');
        if (isset($customerData) && $customerData)  {
            if (isset($customerData['menuid']) && $customerData['menuid']) {
                $customer->load($customerData['menuid'], 'entity_id');
            }
            else {
                $entityId = uniqid();
                $customer->setData('entity_id', $entityId);
            }
                $this->prepareCustomerData($customerData, $customer);
                try {
                    $customer->save();
                    Mage::getSingleton('core/session')->addSuccess('Success Fully');
                    $url = Mage::helper('salon')->getUrl('admin/customer/');
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                    $this->_redirect('*/new');
                }
            
        }        
        else {
            Mage::getSingleton('core/session')->setSessionFormData($customerData);
            Mage::getSingleton('core/session')->addError($this->__('Please fill your customer information!'));
            $this->_redirect('*/new');
        }
    }
    public function ajaxdeleteAction() {
        $response = array();
        $customerId = Mage::app()->getRequest()->getParam('id');
        $customerMongo = Mage::getModel('salon/customer');
        if (isset($customerId) && $customerId) {
            $customerMongo->load($customerId , 'entity_id');
                try {
                    $customerMongo->delete();
                    $response['status'] = 'SUCCESS';
                    $response['message'] = " Customer was deleted from the your salon!";
                    echo json_encode($response);
                    return ;
                } catch (Exception $e) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $e->getMessage();
                    echo json_encode($response);
                    return ;
                }
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = 'This customer is already exist!';
            echo json_encode($response);
            return ;
        }
    }
}