<?php
/**
 * DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
* versions in the future.
*
* @category    Salore
* @package     Salore_Mongo
* @author      Salore team
* @copyright   Copyright (c) Salore team
*/
class Salore_Salon_Helper_Image extends Mage_Core_Helper_Abstract {
    /**
     * create and resize images for gallery collection by ajax
     */
    public function processImgaUploaded( $imgData, $folder) {
        if ( isset($imgData) && count($imgData) > 0 ) {
            $returnData = array();
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            $pathRoot = Mage::getBaseDir('media').DS.'salore'. DS . $folder . DS . $customerId . DS . 'larger' . DS;
            $pathResize = Mage::getBaseDir('media').DS.'salore'. DS . $folder . DS . $customerId . DS . 'small' . DS;
            $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'salore/' . $folder . '/' . $customerId . '/'.'small'.'/';
            foreach ($imgData as $fileId => $file) {
                $fileName = str_replace(' ', '_', $file['name']);
                $fileName = preg_replace("/^'|[^A-Za-z0-9._\']|'$/", '', $fileName);
                $fileInfo = Mage::helper('salon')->createImageAfterUpload($fileName, $fileId, $pathRoot);
                Mage::helper('salon')->resize($pathRoot, $fileName, 280, 160, $fileName, $pathResize);
                $fileInfo['name'] = str_replace(' ', '_', $fileInfo['name']);
                $fileInfo['name'] = preg_replace("/^'|[^A-Za-z0-9._\']|'$/", '', $fileInfo['name']);
                $fileInfo['imgurl'] = $imageUrl.$fileInfo['name'];
                $returnData[$fileId] = $fileInfo;
            }
        }
        return $returnData;
    }
    public function ajaxDeleteImgAction($idImg, $srcImg, $folder, Mage_Core_Model_Abstract $model, $id = false ) {
        $reponse = array();
    
        $imageName = explode('/', $srcImg);
        $imageName = array_pop($imageName);
        
        if( $id ) {
            $obj = $model->load($id, 'entity_id');
        }
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $pathRoot = Mage::getBaseDir('media').DS.'salore'. DS . $folder . DS . $customerId . DS . 'larger' . DS . $imageName;
        $pathResize = Mage::getBaseDir('media').DS.'salore'. DS . $folder . DS . $customerId . DS . 'small' . DS . $imageName;
        try {
            if(file_exists($pathRoot) && file_exists($pathResize) ) {
                Mage::helper('salon')->deletefile($pathRoot);
                Mage::helper('salon')->deletefile($pathResize);
            }
            
            if( !$id )
            {
                $reponse['status'] =  "UPLOAD";
                return $reponse;
            }
            else {
                
                $reponse['status'] =  "SUCCESS";
                $reponse['message'] = "The image  deleted successfully from your gallery";
                return $reponse;
            }
        } catch (Exception $e) {
            $reponse['status'] = 'ERROR';
            $reponse['message'] = $e->getMessage();
            return $reponse;
        }
    

    }
}