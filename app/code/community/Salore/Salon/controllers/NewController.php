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
class Salore_Salon_NewController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * add new salon from form data to mongodb 
     */
    public function saveAction() {
        $error = array('message' => null);
        $salonData = Mage::app()->getRequest()->getParams();
        $salon = Mage::getModel('salon/salon');
        $customerobj = Mage::getModel('customer/customer');
        $url = Mage::getUrl('salon/new');
        if ($this->checkAllowProcess($salon, $salonData, $error)) {
            $salonUrlExisting = Mage::getModel('salon/salon')->checkSalonUrlExists($salonData['salon_url']);
            $salonEmailExisting = Mage::getModel('salon/salon')->checkSalonEmailExists($salonData['email']);
            if(!$salonUrlExisting && !$salonEmailExisting) {
                try {
                    $salon->save();
                    Mage::getSingleton('core/session')->setSuccessSavedNewSalon($salon);
                    $this->_redirect('*/new/success');
                    Mage::helper('salon/mail')->sendEmail($salon, 'salon_register_newsalon_email_template');
                    Mage::helper('salon/mail')->sendEmailForModerator($salon);
                    $url = Mage::getUrl('salon/new/success');
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
            }
            else {
                Mage::getSingleton('core/session')->setSessionFormData($salonData);
                Mage::getSingleton('core/session')->addError($this->__('This email has already registered a salon, please try another email!'));
            }
        }
        else  {
            Mage::getSingleton('core/session')->setSessionFormData($salonData);
            Mage::getSingleton('core/session')->addError($error['message']);
        }
        $this->_redirectUrl($url);
    }
    /**
     * set data before adding new salon
     * @param array $salonData
     * @param object $salon
     * @param array $error
     * @return true|false
     */
    public function checkAllowProcess(&$salon, $salonData, &$error) {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if($customerId = $customer->getId()) {
            $salon->setData('customer_id',  $customerId);
        }
        $salon->setData('entity_id', uniqid('salon_'));
        if (isset($salonData['firstname']) && $salonData['firstname']) {
            $salon->setData('firstname', $salonData['firstname']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter your firstname.').'</li>';
        }
        
        if (isset($salonData['lastname']) && $salonData['lastname']) {
            $salon->setData('lastname', $salonData['lastname']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter your lastname.').'</li>';
        }
        
        if (isset($salonData['email']) && $salonData['email']) {
            $salon->setData('email', $salonData['email']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter your email.').'</li>';
        }
        
        if (isset($salonData['salon_url']) && $salonData['salon_url']) {
            $salon->setData('salon_url', Mage::helper('salon')->transportText($salonData['salon_url']));
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter your salon url.').'</li>';
        }
        
        if (isset($salonData['salon_name']) && $salonData['salon_name']) {
            $salon->setData('salon_name', $salonData['salon_name']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter your salon name.').'</li>';
        }
        
        if (isset($salonData['address']) && $salonData['address']) {
            $salon->setData('address', $salonData['address']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter your salon address.').'</li>';
        }
        
        if (isset($salonData['city']) && $salonData['city']) {
            $salon->setData('city', $salonData['city']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter city.').'</li>';
        }
        
        if (isset($salonData['region_id']) && $salonData['region_id']) {
            $salon->setData('region_id', $salonData['region_id']);
            $region = Mage::getModel('directory/region')->load($salonData['region_id'])->getName();
            $salon->setData('region', $region);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please select region.').'</li>';
        }
        
        
        if (isset($salonData['postcode']) && $salonData['postcode']) {
            $salon->setData('postcode', $salonData['postcode']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter postcode.').'</li>';
        }
        if (isset($salonData['category']) && $salonData['category']) {
            $salon->setData('category', $salonData['category']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please select category.').'</li>';
        }
        $salon->setData('country_id', 'US');
        
        if (isset($salonData['telephone']) && $salonData['telephone']) {
            $salon->setData('telephone', $salonData['telephone']);
        }
        else {
            $error['message'] .= '<li>'.$this->__('Please enter your telephone.').'</li>';
        }
        $salon->setData('created_at' ,Mage::app()->getLocale()->storeTimeStamp());
        $salon->setData('updated_at' ,Mage::app()->getLocale()->storeTimeStamp());
        if(isset($error['message'])) {
            $error['message'] = '<ul class="list-unstyled">'. $error['message'] . '</ul>';
            return false;
        }
        return true;
    }
    public function successAction() {
        $salon = Mage::getSingleton('core/session')->getSuccessSavedNewSalon();
        if (!$salon) {
            $this->_redirect('*/new');
        }
        else {
            $this->loadLayout();
            $this->renderLayout();
        }
    }
} 