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
class Salore_Salon_ClassifiedController extends Mage_Core_Controller_Front_Action
{
    public function reservationAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function newAction() {
         $this->_forward('edit'); 
    }
    public function editAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function saveAction() {
        $flag = true;
        $data = $this->getRequest ()->getParams ();
        $postsObj = Mage::getModel ( 'classified/posts' );
        try {
            if( isset ($data['id']) && $data['id'])
            {
                $postsObj->load($data['id']);
            }
            else 
            {
                $lastEntityId = Mage::helper ( 'salon' )->getLastIdCategory ( $postsObj );
                $postsObj->setData ( 'entity_id', ( string ) ($lastEntityId + 1) );
            }
            
            if (isset ( $data ['title'] ) && $data ['title']) {
                $postsObj->setData ( 'title', $data ['title'] );
            }
            if (isset ( $data ['category'] ) && $data ['category']) {
                $postsObj->setData ( 'category', $data ['category'] );
            }
            if (isset ( $data ['description'] ) && $data ['description']) {
                $postsObj->setData ( 'description', $data ['description'] );
            }
            if (isset ( $data ['expired_date'] ) && $data ['expired_date']) {
                $postsObj->setData ( 'expired_date', $data ['expired_date'] );
            }
            if( isset($data['images']) && is_array( $data['images'] ) ){
                $this->setImageDataForProduct( $data, $postsObj);
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $postsObj->setData('create_date' ,  Mage:: getModel( 'core/date')->timestamp(time()));
            $postsObj->setData('customer_id' ,  (int)$customerId);
            $postsObj->save ();
            Mage::getSingleton ( 'core/session' )->addSuccess ( 'This posts have saved succesfully!' );
            $this->_redirect ( '*/*/' );
        } catch ( Exception $e ) {
                
            $this->failToSaveData ( $data );
        }
    }
        
    public function setImageDataForProduct( $requestData, &$postsObj ){
        
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
        
        $postsObj->setData( 'images', $imgData) ;
        
        if( $requestData['thumbImg'] ){
            
            $thumb = $requestData['thumbImg'];
            
            if( isset($requestData['images'][$thumb] ) ) {
    
                $postsObj->setData('thumbnail_image', array( 'idImage' =>$thumb, 'url' => $requestData[ 'images'][ $thumb] ) );
            }
            
        } else {
            
            $postsObj->setData('thumbnail_image', array() );
        }
    
        if( $requestData['baseImg'] ){
            
            $base = $requestData['baseImg'];
                
            if( isset($requestData['images'][$base] ) ) {
    
                $postsObj->setData('base_image', array( 'idImage' =>$base, 'url' => $requestData[ 'images'][ $base] ) );
            }
        
        } else {
            
            $postsObj->setData('base_image', array() );
        }
    }
    
    public function failToSaveData($data) {
        
        Mage::getSingleton ( "adminhtml/session" )->setSessionDataExist ( $data );
        
        Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please fill all field with value.' ) );
        
        $this->_redirect ( '*/*/edit' );
    }
    
    public function ajaxDeleteImgAction() {
    
        $idImg = $this->getRequest()->getParam('id');
        $srcImg = $this->getRequest()->getParam('src');
        if( isset( $idImg) && $idImg ) {
                
            echo json_encode( Mage::helper('salon/image')->ajaxDeleteImgAction($idImg, $srcImg, 'classified', Mage::getModel('classified/posts') ) );
            return;
                
        } else {
    
            $reponse['status'] = 'ERROR';
            $reponse['message'] ="Image does not exist in the gallery";
            echo json_encode($reponse);
            return ;
        }
    }
    
    public function uploadAction() {
    
        if ( isset($_FILES) && count($_FILES) > 0 ) {
            echo json_encode( Mage::helper('salon/image')->processImgaUploaded( $_FILES, 'classified' ) );
            return;
        }
        echo json_encode( array( 'error' => $this->__('Cannot find any image Data!')) );
        return ;
    }
    
    public function ajaxdeleteAction() {
        $response = array();
        $deleted = false;
        $count = 0;
        $classifiedId = Mage::app()->getRequest()->getParam('id');
        $classifiedMongo = Mage::getModel('classified/posts');
        
        if( is_array( $classifiedId ) && count( $classifiedId ) > 0 ){
            
            $classifiedIds = $classifiedId;
        
        } else {
            
            if (isset($classifiedId) && $classifiedId) {
                
                $classifiedIds = array($classifiedId);
            }
        }
        
        foreach ( $classifiedIds as $id ) {
            
            $classifiedMongo = $classifiedMongo->load($id, 'entity_id');
            
            if ( $classifiedMongo->getEntityId() ) {
                try {
                    
                    $classifiedMongo->delete();
                    
                    $count ++;
                    
                    $deleted = true;
                
                } catch (Exception $e) {
                    
                    $response['status'] = 'ERROR';
                    
                    $response['message'] = $e->getMessage();
                    
                    echo json_encode($response);
                    
                    return ;
                }
            }
        }
        if( $deleted === true) {
            
            Mage::getSingleton('core/session')->addSuccess("{$count} Classified have deleted from your posts!");
            
            $this->loadLayout();
                
            $paginationHtml = $this->getLayout()->getBlock('classified_delete_ajax_pagination')->setData('page', $this->getRequest()->getParam('p'))->toHtml();
            
            $response['status'] = 'SUCCESS';
            
            $response['message'] = "Classified have deleted from your posts!";
            
            $response['pagination'] = $paginationHtml;
            
            echo json_encode($response);
            
            return;
        
        } else {
            
            $response['status'] = 'ERROR';
            
            $response['message'] = 'System error. Please check your php log file fore more detail.';
            
            echo json_encode($response);
            
            return ;
        }
        
    }
}