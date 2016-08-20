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
class Salore_Salon_Block_Admin_Gallery_Gallery extends Mage_Core_Block_Template {
    /**
     * get image gallery data of table gallery  from mongodb
     * @return object
     */
    public function getGallery() {
        $galleryObj = Mage::getModel('salon/gallery')->getCollection();
        return $galleryObj;
    }
}
