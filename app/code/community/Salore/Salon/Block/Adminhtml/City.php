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
class Salore_Salon_Block_Adminhtml_City extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct() { 
        $this->_blockGroup = 'salon'; 
        $this->_controller = 'adminhtml_city';
        $this->_headerText = Mage::helper('salon')->__('City Image Management');
        $this->_addButtonLabel = Mage::helper('salon')->__('Add New Image For Cities');
        parent::__construct();
    }
    
}