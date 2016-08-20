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
class Salore_Classified_Lib_Varien_Data_Form_Element_SaloreImage extends Varien_Data_Form_Element_Abstract {
     public function getElementHtml()
    {
        
        $html = $this->getContentHtml();
        return $html;
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

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml() {

        $uploaderBlock = Mage::getSingleton('core/layout')
            ->createBlock('Salore_Salon_Block_Admin_Image_Uploader','img_upload')->setTemplate('salore/salon/admin/image/uploader.phtml');

        $collection = $this->getValue();
        $spec = array();
        $imgData = array();
        if($collection !== null) {
            if( $collection->getImages() && count($collection->getImages() ) ){
                
                $imgData = $collection->getImages();
                
                $spec = $this->getSpecificImage( $collection);
            }
    }

        $arrayData = array(
                'imgData' => $imgData,
                'uploadUrl' => Mage::helper("adminhtml")->getUrl("classified_admin/adminhtml_posts/upload"),
                'deleteUrl' => Mage::helper("adminhtml")->getUrl("classified_admin/adminhtml_posts/ajaxDeleteImg"),
         );
        
        if( count($spec ) ) {
            foreach( $spec as $key => $value ) {
                
                $arrayData[$key] = $value;  
            }
        }
        
        $uploaderBlock->setData('collection', $arrayData);
        return $uploaderBlock->toHtml();
    }


}