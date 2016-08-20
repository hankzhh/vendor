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

class Salore_Salon_Admin_GalleryController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * delete a record from gallery table in mongodb by ajax
     */
    public function ajaxdeleteAction() {
        $reponse = array();
        $galleryId = $this->getRequest()->getParam('id');
        $gallerySrc = $this->getRequest()->getParam('src');
        if(isset($galleryId) && $galleryId) {
            $imageName = explode('/', $gallerySrc);
            $imageName = array_pop($imageName);
            $galleryObj = Mage::getModel('salon/gallery')->load($galleryId ,'entity_id');
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            $pathRoot = Mage::getBaseDir('media').DS.'salore'. DS . 'gallery' . DS . $customerId . DS . 'larger' . DS . $imageName;
            $pathResize = Mage::getBaseDir('media').DS.'salore'. DS . 'gallery' . DS . $customerId . DS . 'small' . DS . $imageName;
            try {
                if (!$galleryObj->getEntityId())
                {
                    $reponse['status'] =  "UPLOAD";
                    echo json_encode($reponse);
                    return ;
                }
                else {
                    if(file_exists($pathRoot) && file_exists($pathResize) ) {
                        Mage::helper('salon')->deletefile($pathRoot);
                        Mage::helper('salon')->deletefile($pathResize);
                    }
                    $galleryObj->delete();
                    $reponse['status'] =  "SUCCESS";
                    $reponse['message'] = "The image  deleted successfully from your gallery";
                    echo json_encode($reponse);
                    return ;
                }
            } catch (Exception $e) {
                $reponse['status'] = 'ERROR';
                $reponse['message'] = $e->getMessage();
                echo json_encode($reponse);
                return ;
            }
        }
        else {
            $reponse['status'] = 'ERROR';
            $reponse['message'] ="Image does not exist in the gallery";
            echo json_encode($reponse);
            return ;
        }
    }
    /**
     * add new or edit gallery's images by ajax
     */
    public function saveAction() {
        $ajxData = Mage::app()->getRequest()->getParam('params');
        $responseData = array();
        if (isset($ajxData) && $ajxData)  {
            $i=0;
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            foreach ($ajxData as $id => $image) {
                $i++;
                $galleryModel = Mage::getModel('salon/gallery');
                $largerUrl = str_replace('small', 'larger', $image['src']);
                if ($id) {
                    $galleryModel= $galleryModel->load($id, 'entity_id');
                    if (!$galleryModel->getEntityId()) {
                        $galleryModel->setData('entity_id', $id);
                    }
                    $imageName = explode('/', $image['src']);
                    $imageName = array_pop($imageName);
                    $galleryModel->setData('file_name', $imageName);
                    $galleryModel->setData('small_url', $image['src']);
                    $galleryModel->setData('larger_url', $largerUrl);
                    $galleryModel->setData('title', $image['title']);
                    $galleryModel->setData('customer_id', $customerId);
                    $galleryModel->setData('description', $image['description']);
                    try {
                        $galleryModel->save();
                        $responseData['status'] = 'SUCCESS';
                        $responseData['message'] = 'Your images have saved successfully!';
                    
                    } catch (Exception $e) {
                        $responseData['status'] = 'ERROR';
                        $responseData['message'] = $e->getMessage();
                        echo json_encode($responseData);
                        return ;
                    }
                }
            }
        }
        echo json_encode($responseData);
        return ;
    }
    /**
     * create and resize images for gallery collection by ajax
     */
    public function uploadAction() {
        if ( isset($_FILES) && count($_FILES) > 0 ) {
            echo json_encode( Mage::helper('salon/image')->processImgaUploaded( $_FILES, 'gallery' ) );
            return;
        }
        echo json_encode( array( 'error' => $this->__('Cannot find any image Data!')) );
        return ;
    }
    /**
     * save some information of gallery by ajax
     */
    public function saveBranchAction() {
        $ajxData = Mage::app()->getRequest()->getParams();
        if(isset($ajxData) && !empty($ajxData) && isset($ajxData['id']) && $ajxData['id'] && isset($ajxData['target']) && $ajxData['target'] && isset($ajxData['value']) && $ajxData['value']) {
            $galleryModel = Mage::getModel('salon/gallery')->load($ajxData['id'], 'entity_id');
            if($galleryModel->getEntityId()) {
                if ($ajxData['target'] === 'title') {
                    $galleryModel->setData('title', $ajxData['value']);
                }
                if ($ajxData['target'] === 'description') {
                    $galleryModel->setData('description', $ajxData['value']);
                }
                try {
                    $galleryModel->save();
                    $responseData['status'] = 'SUCCESS';
                    $responseData['message'] = "This {$ajxData['target']} have saved successfully!";
                    echo json_encode($responseData);
                    return ;
                } catch (Exception $e) {
                    $responseData['status'] = 'ERROR';
                    $responseData['message'] = $e->getMessage();
                    echo json_encode($responseData);
                    return ;
                }
            }
        }
        $responseData['status'] = 'ERROR';
        $responseData['message'] = 'The System haved met a problem. Please contact with us!';
        echo json_encode($responseData);
        return ;
    }
}