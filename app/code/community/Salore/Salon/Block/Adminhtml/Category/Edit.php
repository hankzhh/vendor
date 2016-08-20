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
class Salore_Salon_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
     public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'salon';
        $this->_controller = 'adminhtml_category';
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('salon')->__('Save Image'));
    }
    /**
     * get Text Header Salon Admin
     * @return string
     */
    public function getHeaderText() {
            return Mage::helper('salon')->__('Add Category');
    }
}