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
class Salore_Salon_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        $form = new Varien_Data_Form(
                array(
                        'id' => 'edit_form',
                        'action' => $this->getUrl('*/*/save', array (
                                'id' => $this->getRequest()
                                ->getParam('id')
                        )),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data',
                )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('category_form', array('legend'=>Mage::helper('salon')->__('Category Form')));
        $fieldset->addField('category_name', 'text', array(
                'label'     => Mage::helper('salon')->__('Category Name'),
                'name'      => 'category_name',
                'value'     => $this->getCategoryName(),
        ));
    
        $fieldset->addField('id', 'hidden', array(
                'label'     => Mage::helper('salon')->__('Item Id'),
                'name'      => 'id',
                'value'     => $this->getIdInputHidden(),
        ));
         $fieldset->addField('img_path', 'image', array (
                'label'     => Mage::helper('salon')->__('Image'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'img_category',
                'note' => $this->render(),
        )); 
        return parent::_prepareForm();
    }
    public function render() {
        
        return     sprintf( 
                            '<img style="width: 100px" alt="" src="%s" />',
                            Mage::registry('category_data')->getImgPathResize());
        
    }
    public function getIdInputHidden() {
        $sessionData = Mage:: getSingleton( 'adminhtml/session')->getSessionDataExist();
        if (isset($sessionData['id']) && $sessionData['id']) {
            return $sessionData['id'];
        }
        else {
            return ($categoryId = Mage::registry('category_data')->getEntityId()) ? $categoryId : '';
        }
    }
    public function getCategoryName() {
        return Mage::registry('category_data')->getCategoryName();
    }
}
