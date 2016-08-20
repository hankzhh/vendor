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

class Salore_Salon_Admin_BannerController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * upload and resize images for banner collection by ajax
     */
    public function uploadAction() {
        if ( isset($_FILES) && count($_FILES) > 0 ) {
            $returnData = array();
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            $pathRoot = Mage::getBaseDir('media').DS.'salore'. DS . 'banner' . DS . $customerId . DS . 'larger' . DS;
            $pathResize = Mage::getBaseDir('media').DS.'salore'. DS . 'banner' . DS . $customerId . DS . 'small' . DS;
            $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'salore/' .'banner'. '/' . $customerId . '/'.'small'.'/';
            foreach ($_FILES as $fileId => $file) {
                $fileName = $file['name'];
                $fileInfo = Mage::helper('salon')->createImageAfterUpload($fileName, $fileId, $pathRoot);
                Mage::helper('salon')->resize($pathRoot, $fileName, 280, 160, $fileName, $pathResize);
                //$resizeFileInfo['imgurl'] = $imageUrl.$resizeFileInfo['name'];
                $fileInfo['imgurl'] = $imageUrl.$fileInfo['name'];
                $returnData[$fileId] = $fileInfo;
            }
        }
        echo json_encode($returnData);
        return ;
    }
    /**
     * save salon's banner information to mongodb by ajax
     */
    public function saveAction() {
        $ajxData = Mage::app()->getRequest()->getParam('params');
        $salonId = Mage::registry('currentsalon')->getEntityId();
        $responseData = array();
        if (isset($ajxData) && $ajxData) {
            foreach ($ajxData as $id => $image) {
                $bannerModel = Mage::getModel('salon/banner');
                $largerUrl = str_replace('small', 'larger', $image['src']);
                if ($id) {
                    $bannerModel= $bannerModel->load($id, 'entity_id');
                    if (!$bannerModel->getEntityId()) {
                        $bannerModel->setData('entity_id', $id);
                    }
                    $imageName = explode('/', $image['src']);
                    $imageName = array_pop($imageName);
                    $bannerModel->setData('file_name', $imageName);
                    $bannerModel->setData('small_url', $image['src']);
                    $bannerModel->setData('banner_url', $largerUrl);
                    $bannerModel->setData('salon_id', $salonId);
                    $bannerModel->setData('title', $image['title']);
                    $bannerModel->setData('description', $image['description']);
                    try {
                        $bannerModel->save();
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
     * delete a banner information from mongodb by ajax 
     */
    public function ajaxdeleteAction() {
        $reponse = array();
        $bannerId = $this->getRequest()->getParam('id');
        $bannerSrc = $this->getRequest()->getParam('src');
        if(isset($bannerId) && $bannerId) {
            $imageName = explode('/', $bannerSrc);
            $imageName = array_pop($imageName);
            $bannerObj = Mage::getModel('salon/banner')->load($bannerId ,'entity_id');
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            $pathRoot = Mage::getBaseDir('media').DS.'salore'. DS . 'banner' . DS . $customerId . DS . 'larger' . DS . $imageName;
            $pathResize = Mage::getBaseDir('media').DS.'salore'. DS . 'banner' . DS . $customerId . DS . 'small' . DS . $imageName;
            try {
                if (!$bannerObj->getEntityId()) {
                    $reponse['status'] =  "UPLOAD";
                    echo json_encode($reponse);
                    return ;
                }
                else  {
                    if(file_exists($pathRoot) && file_exists($pathResize) ) {
                        Mage::helper('salon')->deletefile($pathRoot);
                        Mage::helper('salon')->deletefile($pathResize);
                    }
                    $bannerObj->delete();
                    $reponse['status'] =  "SUCCESS";
                    $reponse['message'] = "The image  deleted successfully from your banner";
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
            $reponse['message'] ="Image does not exist in the banner";
            echo json_encode($reponse);
            return ;
        }
    }
    /**
     * edit banner information by ajax
     */
    public function saveBranchAction() {
        $ajxData = Mage::app()->getRequest()->getParams();
        if(isset($ajxData) && !empty($ajxData) && isset($ajxData['id']) && $ajxData['id'] && isset($ajxData['target']) && $ajxData['target'] && isset($ajxData['value']) && $ajxData['value']) {
            $bannerModel = Mage::getModel('salon/banner')->load($ajxData['id'], 'entity_id');
            if($bannerModel->getEntityId()) {
                if ($ajxData['target'] === 'title') {
                    $bannerModel->setData('title', $ajxData['value']);
                }
                if ($ajxData['target'] === 'description') {
                    $bannerModel->setData('description', $ajxData['value']);
                }
                try {
                    $bannerModel->save();
                    $bannerData['status'] = 'SUCCESS';
                    $bannerData['message'] = "This {$ajxData['target']} have saved successfully!";
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