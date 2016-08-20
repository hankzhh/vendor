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
class Salore_Salon_Adminhtml_CityController extends Mage_Adminhtml_Controller_Action
{
    public function _initData() {
        $city_id = $this->getRequest()->getParam('id');
        Mage::register('solr_city', Mage::getModel('salon/city'));
        if (isset($city_id) && $city_id) {
            Mage::registry('solr_city')->load($city_id);
        }
    }
    public function indexAction() {
        $this->loadLayout()
             ->_setActiveMenu('salore/topcitimanager');
        $this->renderLayout();
    }
    public function saveAction() {
        $this->_initData();
        $data = $this->getRequest()->getParams();
        $cityObj = Mage::getModel('salon/city');
        if(isset($data['id']) && $data['id']) {
            $cityObj = $cityObj->load($data['id']);
        }
        if(isset($_FILES['img_city']['name']) && $_FILES['img_city']['name']) {
            $cityImage = $_FILES['img_city']['name'];
            $path = Mage::getBaseDir('media').DS.'salore'.DS.'city'.DS ;
            $pathDelete = Mage::getBaseDir('media').DS.'salore'.DS.'city'.DS .$cityImage;
            $cityUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'salore/' .'city'. '/' .$cityImage ;
            $cityurlResize = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'salore/' .'city'. '/' .$cityImage ;
            try {
                if (!$cityObj->getEntityId()) {
                    $cityObj->setData('entity_id', uniqid('city_'));
                }
                $cityObj->setData('city_name' ,$data['city_name']);
                $cityObj->setData('img_path' ,$pathDelete);
                $cityObj->setData('img_path_resize' ,$cityurlResize);
                $cityObj->save();
                Mage::helper('salon')->createImageAfterUpload($cityImage, 'img_city', $path);
                Mage::helper('salon')->resizeImage($path , $cityImage , 300 , 300 , $cityImage);
                Mage::getSingleton('core/session')->addSuccess('This city have saved succesfully!');
                $this->_redirect('*/*');
            } catch (Exception $e) {
                Mage:: getSingleton( "adminhtml/session" )->setSessionDataExist($data);
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('*/*/edit');
            }
        }
        else {
            Mage:: getSingleton( "adminhtml/session" )->setSessionDataExist($data);
            Mage::getSingleton('core/session')->addError($this->__('Please choose image'));
            $this->_redirect('*/*/edit');
        }
    }
    public function newAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        $this->_initData();
        $this->loadLayout()->_title ( $this->__ ( 'Top City Manager' ) )
                            ->_addContent ( $this->getLayout ()->createBlock ( 'salon/adminhtml_city_edit' ) )
                            ->renderLayout();
    }
    public function recursiveDelete($str) {
        if(is_file($str)) {
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path) {
                recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
    public function ajaxdeleteAction() {
        $responses = array();
        $id = Mage::app()->getRequest()->getParam('id');
        try {
            $cityModel = Mage::getModel('salon/city')->load($id, 'entity_id');
            $imgPathDelete = $cityModel->getData('img_path_resize');
            $this->recursiveDelete($imgPathDelete);
            $cityModel->delete();
            $responses['status'] = 'SUCCESS';
            $responses['message'] = 'This image city have deleted successfully!';
            echo json_encode($responses);
        
        } catch (Exception $e) {
            $responses['status'] = 'ERROR';
            $responses['message'] = $e->getMessage();
            echo json_encode($responses);
        }
        
    } 
}