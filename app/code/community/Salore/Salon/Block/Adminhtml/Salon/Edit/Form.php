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
class Salore_Salon_Block_Adminhtml_Salon_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/ajaxApprove', array('salonId' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
        )
        );
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('salon_edit_form', array('legend'=>Mage::helper('salon')->__('Salon Infomation')));
        
        $fieldset->addField('firstname', 'text', array(
                'label'     => Mage::helper('salon')->__('First Name'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'firstname',
        ));
        
        $fieldset->addField('lastname', 'text', array(
                'label'     => Mage::helper('salon')->__('Last Name'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'lastname',
        ));
        $fieldset->addField('email', 'text', array(
                'label'     => Mage::helper('salon')->__('Email'),
                'class'     => 'required-entry validate-email',
                'required'  => true,
                'name'      => 'email',
        ));
        $fieldset->addField('salon_url', 'text', array(
                'label'     => Mage::helper('salon')->__('Salon Url'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'salon_url',
        ));
        $fieldset->addField('salon_name', 'text', array(
                'label'     => Mage::helper('salon')->__('Salon Name'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'salon_name',
        ));
        $fieldset->addField('category', 'select', array(
                'label'     => Mage::helper('salon')->__('Category'),
                'class'     => 'required-entry',
                'name'      => 'category',
                'values'        => $this->getCategoryName(),
        ));
        $fieldset->addField('address', 'text', array(
                'label'     => Mage::helper('salon')->__('Address'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'address',
        ));
        $fieldset->addField('telephone', 'text', array(
                'label'     => Mage::helper('salon')->__('Telephone'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'telephone',
        ));
        $fieldset->addField('city', 'text', array(
                'label'     => Mage::helper('salon')->__('City'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'city',
        ));
        $countryOptions = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        $cOptions = array();
        foreach($countryOptions as $option){
            $cOptions[$option['value']] = $option['label'];
        }
        $fieldset->addField('country_id', 'select', array(
                'label'     => Mage::helper('salon')->__('Country'),
                'name'      => 'country_id',
                'options'      => $cOptions,
        ));
        $fieldset->addField('region', 'text', array(
                'label'     => Mage::helper('salon')->__('State/Province'),
                'required'  => false,
                'name'      => 'region',
        ));
        
        $fieldset->addField('region_id', 'select', array(
                'label'     => Mage::helper('salon')->__('State/Province'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'region_id',
        ));
        
        $fieldset->addField('postcode', 'text', array(
                'label'     => Mage::helper('salon')->__('Zip/Postal Code'),
                'name'      => 'postcode',
        ));
        $fieldset->addField('approve', 'select', array(
                'label'     => Mage::helper('salon')->__('Approve'),
                'name'      => 'approve',
                'disabled'    => true,
                'values'    => $this->getAllOptions(),
        ));
        $regionElement = $form->getElement('region');
        $regionElement->setRequired(true);
        if ($regionElement) {
            $regionElement->setRenderer(Mage::getModel('salon/salon_renderer_region'));
        }
        $regionElement = $form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }
        if ( Mage::getSingleton('adminhtml/session')->getSalonData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSalonsData());
            Mage::getSingleton('adminhtml/session')->setSalonData(null);
        } elseif ( Mage::registry('salon_data') ) {
            $data = Mage::registry('salon_data')->getData();
            if(!isset($data['approve']))
            {
                $data['approve'] = (string)0;
            }else{
                $stringValue = (string)$data['approve'];
                $data['approve'] = $stringValue;
            }
            
            $form->setValues($data);
        } 
        
        return parent::_prepareForm();
    }
    /**
     *
     * @return array
     */
    public function getAllOptions() {
        return array('0'=>'Pending', '1'=>'Approve');
    }
    public function getCategoryName()
    {
        $categoryCollection = Mage::getModel('salon/category')->getCollection();
        $options = array();
        foreach ($categoryCollection as $category)
        {
            if( $category->getEntityId() )
            {
                $options[$category->getEntityId()] = $category->getData('category_name');
            }
        }
        return $options;
    }
}
