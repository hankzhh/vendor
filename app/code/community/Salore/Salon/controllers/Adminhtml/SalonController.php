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
class Salore_Salon_Adminhtml_SalonController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function _initData() {
        Mage::register('salon_obj', Mage::getModel('salon/salon'));
        $salonId = Mage::app()->getRequest()->getParam('salonId');
        if (isset($salonId) && $salonId) {
            Mage::registry('salon_obj')->load($salonId, 'entity_id');
        }
    }
    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('salon/salon')->load($id);
        if ($model->getEntityId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
        
            Mage::register('salon_data', $model);
            $this->loadLayout();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Salon Manager'), Mage::helper('adminhtml')->__('Salon Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Salon News'), Mage::helper('adminhtml')->__('Salon News'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('salon/adminhtml_salon_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salon')->__('Salon does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction() {
        
    }
    
    public function ajaxDeleteAction() {
        $this->_initData();
        $responses = array();
        $salonObj = Mage::registry('salon_obj');
        if ($salonObj->getEntityId()) {
            if (!$salonObj->getApprove()) {
                try {
                    $salonName = $salonObj->getSalonName();
                    $salonObj->delete();
                    $responses['status'] = 'SUCCESS';
                    
                    //Dispath an event for salon approved
                    Mage::dispatchEvent('salore_salon_deleted_after', array('salon'=>$salonObj));
                    
                    $responses['message'] = "Salon {$salonName} have deleted successfully!";
                    echo json_encode($responses);
                    return ;
                } catch (Exception $e) {
                    $responses['status'] = 'ERROR';
                    $responses['message'] = $e->getMessage();
                    echo json_encode($responses);
                    return ;
                }
            }
            else {
                if ($this->deleteCustomerInMagento($salonObj)) {
                    try {
                        $salonObj->delete();
                        $responses['status'] = 'SUCCESS';
                        //Dispath an event for salon approved
                        Mage::dispatchEvent('salore_salon_deleted_after', array('salon'=>$salonObj));
                        $responses['message'] = 'This salon have deleted successfully!';
                        echo json_encode($responses);
                        return ;
                    } catch (Exception $e) {
                        $responses['status'] = 'ERROR';
                        $responses['message'] = $e->getMessage();
                        echo json_encode($responses);
                        return ;
                    }
                }
                else {
                    $responses['status'] = $this->__('ERROR');
                    $responses['message'] = $this->__('The system may be meet problem when deleting customer in magento!');
                    echo json_encode($responses);
                    return ;
                }
            }
        }
        $responses['status'] = $this->__('ERROR');
        $responses['message'] = $this->__('This salon is not exist!');
        echo json_encode($responses);
        return ;
    }
    public function addCoordinatesToSalon(&$salon) {
        $salonAddress = $salon->getAddress().', '.$salon->getPostcode().' '.$salon->getCity().' '.$salon->getRegion().', '.$salon->getCountryId();
        $coordinates = Mage::helper('salon/google')->getCoordinates($salonAddress);
        $salon->setData('lat', '0');
        $salon->setData('lng', '0');
        if (isset($coordinates['lat'])) {
            $salon->setData('lat', $coordinates['lat']);
        }
        if (isset($coordinates['lng'])) {
            $salon->setData('lng', $coordinates['lng']);
        }
    }
    public function ajaxApproveAction() {
        $ajax = $this->getRequest()->getParam('isAjax');
        $this->_initData();
        $responses = array();
        $salonObj = Mage::registry('salon_obj');
        if ( $salonObj->getEntityId() ) {
            if(!is_numeric($salonObj->getApprove())) {
                $tempPassword = Mage::helper('salon')->generatePassword();
                //$tempPassword = '123456';
                $salonObj->setData('approve', 1);
                $salonObj->setData('password', $tempPassword);
                $this->addCoordinatesToSalon($salonObj);
                try {
                    if($this->saveIntoMagentoAsCustomer($salonObj, $tempPassword)) {
                        $salonObj->save();
                        Mage::register('currentsalon', $salonObj);
                        $pageId = uniqid();
                        $this->createAboutusPage($pageId);
                        Mage::getModel('salon/menu')->generateDefaultMenu($pageId);
                        Mage::getModel('salon/staff')->createSampleStaff();
                        //Prepare salon_url and salon admin url for email
                        $salonFrontUrl = trim(Mage::getBaseUrl(),'/').'/'.$salonObj->getData('salon_url');
                        $salonObj->setData('salon_front_url', $salonFrontUrl);
                        $salonObj->setData('salon_admin_url', $salonFrontUrl.'/admin');
                        Mage::helper('salon/mail')->sendEmail($salonObj, 'salon_send_account_info_email_template');
                        $this->createSampleData($salonObj ,Mage::getModel('salon/service') , 'Service' , 'service');
                        $this->createSampleData($salonObj , Mage::getModel('salon/product') , 'Product' ,'product');
                        $this->createDefaultFooter($salonObj);
                        $responses['status'] = $this->__('APPROVE');
                        $responses['message'] = $this->__('This salon have approved successfully!');
                        
                        //Dispath an event for salon approved
                        Mage::dispatchEvent('salore_salon_approved_after', array('salon'=>$salonObj));
                        
                        if( !isset($ajax) || false === $ajax || strlen($ajax) <= 0 ) {
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salon')->__('Salon %s has been approve successfully!', $salonObj->getSalonName()));
                            return $this->_redirect('*/*');
                        }
                        echo json_encode($responses);
                        return ;
                    }
                    else {
                        $responses['status'] = $this->__('ERROR');
                        $responses['message'] = $this->__('Have a problem when save customer. Please check in log file!');
                        if( !isset($ajax) || false === $ajax || strlen($ajax) <= 0 ) {
                    
                            Mage::getSingleton('adminhtml/session')->addError($responses['message']);
                            return $this->_redirect('*/*');
                        }
                        echo json_encode($responses);
                        return ;
                    }
                } catch (Exception $e) {
                    $responses['status'] = $this->__('ERROR');
                    $responses['message'] = $this->__($e->getMessage());
                    if( !isset($ajax) || false === $ajax || strlen($ajax) <= 0 ) {
                            
                        Mage::getSingleton('adminhtml/session')->addError($responses['message']);
                        return $this->_redirect('*/*');
                    }
                    echo json_encode($responses);
                    return ;
                }
            }
            elseif (0 == $salonObj->getApprove()) {
                $salonObj->setData('approve', 1);
                $this->addCoordinatesToSalon($salonObj);
                try {
                    $salonObj->save();
                    $responses['status'] = $this->__('APPROVE');
                    $responses['message'] = $this->__('This salon have approved successfully!');
                    
                    //Dispath an event for salon approved
                    Mage::dispatchEvent('salore_salon_approved_after', array('salon'=>$salonObj));
                    
                    if( !isset($ajax) || false === $ajax || strlen($ajax) <= 0 ){
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salon')->__('Salon %s has been approve successfully!', $salonObj->getSalonName()));
                        return $this->_redirect('*/*');
                    }
                    echo json_encode($responses);
                    return ;
                } catch (Exception $e) {
                    $responses['status'] = $this->__('ERROR');
                    $responses['message'] = $this->__($e->getMessage());
                    if( !isset($ajax) || false === $ajax || strlen($ajax) <= 0 ) {
                            
                        Mage::getSingleton('adminhtml/session')->addError($responses['message']);
                        return $this->_redirect('*/*');
                    }
                    echo json_encode($responses);
                    return ;
                }
            }
            else {
                $salonObj->setData('approve', 0);
                try {
                    $salonObj->save();
                    $responses['status'] = $this->__('UNAPPROVE');
                    $responses['message'] = $this->__('This salon have unapproved successfully!');
                    
                    Mage::dispatchEvent('salore_salon_unapproved_after', array('salon'=>$salonObj));
                    
                    echo json_encode($responses);
                    return ;
                } catch (Exception $e) {
                    $responses['status'] = $this->__('ERROR');
                    $responses['message'] = $this->__($e->getMessage());
                    echo json_encode($responses);
                    return ;
                }
            }
        }
        $responses['status'] = $this->__('ERROR');
        $responses['message'] = $this->__('This salon is not exist!');
        if( !isset($ajax) || false === $ajax || strlen($ajax) <= 0 ) {
                
            Mage::getSingleton('adminhtml/session')->addError($responses['message']);
            return $this->_redirect('*/*');
        }
        echo json_encode($responses);
        return ;
    }
    public function saveIntoMagentoAsCustomer(&$salon, $tempPassword) {
        $code = Mage::helper('salon')->transformCode($salon->getSalonUrl(), $salon->getEntityId());
        //create website 
        $website = Mage::getModel('core/website');
        $website->setData(array('code' => $code, 'name' => $salon->getSalonName()));
        $website->save();
        //create store group
        $storeGroup = Mage::getModel('core/store_group');
        $storeGroup->setData(array('website_id' => $website->getId(), 'name' => $salon->getSalonUrl(), 'root_category_id' => 0, 'default_store_id' => 0));
        $storeGroup->save();
        //create store view
        $storeView = Mage::getModel('core/store');
        $storeView->setData(array('code' => $code, 'group_id' => $storeGroup->getId(), 'website_id' => $website->getId(), 'name' => $salon->getSalonName(), 'is_active' => 1));
        $storeView->save();
        $salon->setData('website_id', $website->getId());
        $salon->setData('store_id', $storeView->getId());
        // create working time
        $days = array('Sunday' => array('timestart' => '07:00' , 'timeend' => '18:00') ,
                      'Monday'  => array('timestart' => '07:00' , 'timeend' => '18:00') , 
                      'Tuesday' => array('timestart' => '07:00' , 'timeend' => '18:00')  , 
                       'Wednesday' => array('timestart' => '07:00' , 'timeend' => '18:00') , 
                        'Thursday' => array('timestart' => '07:00' , 'timeend' => '18:00')  , 
                       'Friday' => array('timestart' => '07:00' , 'timeend' => '18:00')  ,
                        'Saturday'=> array('timestart' => '07:00' , 'timeend' => '18:00')  
                     );
        $salon->setData('workingtime' , $days);
        // save customer to magento
        try {
            
            $customerObj = Mage::getModel('customer/customer');
            $customerAddress = Mage::getModel('customer/address');
            $email = $salon->getEmail();
            $customerObj->setWebsiteId($website->getId());
            $customerObj->loadByEmail($email);
            $customerObj->setStore($storeView)
            ->setFirstname($salon->getFirstname())
            ->setLastname($salon->getLastname())
            ->setEmail($salon->getEmail())
            ->setPassword($tempPassword);
            $customerObj->save();
            
            $_customer_address = array (
                    'firstname' => $salon->getFirstname(),
                    'lastname' => $salon->getLastname(),
                    'street' => array (
                            '0' => $salon->getAddress(),
                            '1' => '',
                    ),
                    'city' => $salon->getCity(),
                    'region_id' => $salon->getRegionId(),
                    'region' => $salon->getRegion(),
                    'postcode' => $salon->getPostcode(),
                    'country_id' => $salon->getCountryId(),
                    'telephone' => $salon->getTelephone(),
            );
            $customerAddress->setData($_customer_address)
            ->setCustomerId($customerObj->getId())
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');
            $customerAddress->save();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    
    public function deleteCustomerInMagento($salon) {
        try {
            //Find and drop collections which prefix = salonurl_
            Mage::getModel('salon/salon')->dropSalonCollections($salon);
            
            if (!$model = Mage::getModel('core/website')->load($salon->getWebsiteId())) {
                return false;
            }
            if (!$model->isCanDelete()) {
                return false;
            }
            $customer = Mage::getModel('customer/customer');
            $customer->setWebsiteId($salon->getWebsiteId());
            $customer->loadByEmail($salon->getEmail());
            $customer->cleanAllAddresses();
            $customer->delete();
            //delete websiteID
            $model->delete();
        } catch (Exception $e) {
            return false;
        }
        return true;
         
    }
    public function createSampleData($salon , $model , $type , $name) {
        for($i = 1; $i <= 4; $i++) {
            if($type == "Service" && $name == "service") {
                $imageservice = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/salon/'.'default/'.'images/'."sample/{$name}{$i}.png";
                $model->setData('duration', 30);
                $model->setData('service_name', "Sample {$type} {$i}");
                $model->setData('image_service', $imageservice);
                
            }
            else {    
                $imageproduct = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/salon/'.'default/'.'images/'."sample/{$name}{$i}.jpeg";
                $model->setData('product_name', "Sample {$type} {$i}");
                $model->setData('image_product', $imageproduct);
            }
              $model->setData('entity_id',uniqid());
              $model->setData('salon_id', $salon->getEntityId());
              $model->setData('price', 10);
              $model->setData('description', 'This is description');
              $model->setData('short_description', 'This is short description');
              $model->setData('created_at', strtotime ( 'now' ));
              $model->setData('store_id', $salon->getStoreId());
              $model->setData('display', 1);
              try {
                $model->save();
             } catch (Exception $e) {
                throw $e;
            } 
        }
    }
    public function createDefaultFooter($salonObj) {
        $footerModel = Mage::getModel('salon/footer');
        $content = array();
        $footerModel->setData('entity_id', 'footer_250190');
        $footerModel->setData('title', 'Our Working Time');
        $footerModel->setData('system', 1);
        $footerModel->setData('active', 1);
        $footerModel->setData('position', 0);
        $workingTime = $salonObj->getWorkingtime();
        $i = 0;
        foreach ($workingTime as $day => $timeframe) {
            $content[$i]['item'] = "{$day} {$timeframe['timestart']}-{$timeframe['timeend']}";
            $i++;
        }
        $footerModel->setData('content', $content);
        try {
            $footerModel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function createAboutusPage($pageId) {
        $pageModel = Mage::getModel('salon/page');
        try {
            $pageModel->setData('entity_id' , $pageId);
            $pageModel->setData('title' , 'About Us');
            $pageModel->setData('active', 1);
            $content = '<div class="para-wrap"><ul><li><span">This is a example aboutus page '.
            '</span></li></ul>'.'</div>';
            $pageModel->setData('content' , $content);
            $pageModel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
            return ;
        }
    }
}
