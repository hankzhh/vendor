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
class Salore_Salon_Block_Adminhtml_Salon extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct() {
        $this->_blockGroup = 'salon'; 
        $this->_controller = 'adminhtml_salon';
        $this->_headerText = Mage::helper('salon')->__('Salon Management');
        parent::__construct();
        $this->_removeButton('add');
    }
}