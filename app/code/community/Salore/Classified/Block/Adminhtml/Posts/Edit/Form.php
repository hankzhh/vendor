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
class Salore_Classified_Block_Adminhtml_Posts_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        
        return $return;
    }
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
        $fieldset = $form->addFieldset('posts_form', array('legend'=>Mage::helper('classified')->__('Posts Form')));
        $fieldset->addField('title', 'text', array(
                'label'     => Mage::helper('classified')->__('Title'),
                'name'      => 'title',
                'value'     => $this->getPostsName(),
                'required'  => true,
        ));
        
        $fieldset->addField('description', 'editor',
                array (
                        'name' => 'description',
                        'label' => Mage::helper('classified')->__('Description'),
                        'title' => Mage::helper('classified')->__('Description'),
                        'style' => 'height:20em;',
                        'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
                        'required' => true,
                        'value' => Mage::registry('posts_data') ? Mage::registry('posts_data')->getDescription() : '',
                 ));
        
        $fieldset->addField('category', 'select', array(
                'label'     => Mage::helper('classified')->__('Category'),
                'name'      => 'category',
                'value'     => Mage::registry('posts_data')? Mage::registry('posts_data')->getCategory() : '',
                'values'    => $this->getAllCategory(),
                'required'  => true,
        ));
        
        $fieldset->addField( 'is_specific', 'checkbox', array(
                'label'     => Mage::helper('classified')->__('Is specific'),
                'name'      => 'specific',
                'checked'   => $this->getCheckBox(),
        ));
        
        $fieldset->addField('expired_date', 'date', array(
                'name'               => 'expired_date',
                'label'              => Mage::helper('classified')->__('Expired Date'),
                'after_element_html' => '<small>Calendar</small>',
                'tabindex'           => 1,
                'image'              => $this->getSkinUrl('images/grid-cal.gif'),
                'format'             => 'MM/dd/yyyy',
                'value'              => Mage::registry('posts_data')->getExpiredDate()? Mage::registry('posts_data')->getExpiredDate() : date( 'mm/dd/yyyy',
                        strtotime('next weekday') ),
                'required'  => true,
        ));
        
        
        $fieldset->addType('dynamic', 'Salore_Classified_Lib_Varien_Data_Form_Element_SaloreImage');
        
        $fieldset->addField('img_path_resize', 'dynamic', array(
                'label'     => Mage::helper('classified')->__('Image'),
                'name'      => 'image[url]',
                'value'     => Mage::registry('posts_data') ? Mage::registry('posts_data') : null,
        ));
        
        if ( Mage::getSingleton('adminhtml/session')->getPostsData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostsData());
            Mage::getSingleton('adminhtml/session')->setPostsData(null);
        }
        return parent::_prepareForm();
    }
    
    public function render() {
        
        return     sprintf( 
                            '<img style="width: 100px" alt="" src="%s" />',
                            Mage::registry('posts_data')->getImgPathResize());
        
    }
    
    public function getCheckBox(){
        
        if( Mage::registry('posts_data') ) {
            if( Mage::registry('posts_data')->getIsSpecific() )
                return true;
            return false;
        }
        return false;
    }
    public function getPostsName() {
        if( Mage::registry('posts_data') )
            return Mage::registry('posts_data')->getTitle();
        return '';
    }
    
    public function getAllCategory() {
        $result = array();
        
        $categoryCollections = Mage::getModel('classified/category')->getCollection();
        
        if( $categoryCollections->getSize() <= 0 ) {
            return $result;
        }
        
        foreach ( $categoryCollections as $category ) {
                
            if ( $IdCategory = $category->getEntityId () ) {
                
                $result [$IdCategory] = $category->getTitle ();
            }
        }
        
        return $result;
    }
}
