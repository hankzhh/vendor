<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Classified to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action {    
    
    public function _initData() {
        $category_id = $this->getRequest()->getParam('id');
        Mage::register('category_data', Mage::getModel('classified/category'));
        if (isset( $category_id) && $category_id ) {
            Mage::registry('category_data')->load( $category_id );
        }
    }
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function newAction() {
        $this->_forward('edit');
    }
    public function editAction() {
        $this->_initData();
        $this->loadLayout()->_title ( $this->__ ( 'Category Manager' ) )
        ->_addContent ( $this->getLayout ()->createBlock ( 'classified/adminhtml_category_edit' ) )
        ->renderLayout();
    }
    public function saveAction() {
        $this->_initData();
        $data = $this->getRequest()->getParams();
        $categoryObj = Mage::getModel('classified/category');
        if(isset($data) && $data)
        {
            if(isset($data['id']) && $data['id']) {
                $categoryObj = $categoryObj->load($data['id']);
            }
            else if ( !$categoryObj->getEntityId()) {
                $categoryModel = Mage::getModel('classified/category');
                $lastEntityId = Mage::helper('salon')->getLastIdCategory($categoryModel);
                $categoryObj->setData('entity_id', (string)( $lastEntityId + 1 ));
            }
            if(isset($data['title']) && $data['title']) {
                $categoryObj->setData('title' ,$data['title']);
            }
            if( $data && !$_FILES['img_category']['name']  ) {
            
                $this->updateValuePostsImage($categoryObj);
                $flag = false;
            
            }
            if(isset($_FILES['img_category']['name']) && $_FILES['img_category']['name'] ) {
                $categoryImage = $_FILES['img_category']['name'];
                $path = Mage::getBaseDir('media').DS.'classified'.DS.'category'.DS ;
                $pathDelete = Mage::getBaseDir('media').DS.'classified'.DS.'category'.DS .$categoryImage;
                $categoryUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'classified/' .'category'. '/'  ;
                $categoryurlResize = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'classified/' .'category'. '/' .$categoryImage ;
                Mage::helper('salon')->createImageAfterUpload($categoryImage, 'img_category', $path);
                Mage::helper('salon')->resizeImage($path , $categoryImage , 800 , 600, 'img_rezize_' . $categoryImage);
                $categoryObj->setData('img_path' ,$pathDelete);
                $categoryObj->setData('img_path_resize' , $categoryUrl. 'img_rezize_' . $categoryImage);
                $categoryObj->setData('img_path_original' ,$categoryUrl . $categoryImage);
                
            }
            try {
                $categoryObj->save();
                Mage::getSingleton('core/session')->addSuccess('This category have saved succesfully!');
                $this->_redirect('*/*');
            } catch (Exception $e) {
                $this->failToSaveData( $data);
            }
        }
        else
        {
            Mage::getSingleton('core/session')->addError($this->__("Please Enter Full Information Category"));
        } 
    }
    public function failToSaveData( $data) {
        Mage:: getSingleton( "adminhtml/session" )->setSessionDataExist($data);
        Mage::getSingleton('core/session')->addError($this->__('Please fill all field with value.'));
        $this->_redirect('*/*/edit');
    }
    public function ajaxdeleteAction() {
        $responses = array();
        $id = Mage::app()->getRequest()->getParam('id');
        try {
            $categoryModel = Mage::getModel('classified/category')->load($id, 'entity_id');
            $imgPathDelete = $categoryModel['img_path_resize'];
            $this->recursiveDelete($imgPathDelete);
            $categoryModel->delete();
            $responses['status'] = 'SUCCESS';
            $responses['message'] = 'This  category have deleted successfully!';
            echo json_encode($responses);
    
        } catch (Exception $e) {
            $responses['status'] = 'ERROR';
            $responses['message'] = $e->getMessage();
            echo json_encode($responses);
        }
    
    }
    public function updateValuePostsImage(&$categoryObj) {
    
        $imgPath = Mage::registry('category_data')->getImgPath();
    
        $imgPathOriginal = Mage::registry('category_data')->getImgPathOriginal();
    
        $imgPathResize = Mage::registry('category_data')->getImgPathResize();
    
        $imgResize = Mage::registry('category_data')->getImgResize();
    
        $categoryObj->setData('img_path' ,$imgPath);
    
        $categoryObj->setData('img_path_resize' , $imgPathResize);
    
        $categoryObj->setData('img_path_original' ,$imgPathOriginal);
    
    }
    public function recursiveDelete($str) {
        if(is_file($str)) {
            return @unlink($str);
        }
        elseif(is_dir($str)) {
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path) {
                recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
}
