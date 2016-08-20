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
class Salore_Salon_Block_Customer_Classified_New extends Mage_Core_Block_Template {
    /**
      * get staff data of table staff from mongodb
      * @return array
      */
    public function getclassifiedMongo()  {
        $classifiedId = $this->getRequest()->getParam('id');
        $classifiedMongo = Mage::getModel('classified/posts');
        if ( $classifiedId ) {
            return $classifiedMongo->load($classifiedId, 'entity_id');
        }
        return $classifiedMongo;
    }
    /**
     * get Action Name
     * @return string
     */
    public function getActionName() {
        return Mage::app()->getRequest()->getActionName();
    }
    /**
     * get Action save on template form staff
     * @return string url
     */
    public function getActionForForm() {
        return Mage::helper('salon')->getUrl('salon/classified/save');
    }
    public function getBlockUploadImg($collection) {
        $spec = array();
        $imgData = array();
        $id = $this->getRequest()->getParam('id');
        if(is_array($collection)) {
            $id = $this->getRequest()->getParam('id');
            if(isset($id) && $id)
            {
                if( $collection->getImages() && count($collection->getImages() ) ){
                    $imgData = $collection->getImages();
                
                    $spec = $this->getSpecificImage( $collection);
                }
                $collection = Mage::getModel('classified/posts')->load($id);
            }
            else {
                $collection = Mage::getModel('classified/posts')->getCollection();
            }
            
        }else{
            if(isset($id) && $id) {
                if( $collection->getImages() && count($collection->getImages() ) ){
                    $imgData = $collection->getImages();
            
                    $spec = $this->getSpecificImage( $collection);
                }
                $collection = Mage::getModel('classified/posts')->load($id);
            }
        }
        $uploaderBlock = $this->getLayout()->createBlock('Salore_Salon_Block_Admin_Image_Uploader','img_upload')->setTemplate('salore/salon/admin/image/uploader.phtml');
        $arrayData = array(
                'imgData' => $imgData,
                'uploadUrl'    => Mage::helper('salon')->getUrl('salon/classified/upload'),
                'saveUrl'    => Mage::helper('salon')->getUrl('salon/classified/save'),
                'deleteUrl'    => Mage::helper('salon')->getUrl('salon/classified/ajaxDeleteImg'),
        );
        if( count($spec ) ) {
            foreach( $spec as $key => $value ) {
    
                $arrayData[$key] = $value;
            }
        }
        $uploaderBlock->setData('collection', $arrayData);
        return $uploaderBlock->toHtml();
    }
    
    public function getSpecificImage($collection) {
        $rs = array();
        if( $base = $collection->getBaseImage() ) {
            $rs['baseImg'] = $base['idImage'];
        }
        if( $base = $collection->getThumbnailImage() ) {
            $rs['thumbImg'] = $base['idImage'];
        }
        return $rs;
    }
    public function getCategoryMongo() {
        return Mage::getModel('classified/category')->getCollection();
    }
    public function getDisable()
    {
        $action =  Mage::app()->getRequest()->getActionName();
        if($action === 'edit')
        {
            $this->disable = 'disabled';
        }
        return $this->disable;
    }
}