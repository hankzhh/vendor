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
class Salore_Salon_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    protected function _prepareCollection() {
        $collection = Mage::getModel('salon/category')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        
        $this->addColumn('category_name', array(
                'header'    => Mage::helper('salon')->__('Category Name'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'category_name',
        ));
        $this->addColumn('img_path', array(
                'header'    => Mage::helper('salon')->__('Category Image'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'img_path',
                'renderer'  =>  'salon/adminhtml_category_renderer_categoryimage'
        ));
        $this->addColumn('edit',
                array(
                        'header'    => Mage::helper('salon')->__('Edit'),
                        'width'     => '50px',
                        'type'      => 'action',
                        'getter'     => 'getEntityId',
                        'actions'   => array(
                                array(
                                        'caption' => $this->__('Edit'),
                                        'class' => 'salon-category-edit',
                                        'url' => '#salon-category-edit',
                                        'field'   => 'id'
                                )
                        ),
                        'filter'    => false,
                        'sortable'  => false,
                        'index'     => 'entity_id',
                ));
        $this->addColumn('delete',
                array(
                        'header'    => Mage::helper('salon')->__('Delete'),
                        'width'     => '50px',
                        'type'      => 'action',
                        'getter'     => 'getEntityId',
                        'actions'   => array(
                                array(
                                        'caption' => $this->__('Delete'),
                                        'class' => 'salon-category-delete',
                                        /*it use for ajax*/
                                        'url' => '#salon-category-delete',
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
     * @param unknown $salon
     * @return string
     */
    public function getRowUrl($salon) {
        return $salon->getEntityId();
    }
    /**
     *
     * Get Javascript on Form
     * @return string
     */
  public function getAdditionalJavaScript() {
        $editUrl = Mage::helper("adminhtml")->getUrl("sbsalon/adminhtml_category/edit");
        $deleteUrl = Mage::helper("adminhtml")->getUrl("sbsalon/adminhtml_category/ajaxdelete");
        $messageDelete = Mage::helper('salon')->__('Are you sure to delete this Category?');
        $okLabel = Mage::helper('salon')->__('Yes');
        $canelLabel = Mage::helper('salon')->__('No');
        return 'document.observe("dom:loaded", function() {
            new CategoryManagement(\''.$editUrl.'\' , \''.$deleteUrl.'\' , \''.$messageDelete.'\' , \''.$okLabel.'\' , \''.$canelLabel.'\' );
        });'; 
    }
}
