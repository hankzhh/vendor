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

class Salore_Salon_Admin_StaffController extends Salore_Salon_Admin_BaseController
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editAction() {
    
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Edit Your Staff!' ) );
        $this->renderLayout ();
    }
    public function newAction() {
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'New Staff!' ) );
        $this->renderLayout ();
    }
    /**
     * save staff information from form data to mongodb
     */
    protected function saveAction() {
        $staffId = $this->getRequest ()->getParam ('id');
        $staffMongo = Mage::getModel('salon/staff');
        $staffParams = $this->getRequest()->getParams();
        $staffMongo->load($staffId, 'entity_id');
        if (isset($staffParams['name']) && $staffParams['name'] && isset($staffParams['age']) && is_numeric($staffParams['age'])  && isset($staffParams['year']) && is_numeric($staffParams['year']) && isset($staffParams['sex']) && $staffParams['sex']) {
            $staffMongo->setData('entity_id', uniqid());
            if(isset($staffId) && $staffId) {
                $staffMongo->load($staffId , 'entity_id');
                if($staffMongo->getEntityId()) {
                    $staffMongo->setData('entity_id', $staffId);
                }
            }
            $this->setDataStaff( $staffMongo , $staffParams  );
            try {
                $staffMongo->save();
                Mage::getSingleton('core/session')->addSuccess($this->__('Add new staff successfully!'));
                $url = Mage::helper('salon')->getUrl('admin/staff');
                $this->_redirectUrl ($url);
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($exeption->getMessage ());
                $url = Mage::getSingleton('core/session')->getRedirectUrl();
                Mage::getSingleton('core/session')->unsRedirectUrl('url');
                $this->_redirectUrl($url);
            }
        }
        else {
            Mage::getSingleton('core/session')->addError($this->__('Please fill full information!'));
            $this->_redirectReferer();
        }
            
            
    }
    /**
     * set staff information and create avatar before saving
     * @param object $staffMongo
     * @param array $staffParams
     */
    public function setDataStaff( &$staffMongo , $staffParams  ) {
    
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $staffMongo->setData('name', $staffParams['name']);
            $staffMongo->setData('age', $staffParams['age']);
            $staffMongo->setData('year', $staffParams['year']);
            $staffMongo->setData('sex', $staffParams['sex']);
            $staffMongo->setData('created_at', strtotime ( 'now' ));
            if (isset($_FILES['img_avatar']['name']) && $_FILES['img_avatar']['name']) {
                $image_staff  = $_FILES['img_avatar']['name'];
                $image_Dir = $this->getImageDir($customerId, $image_staff);
                $pathRoot = Mage::getBaseDir('media').DS.'salore'. DS . 'img_avatar' . DS . $customerId . DS;
                Mage::helper('salon')->createImageAfterUpload($image_staff, 'img_avatar', $pathRoot);
                Mage::helper('salon')->resizeImage($pathRoot, $image_staff, 300, 200, $image_staff);
                $staffMongo->setData('img_avatar' , $image_Dir);
            }
                
    }
    /**
     * return a path contain image
     * @param string $serviceId
     * @param string $containFolder
     * @return string
     */
    public function getImageDir($serviceId, $containFolder) {
        return $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'salore/' .'img_avatar'. '/' . $serviceId . '/' .$containFolder ;
    }
    /**
     * delete a record of staff table in mongodb by ajax
     */
    public function ajaxdeleteAction() {
        $response = array();
        $staffId = Mage::app()->getRequest()->getParam('id');
        $staffMongo = Mage::getModel('salon/staff');
        if (isset($staffId) && $staffId) {
            $staffMongo = $staffMongo->load($staffId, 'entity_id');
        }
        if ($staffMongo->getEntityId()) {
            try {
                $staffMongo->delete();
                $response['status'] = 'SUCCESS';
                $response['message'] = "Staff have deleted from your salon!";
                echo json_encode($response);
                return;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $e->getMessage();
                echo json_encode($response);
                return ;
            }
        }
        $response['status'] = 'ERROR';
        $response['message'] = 'System error. Please check your php log file fore more detail.';
        echo json_encode($response);
        return ;
    }
    
}