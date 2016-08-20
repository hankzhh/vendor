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
class Salore_Salon_Block_Adminhtml_City_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    protected function _construct() {
        parent::_construct();
    }
    public function getTopCities() {
        $data = Mage::getModel('solr/document')->getTopCities();
        return $data;
    }
    public function addDataToMongo() {
        $cityData  = $this->getTopCities();
        $cityModel = Mage::getModel('salon/city');
        $i = 1;
        foreach( $cityData['facet_counts']['facet_fields']['city_varchar'] as $city => $count)
        {
            $cityModel->setData('_id', $i);
            $cityModel->setData('name', $city);
            $cityModel->setData('img_path','');
            $cityModel->save();
            $i++;
        }
    }
    protected function _prepareCollection() {
        $collection = Mage::getModel('salon/city')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn('city_name', array(
            'header'    => Mage::helper('salon')->__('City Name'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'city_name',
        ));
        
        $this->addColumn('img_path', array(
                'header'    => Mage::helper('salon')->__('City Image'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'img_path',
                'renderer'  =>  'salon/adminhtml_city_renderer_cityimage'
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
                                 'class' => 'salon-city-edit',
                                 'url' => '#salon-city-edit',
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
                                        'class' => 'salon-city-delete',
                                        /*it use for ajax*/
                                        'url' => '#salon-city-delete',
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
     * @param  $city
     * @return string
     */
    public function getRowUrl($city) {
        return $city->getEntityId();
    }
    /**
     * get Javascript on form  city
     * @return string
     */
    public function getAdditionalJavaScript() {
        $editUrl = Mage::helper("adminhtml")->getUrl("sbsalon/adminhtml_city/edit");
        $deleteUrl = Mage::helper("adminhtml")->getUrl("sbsalon/adminhtml_city/ajaxdelete");
        $messageDelete = Mage::helper('salon')->__('Are you sure to delete this CityImage?');
        $okLabel = Mage::helper('salon')->__('Yes');
        $canelLabel = Mage::helper('salon')->__('No');
        return 'document.observe("dom:loaded", function() {
            new CityImageManagement(\''.$editUrl.'\' , \''.$deleteUrl.'\' , \''.$messageDelete.'\' , \''.$okLabel.'\' , \''.$canelLabel.'\' );
        });'; 
    }
}
