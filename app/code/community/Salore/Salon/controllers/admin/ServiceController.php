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

class Salore_Salon_Admin_ServiceController extends Salore_Salon_Admin_BaseController
{
    protected $editImage = false;
    protected $id  = null;
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editAction() {
        
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Edit Your Service!' ) );
        $this->renderLayout ();
    }
    public function newAction() {
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'New Service!' ) );
        $this->renderLayout ();
    }
    /**
     * save service information to mongodb from form data
     */
    protected function saveAction() {
        $salonObj = Mage::registry('currentsalon');
        $serviceMongo = Mage::getModel ( 'salon/service' );
        $serviceId = $this->getRequest ()->getParam ( 'sid' );
        $serviceMongo = $serviceMongo->load ( $serviceId );
        if ($this->getRequest ()->isPost ()) {
            $serviceParams = $this->getRequest()->getParams();
            $this->savingAfterSetData($serviceMongo, $serviceParams, $salonObj);
        }
    }
    /**
     * save product information to magento and mongodb after set full data
     * @param string $attributeSetId
     * @param object $product
     * @param array $serviceParams
     * @param object $salonObj
     */
    public function savingAfterSetData($serviceMongo, $serviceParams, $salonObj) {
    
        $salonId = $salonObj->getEntityId();
        if (isset($serviceParams['title']) && $serviceParams['title'] && isset($serviceParams['price']) && is_numeric($serviceParams['price']) && isset($serviceParams['duration']) && is_numeric($serviceParams['duration']) && isset($serviceParams['short_description']) && $serviceParams['short_description'] && isset($serviceParams['description']) && $serviceParams['description']) {
            if ($serviceMongo->getEntityId()) {
                $serviceMongo->setData('updated_at' , strtotime ( 'now' ) );
                if( $serviceImages = $serviceMongo->getImages() ) {
                    $this->editImage = true;
                }
                //edit magento product if exist                
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $serviceMongo->getEntityId());
                if(is_object($product) && $product->getId()){
                    $this->editProductMagento($product, $serviceParams, $salonObj);
                }
            }
            else {
                $serviceMongo->setData('entity_id', uniqid());
            }
            $serviceMongo->setData('salon_id', $salonId);
            $serviceMongo->setData('service_name', $serviceParams['title']);
            $serviceMongo->setData('duration', (int) $serviceParams['duration']);
            $serviceMongo->setData('price', (int)$serviceParams['price']);
            if (isset($serviceParams['special_price']) && $serviceParams['special_price']) {
                $serviceMongo->setData('special_price', (int)$serviceParams['special_price']);
                if ( isset( $serviceParams['special_from_date']) && $serviceParams['special_from_date']) {
                    $specialFromDateTimestamp = Mage::getModel('core/date')->timestamp( $serviceParams['special_from_date'].' 00:00:00');
                    $serviceMongo->setData('special_from_date', $specialFromDateTimestamp);
                }
                if ( isset( $serviceParams['special_to_date']) && $serviceParams['special_to_date']) {
                    $specialToDateTimestamp = Mage::getModel('core/date')->timestamp( $serviceParams['special_to_date'].' 00:00:00');
                    $serviceMongo->setData('special_to_date',$specialToDateTimestamp);
                }
            }
            $serviceMongo->setData('description', $serviceParams['description']);
            $serviceMongo->setData('short_description', $serviceParams['short_description']);
            if(isset($serviceParams['display']) && $serviceParams['display']) {
                $serviceMongo->setData('display', 1);
            }
            else {
                $serviceMongo->setData('display', 0);
            }
            try {
                $time = strtotime('now');
                $serviceMongo->setData('created_at', $time);
                if( !$serviceMongo->getData( 'updated_at' ) ) {
                    $serviceMongo->setData( 'updated_at', $time );
                }
                if( $this->editImage && !isset($serviceParams['images'] ) ){
                    $arr = array('images', 'thumbnail_image', 'base_image');
                    foreach ($arr as $field) {
                        $serviceMongo->setData( $field, array() );
                    }
                }
                if( isset($serviceParams['images']) && is_array( $serviceParams['images'] ) ){
                    $this->setImageDataForService( $serviceParams, $serviceMongo);
                }
                $serviceMongo->save();
                Mage::getSingleton('core/session')->addSuccess($this->__('Add new service successfully!'));
                $url = Mage::helper('salon')->getUrl('admin/service');
                $this->_redirectUrl ($url);
            }
            catch ( Exception $exeption ) {
                Mage::getSingleton('core/session')->addError($exeption->getMessage ());
                $url = Mage::getSingleton('core/session')->getRedirectUrl();
                Mage::getSingleton('core/session')->unsRedirectUrl('url');
                $this->_redirectUrl($url);
            }
        } else {
            Mage::getSingleton('core/session')->addError('Please fill fully and exactly information!');
            $url = Mage::getSingleton('core/session')->getRedirectUrl();
            Mage::getSingleton('core/session')->unsRedirectUrl('url');
            $this->_redirectUrl($url);
        }
    }    
    public function editProductMagento($product, $serviceParams, $salon) 
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        if (isset($serviceParams['title']) && !empty($serviceParams['title'])) {
            $product->setName ( $serviceParams['title'] );
        }
        if (isset($serviceParams['duration']) && !empty($serviceParams['duration'])) {
            $product->setDuration ( $serviceParams['duration'] );
        }
        if (isset($serviceParams['price']) && !empty($serviceParams['price'])) {
            $product->setPrice ( $serviceParams['price'] );
        }
         if (isset($serviceParams['special_price']) && !empty($serviceParams['special_price'])) {
            $product->setSpecialPrice ( $serviceParams['special_price'] );
             if ( isset( $serviceParams['special_from_date']) && !empty($serviceParams['special_from_date']) ) {
                 $specialFromDateTimestamp = Mage::getModel('core/date')->timestamp( $serviceParams['special_from_date'].' 00:00:00');
                $product->setSpecialFromDate ( $serviceParams['special_from_date'] );
            }
             if ( isset( $serviceParams['special_to_date'] ) && !empty($serviceParams['special_to_date']))  {
                 $specialToDateTimestamp = Mage::getModel('core/date')->timestamp( $serviceParams['special_to_date'].' 00:00:00');
                 $product->setSpecialToDate ( $serviceParams['special_to_date'] );
             }
         }
        if(isset( $serviceParams['description'] )) {
            $product->setDescription( $serviceParams['description'] );
        }
        if(isset( $serviceParams['short_description'] )) {
            $product->setShortDescription( $serviceParams['short_description'] );
        }
        try {
            $product->save();
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function setImageDataForService( $requestData, &$serviceMongoObj ){
        
        $imgData = array();
        
        foreach($requestData['images'] as $idImg => $url){
                
            $largerUrl = str_replace('small', 'larger', $url);
            
            $imageName = explode('/', $url );
            
            $imageName = trim( array_pop( $imageName ) );
                
            $imgData[ $idImg ] = array(
                    'fileName'     => $imageName,
                    'smallUrl'    => $url,
                    'largerUrl' => $largerUrl,
            );
        }
    
        $serviceMongoObj->setData( 'images', $imgData) ;
        $serviceMongoObj->setData( 'largerUrl', $largerUrl) ;
        if( array_key_exists('thumbImg', $requestData ) && isset( $requestData['thumbImg'] ) ) {
                
            $thumb = $requestData['thumbImg'];
                
            if( isset($requestData['images'][$thumb] ) ) {
    
                $serviceMongoObj->setData('thumbnail_image', array( 'idImage' =>$thumb, 'url' => $requestData[ 'images'][ $thumb] ) );
            }
        } else {
            $serviceMongoObj->setData('thumbnail_image', array() );
        }
    
        if( $requestData['baseImg'] ){
            $base = $requestData['baseImg'];
                
            if( isset($requestData['images'][$base] ) ) {
    
                $serviceMongoObj->setData('base_image', array( 'idImage' =>$base, 'url' => $requestData[ 'images'][ $base] ) );
            }
        } else {
            $serviceMongoObj->setData('base_image', array() );
        }
    }
    
    /**
     * delete a record of service table from mongodb 
     */
    
    public function ajaxdeleteAction() {
        $response = array();
        $deleted = false;
        $serviceIds = array();
        $count = 0;
        $serviceId = Mage::app()->getRequest()->getParam('id');
        $serviceMongo = Mage::getModel('salon/service');
        $serviceMagento = Mage::getModel('catalog/product');
    
    
        if(is_array($serviceId) && count($serviceId) >0)
        {
            $serviceIds = $serviceId;
        }
        else
        {
            if ( isset( $serviceId) && 0 < $serviceId) {
                $serviceIds = array( $serviceId);
            }
        }
        foreach ($serviceIds as $id) {
            
            if ( isset( $id) && 0 < $id) {
                
                $serviceMongo = $serviceMongo->load( $id, 'entity_id');
                $serviceMagento = $serviceMagento->load( $id);
            }
                
            if ( $serviceMongo->getEntityId() ) {
                try {
                    
                    Mage::register('isSecureArea', true);
                    
                    $serviceName = $serviceMongo->getServiceName();
                    
                    $serviceMongo->delete();
                    
                    $serviceMagento->delete();
                    
                    $deleted = true;
                    
                    Mage::unregister('isSecureArea');
                
                } catch (Exception $e) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $e->getMessage();
                    echo json_encode($response);
                    return ;
                }
                $count ++;
            }
        }
        if( $deleted === true ) {
                
            Mage::getSingleton('core/session')->addSuccess("{$count} service have deleted from your salon!");
            
            $response['status'] = 'SUCCESS';
            
            $response['message'] = "{$serviceName} service have deleted from your salon!";
            
            echo json_encode($response);
            
            return;
        } else {
            
            Mage::getSingleton('core/session')->addSuccess("System error. Please check your php log file fore more detail.");
            
            $response['status'] = 'ERROR';
            
            $response['message'] = 'System error. Please check your php log file fore more detail.';
            
            echo json_encode($response);
            
            return ;
        }
    }
    
    public function uploadAction() {
        
        if ( isset($_FILES) && count($_FILES) > 0 ) {
            echo json_encode( Mage::helper('salon/image')->processImgaUploaded( $_FILES, 'service' ) );
            return;
        }
        echo json_encode( array( 'error' => $this->__('Cannot find any image Data!')) );
        return ;
    }
    
    public function ajaxDeleteImgAction() {
    
        $idImg = $this->getRequest()->getParam('id');
        $srcImg = $this->getRequest()->getParam('src');
        if( isset( $idImg) && $idImg ) {
                
            echo json_encode( Mage::helper('salon/image')->ajaxDeleteImgAction($idImg, $srcImg, 'service', Mage::getModel('salon/service') ) );
            return;
                
        } else {
    
            $reponse['status'] = 'ERROR';
            $reponse['message'] ="Image does not exist in the Service";
            echo json_encode($reponse);
            return ;
        }
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
    
        $tableHtml = $this->getLayout()->getBlock('salon_service_ajax_sort')->setData( $data )->toHtml();
    
        $direct = Mage::helper('salon')->getSort( $direct );
    
        echo json_encode( array(
                'data'        => $tableHtml,
                'direct'    => $direct,
        ) );
            
        return;
    
            
    }
}