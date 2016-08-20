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
class Salore_Classified_Block_Adminhtml_Posts_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
     public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'classified';
        $this->_controller = 'adminhtml_posts';
          if( !Mage::registry('posts_data')) {
            $this->_removeButton('delete');
        }
        $this->_updateButton('save', 'label', Mage::helper('classified')->__('Save Posts'));
    }
    /**
     * get Text Header classified Admin
     * @return string
     */
    public function getHeaderText() {
            return Mage::helper('classified')->__('Add Posts');
    }
}