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

class Salore_Salon_Admin_ProductController extends Salore_Salon_Admin_BaseController
{
    protected $editImage = false;
    protected $id  = null;
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editAction() {
    
        if ( $productId = $this->getRequest ()->getParam ( 'sid' ) && empty( $this->id ) ) {
            $this->id = (int)$productId;
        }        
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Edit Your Product!' ) );
        $this->renderLayout ();
    }
    public function newAction() {
        $this->loadLayout ();
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'New Product!' ) );
        $this->renderLayout ();
    }
    /**
     * save product information from form data to mongodb
     */
    protected function saveAction() {
        
        $salonObj = Mage::registry('currentsalon');
    
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $attributeSetId = Mage::helper ( 'salon' )->getAttributeSetIdNameDefault ();
    
        $product = Mage::getModel ( 'catalog/product' );
        $productId = $this->getRequest ()->getParam ( 'sid' );
        if (!$productId) {
                
            $product->setSku ( uniqid() );
                
            $product->setCategoryIds (
                    array(3, 10)
            );
            $product->setWebsiteIds (
                    array($salonObj->getWebsiteId())
            );
            $product->setStoreId ( $salonObj->getStoreId() );
            $product->setAttributeSetId ( $attributeSetId );
            $product->setTypeId ( 'simple' );
                
        } else {
            $product->load ( $productId );
        }
    
        if ($this->getRequest ()->isPost ()) {
            $productParams = $this->getRequest()->getParams();
            $this->savingAfterSetData( $product, $productParams, $salonObj);
        }
    }
    /**
     * save product information to magento and mongodb after set full data
     * @param string $attributeSetId
     * @param object $product
     * @param array $productParams
     * @param object $salonObj
     */
    public function savingAfterSetData( $product, $productParams, $salonObj) {
        $productMongo = Mage::getModel('salon/product');
        $salonId = $salonObj->getEntityId();
        if (isset($productParams['title']) && $productParams['title'] && isset($productParams['price']) && is_numeric($productParams['price'])  && isset($productParams['short_description']) && $productParams['short_description'] && isset($productParams['description']) && $productParams['description']) {
            if ($product->getId() && ( 0 < $product->getId() )) {
                
                if( $productDetail = $productMongo->load($product->getId(), 'entity_id' )  ){
                    
                    if( $productImages = $productDetail->getImages() ) {
                        $this->editImage = true;
                    }
                }
            }
            $productMongo->setData('salon_id', $salonId);
            $product->setName ( $productParams['title'] );
            $productMongo->setData('product_name', $productParams['title']);
            $product->setPrice ( $productParams['price'] );
            $productMongo->setData('price', $productParams['price']);
            if (isset($productParams['special_price']) && $productParams['special_price']) {
                $product->setSpecialPrice ( $productParams['special_price'] );
                $productMongo->setData('special_price', $productParams['special_price']);
                if (isset($productParams['special_from_date']) && $productParams['special_from_date']) {
                    $specialFromDateTimestamp = Mage::getModel('core/date')->timestamp($productParams['special_from_date'].' 00:00:00');
                    $product->setSpecialFromDate ( $productParams['special_from_date'] );
                    $productMongo->setData('special_from_date', $specialFromDateTimestamp);
                }
                if (isset($productParams['special_to_date']) && $productParams['special_to_date']) {
                    $specialToDateTimestamp = Mage::getModel('core/date')->timestamp($productParams['special_to_date'].' 00:00:00');
                    $product->setSpecialToDate ( $productParams['special_to_date'] );
                    $productMongo->setData('special_to_date',$specialToDateTimestamp);
                }
            }
            $product->setDescription ( $productParams['description'] );
            $productMongo->setData('description', $productParams['description']);
            $product->setShortDescription ( $productParams['short_description'] );
            $productMongo->setData('short_description', $productParams['short_description']);
                
            if(isset($productParams['display']) && $productParams['display']) {
                $productMongo->setData('display', 1);
            }
            else {
                $productMongo->setData('display', 0);
            }
            $product->setWeight ( 1.0 );
            $product->setVisibility ( 4 );
            $product->setStatus ( 1 );
            $product->setTaxClassId ( 0 );
            $product->setStockData ( array (
                    'is_in_stock' => 1,
                    'qty' => 1000
            ) );
            try {
                $product->setCreatedAt ( strtotime ( 'now' ) );
                $productMongo->setData('created_at', strtotime ( 'now' ));
                $product->save ();
                $productMongo->setData('entity_id', $product->getId());
                if( $this->editImage && !isset($productParams['images'] ) ){
                    $arr = array('images', 'thumbnail_image', 'base_image');
                    foreach ($arr as $field) {
                        $productMongo->setData( $field, array() );
                    }
                }
                if( isset($productParams['images']) && is_array( $productParams['images'] ) ){
                    $this->setImageDataForProduct( $productParams, $productMongo);
                }
                
                $productMongo->save();
                Mage::getSingleton('core/session')->addSuccess($this->__('Add new product successfully!'));
                $url = Mage::helper('salon')->getUrl('admin/product');
                $this->_redirectUrl ($url);
            }
            catch ( Exception $exeption ) {
                Mage::getSingleton('core/session')->addError($exeption->getMessage ());
                $url = Mage::getSingleton('core/session')->getRedirectUrl();
                Mage::getSingleton('core/session')->unsRedirectUrl('url');
                $this->_redirectUrl($url);
            }
        }
        else {
            Mage::getSingleton('core/session')->addError('Please fill fully and exactly information!');
            $url = Mage::getSingleton('core/session')->getRedirectUrl();
            Mage::getSingleton('core/session')->unsRedirectUrl('url');
            $this->_redirectUrl($url);
        }
    }
    /**
     * it is use for add new
     * @param unknown $requestData
     */
    public function setImageDataForProduct( $requestData, &$productMongoObj ){
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
        
        $productMongoObj->setData( 'images', $imgData) ;
        
        //// thumnail or base
        if( array_key_exists('thumbImg', $requestData ) && isset( $requestData['thumbImg'] ) ) {
            
            $thumb = $requestData['thumbImg'];
            
            if( isset($requestData['images'][$thumb] ) ) {
                
                $productMongoObj->setData('thumbnail_image', array( 'idImage' =>$thumb, 'url' => $requestData[ 'images'][ $thumb] ) );
            }
        } else {
            $productMongoObj->setData('thumbnail_image', array() );
        }
        
        if( $requestData['baseImg'] ){
            $base = $requestData['baseImg'];
            
            if( isset($requestData['images'][$base] ) ) {
                
                $productMongoObj->setData('base_image', array( 'idImage' =>$base, 'url' => $requestData[ 'images'][ $base] ) );
            }
        } else {
            $productMongoObj->setData('base_image', array() );
        }
    }
    
    
    /**
     * delete a record of product table from magento and mongodb by ajax
     */
    public function ajaxdeleteAction() {
        $response = array();
        $deleted = false;
        $productIds = array();
        $count = 0;
        $productId = Mage::app()->getRequest()->getParam('id');
        $productMongo = Mage::getModel('salon/product');
        $productMagento = Mage::getModel('catalog/product');
        

        if(is_array($productId) && count($productId) >0)
        {
            $productIds = $productId;
        }
        else
        {
            if ( isset( $productId) && $productId) {
                $productIds = array( $productId);
            }
        }
        foreach ($productIds as $id)
        {
            if (isset($id) && 0 < $id) {
                $productMongo = $productMongo->load($id, 'entity_id');
                $productMagento = $productMagento->load($id);
            }
            
            if ($productMongo->getEntityId()) {
                try {
                    Mage::register('isSecureArea', true);
                    $ProductName = $productMongo->getProductName();
                    $productMongo->delete();
                    $productMagento->delete();
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
        if($deleted === true)
        {
            Mage::getSingleton('core/session')->addSuccess("{$count} product have deleted from your salon!");
            $response['status'] = 'SUCCESS';
            $response['message'] = "{$ProductName} product have deleted from your salon!";
            echo json_encode($response);
            return;
        }
        else
        {
            Mage::getSingleton('core/session')->addSuccess("System error. Please check your php log file fore more detail.");
            $response['status'] = 'ERROR';
            $response['message'] = 'System error. Please check your php log file fore more detail.';
            echo json_encode($response);
            return ;
        }
    }

    public function ajaxDeleteImgAction() {
        
        $idImg = $this->getRequest()->getParam('id');
        $srcImg = $this->getRequest()->getParam('src');
        if( isset( $idImg) && $idImg ) {
            
            echo json_encode( Mage::helper('salon/image')->ajaxDeleteImgAction($idImg, $srcImg, 'product', Mage::getModel('salon/product') ) );
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
            echo json_encode( Mage::helper('salon/image')->processImgaUploaded( $_FILES, 'product' ) );
            return;
        }
        echo json_encode( array( 'error' => $this->__('Cannot find any image Data!')) );
        return ;
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
    
        $tableHtml = $this->getLayout()->getBlock('salon_product_ajax_sort')->setData( $data )->toHtml();
    
        $direct = Mage::helper('salon')->getSort( $direct );
    
        echo json_encode( array(
                'data'        => $tableHtml,
                'direct'    => $direct,
        ) );
            
        return;
    
            
    }

}