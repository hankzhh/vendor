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
class Salore_Classified_Block_Adminhtml_Posts_Renderer_Getcategoryname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
     /**
      * Render Image in Row index
      * @param Varien_Object $row
      * @return image
      */
    public function render(Varien_Object $row) {
        $id = $row->getData($this->getColumn()->getIndex());
        $rs = '';
        if( $id ){
            
            $postsObj = Mage::getModel('classified/category')->load( (int) $id);
            $rs = $postsObj->getTitle();
        }
         return $rs;

    }
}