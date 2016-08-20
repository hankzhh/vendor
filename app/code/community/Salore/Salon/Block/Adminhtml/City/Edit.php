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
class Salore_Salon_Block_Adminhtml_City_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'salon';
        $this->_controller = 'adminhtml_city';
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('salon')->__('Save Image'));
    }
    /**
     * get Text header image city in salon admin
     * @return string
     */
    public function getHeaderText() {
            return Mage::helper('salon')->__('Add Image for City');
    }
}