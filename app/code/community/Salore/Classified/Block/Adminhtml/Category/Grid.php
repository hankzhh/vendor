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
class Salore_Classified_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    protected function _prepareCollection() {
        $collection = Mage::getModel('classified/category')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        
        $this->addColumn('title', array(
                'header'    => Mage::helper('classified')->__('Category Name'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'title',
        ));
        $this->addColumn('img_path', array(
                'header'    => Mage::helper('classified')->__('Category Image'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'img_path',
                'renderer'  =>  'classified/adminhtml_category_renderer_categoryimage'
        ));
        $this->addColumn('edit',
                array(
                        'header'    => Mage::helper('classified')->__('Edit'),
                        'width'     => '50px',
                        'type'      => 'action',
                        'getter'     => 'getEntityId',
                        'actions'   => array(
                                array(
                                        'caption' => $this->__('Edit'),
                                        'class' => 'classifed-category-edit',
                                        'url' => '#classifed-category-edit',
                                        'field'   => 'id'
                                )
                        ),
                        'filter'    => false,
                        'sortable'  => false,
                        'index'     => 'entity_id',
                ));
        $this->addColumn('delete',
                array(
                        'header'    => Mage::helper('classified')->__('Delete'),
                        'width'     => '50px',
                        'type'      => 'action',
                        'getter'     => 'getEntityId',
                        'actions'   => array(
                                array(
                                        'caption' => $this->__('Delete'),
                                        'class' => 'classifed-category-delete',
                                        /*it use for ajax*/
                                        'url' => '#classifed-category-delete',
                                        'field'   => 'id'
                                )
                        ),
                        'filter'    => false,
                        'sortable'  => false,
                        'index'     => 'entity_id',
                ));
        return parent::_prepareColumns();
    }
    /**
     *
     * @param unknown $classified
     * @return string
     */
    public function getRowUrl($classified) {
        return $classified->getEntityId();
    }
    /**
     *
     * Get Javascript on Form
     * @return string
     */
  public function getAdditionalJavaScript() {
        $editUrl = Mage::helper("adminhtml")->getUrl("classified_admin/adminhtml_category/edit");
        $deleteUrl = Mage::helper("adminhtml")->getUrl("classified_admin/adminhtml_category/ajaxdelete");
        $messageDelete = Mage::helper('classified')->__('Are you sure to delete this Category?');
        $okLabel = Mage::helper('classified')->__('Yes');
        $canelLabel = Mage::helper('classified')->__('No');
        return 'document.observe("dom:loaded", function() {
            new CategoryManagement(\''.$editUrl.'\' , \''.$deleteUrl.'\' , \''.$messageDelete.'\' , \''.$okLabel.'\' , \''.$canelLabel.'\' );
        });'; 
    }
}
