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
class Salore_Salon_Block_Adminhtml_City_Renderer_Cityimage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
     /**
      * Render Image in Row index
      * @param Varien_Object $row
      * @return image
      */
    public function render(Varien_Object $row) {
        if($row->getData('img_path_resize')) {
            return sprintf( 
                            '<img style="width: 100px" alt="" src="%s" />',
                            $row->getImgPathResize());
        }
    }
}