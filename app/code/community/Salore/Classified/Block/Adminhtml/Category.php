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
class Salore_Classified_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct() { 
        $this->_blockGroup = 'classified'; 
        $this->_controller = 'adminhtml_category';
        $this->_headerText = Mage::helper('classified')->__('Category Management');
        $this->_addButtonLabel = Mage::helper('classified')->__('Add New Category');
        parent::__construct();
    }
    
}