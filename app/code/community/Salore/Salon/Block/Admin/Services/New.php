<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Admin_Services_New extends Mage_Core_Block_Template  {
    public $disable ='';
    /**
     * get service data of table service from mongodb
     * @return array
     */
    public function getServicefromMongo()  {
        $serviceId = $this->getRequest ()->getParam ( 'sid' );
        $serviceMongo = Mage::getModel('salon/service');
        if ( $serviceId ) {
            return $serviceMongo->load($serviceId, 'entity_id');
        }
        return $serviceMongo;
    }
    /**
     * get action name active
     * @return string
     */
    public function getActionName() {
        return Mage::app()->getRequest()->getActionName();
    }
    /**
     * get action save in template form service
     * @return string url
     */
    public function getActionForForm() {
        return Mage::helper('salon')->getUrl('admin/service/save');
    }
    public function getBlockUploadImg($collection) {
        $spec = array();
        $imgData = array();
    
        if( $collection->getImages() && count($collection->getImages() ) ){
                
            $imgData = $collection->getImages();
                
            $spec = $this->getSpecificImage( $collection);
        }
    
        $uploaderBlock = $this->getLayout()->createBlock('Salore_Salon_Block_Admin_Image_Uploader','img_upload')->setTemplate('salore/salon/admin/image/uploader.phtml');
        $arrayData = array(
                'imgData' => $imgData,
                'uploadUrl'    => Mage::helper('salon')->getUrl('admin/service/upload'),
                'saveUrl'    => Mage::helper('salon')->getUrl('admin/service/save'),
                'deleteUrl'    => Mage::helper('salon')->getUrl('admin/service/ajaxDeleteImg'),
        );
        $serviceId = $this->getRequest ()->getParam ( 'sid' );
        
        if( $serviceId ){
                
            $arrayData['edit'] = true;
        }
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
    public function getDisable() {
        $actions = array('edit','new');
        $action =  Mage::app()->getRequest()->getActionName();
        if( in_array( $action, $actions ) ) {
            $this->disable = 'disabled';
        }
        return $this->disable;
    }
}
