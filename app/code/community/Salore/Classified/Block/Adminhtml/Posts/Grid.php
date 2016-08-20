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

class Salore_Classified_Block_Adminhtml_Posts_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    protected function _construct() {
        parent::_construct();
    }
    protected function _prepareCollection() {
        $collection = Mage::getModel('classified/posts')->getCollection();
        $collection->setSort('update_at','DESC');
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
protected function _prepareColumns() {
        $this->addColumn('title', array(
                'header'    => Mage::helper('classified')->__('Posts Name'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'title',
        ));
        
        $this->addColumn('img_path', array(
                'header'    => Mage::helper('classified')->__('Posts Image'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'img_path',
                'renderer'  =>  'classified/adminhtml_posts_renderer_postsImage'
        ));
        
        $this->addColumn('description', array(
                'header'    => Mage::helper('classified')->__('Description'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'description',
                'renderer' => "classified/adminhtml_posts_renderer_getDescription"
        ));
        
        $this->addColumn('is_specific', array(
                    'header_css_class' => 'a-center',
                    'header' => Mage::helper('classified')->__('Specific'),
                    'index' => 'is_specific',
                    'type' => 'checkbox',
                    'width'     => '50px',
                    'align' => 'center',
                   'renderer' => "classified/adminhtml_posts_renderer_specialcheckbox"
                ));
        $this->addColumn('category', array(
                'header'    => Mage::helper('classified')->__('Category'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'category',
                'renderer'  => "classified/adminhtml_posts_renderer_getcategoryname"
        ));
        $this->addColumn('expired_date', array(
                'header'    => Mage::helper('classified')->__('Expired Date'),
                'align'     => 'left',
                'width'     => '40px',
                'index'     => 'expired_date',
                'renderer'  => 'classified/adminhtml_posts_renderer_getFormatDate'
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
                                        'class' => 'classifed-posts-edit',
                                        'url' => '#classifed-posts-edit',
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
                                        'class' => 'classifed-posts-delete',
                                        /*it use for ajax*/
                                        'url' => '#classifed-posts-delete',
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
     * get Javascript on form  city
     * @return string
     */
    public function getAdditionalJavaScript() {
        $editUrl = Mage::helper("adminhtml")->getUrl("classified_admin/adminhtml_posts/edit");
        $deleteUrl = Mage::helper("adminhtml")->getUrl("classified_admin/adminhtml_posts/ajaxdelete");
        $messageDelete = Mage::helper('classified')->__('Are you sure to delete this Post?');
        $okLabel = Mage::helper('classified')->__('Yes');
        $canelLabel = Mage::helper('classified')->__('No');
        return 'document.observe("dom:loaded", function() {
            new PostsManagement(\''.$editUrl.'\' , \''.$deleteUrl.'\' , \''.$messageDelete.'\' , \''.$okLabel.'\' , \''.$canelLabel.'\' );
        });';
    }
}