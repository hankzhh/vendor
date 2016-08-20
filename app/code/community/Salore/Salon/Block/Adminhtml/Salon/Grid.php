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
class Salore_Salon_Block_Adminhtml_Salon_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    protected function _prepareCollection() {
        $collection = Mage::getModel('salon/salon')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        
        $this->addColumn('firstname', array(
                'header'    => Mage::helper('salon')->__('First Name'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'firstname',
        ));
        $this->addColumn('lastname', array(
                'header'    => Mage::helper('salon')->__('Last Name'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'lastname',
        ));
        $this->addColumn('email', array(
                'header'    => Mage::helper('salon')->__('Email'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'email',
        ));
        $this->addColumn('telephone', array(
                'header'    => Mage::helper('salon')->__('Phone Number'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'telephone',
        ));
        $this->addColumn('salon_url', array(
                'header'    => Mage::helper('salon')->__('Salon Url'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'salon_url',
        ));
        $this->addColumn('salon_address', array(
                'header'    => Mage::helper('salon')->__('Salon Address'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'address',
        ));
        $this->addColumn('approve',
                array(
                        'header'    => Mage::helper('salon')->__('Approve'),
                        'width'     => '50px',
                        'type'      => 'action',
                        'getter'     => 'getEntityId',
                        'renderer' => 'Salore_Salon_Block_Adminhtml_Render_Approve',
                        'actions'   => array(
                                array(
                                        'caption' => $this->__('Approve'),
                                        'class' => 'salore-salon-approve',
                                        'url' => '#salore-salon-approve',
                                        'field'   => 'id'
                                )
                        ),
                        'filter'    => false,
                        'sortable'  => false,
                        'index'     => 'id',
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
                                        'class' => 'salore-salon-edit',
                                        'url'     => array('base'=>'*/*/edit'),
                                        'field'   => 'id'
                                )
                        ),
                        'filter'    => false,
                        'sortable'  => false,
                        'index'     => 'id',
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
                                        'class' => 'salore-salon-delete',
                                        'url' => '#salore-salon-delete',
                                        'field'   => 'id'
                                )
                        ),
                        'filter'    => false,
                        'sortable'  => false,
                        'index'     => 'id',
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
        $deleteUrl = Mage::helper("adminhtml")->getUrl("salon/adminhtml_salon/ajaxDelete");
        $approveUrl = Mage::helper("adminhtml")->getUrl("salon/adminhtml_salon/ajaxApprove");
        $messageDelete = Mage::helper('salon')->__('Are you sure to delete this salon?');
        $messageApprove = Mage::helper('salon')->__('Are you sure to approve this salon?');
        $okLabel = Mage::helper('salon')->__('Yes');
        $cancelLabel = Mage::helper('salon')->__('No');
        return 'document.observe("dom:loaded", function() {
            new SalonManagement(\''.$deleteUrl.'\', \''.$approveUrl.'\', \''.$messageDelete.'\', \''.$messageApprove.'\', \''.$okLabel.'\', \''.$cancelLabel.'\');
        });';
    }
}
