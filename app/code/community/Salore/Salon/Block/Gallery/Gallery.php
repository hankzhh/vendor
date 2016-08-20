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
class Salore_Salon_Block_Gallery_Gallery extends Mage_Core_Block_Template {
    /**
     * Get all Gallery in mongodb
     * @return object Gallery
     */
    public function getGallery() {
        $galleryColletion = Mage::getModel('salon/gallery')->getCollection();
        return $galleryColletion;
    }

    
}