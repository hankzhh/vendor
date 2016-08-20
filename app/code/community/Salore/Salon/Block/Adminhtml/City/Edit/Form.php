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
class Salore_Salon_Block_Adminhtml_City_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
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
        $fieldset = $form->addFieldset('imagecity_form', array('legend'=>Mage::helper('salon')->__('City Image')));
        $fieldset->addField('name', 'select', array(
                'label'     => Mage::helper('salon')->__('City Name'),
                'name'      => 'city_name',
                'value'        => $this->getCityName(),
                'values'    =>    $this->cityImage()
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
                'name'      => 'img_city',
        ));
    }  
    public function getIdInputHidden() {
        $sessionData = Mage:: getSingleton( 'adminhtml/session')->getSessionDataExist();
        if (isset($sessionData['id']) && $sessionData['id']) {
            return $sessionData['id'];
        }
        else {
            return ($cityId = Mage::registry('solr_city')->getEntityId()) ? $cityId : '';
        }
    }
    public function getCityName() {
        $sessionData = Mage:: getSingleton( 'adminhtml/session')->getSessionDataExist();
        if (isset($sessionData['city_name']) && $sessionData['city_name']) {
            return $sessionData['city_name'];
        }
        else {
            return ($cityName = Mage::registry('solr_city')->getCityName()) ? $cityName : '';
        }
    }
    public function cityImage() {
        $cityArr = $this->getCity();
            $cityOption = array();
           foreach($cityArr as $k => $v) {

               $cityOption[] = array(
                   'value' => $k,
                   'label' => $v,
               );
           }
      return $cityOption;
    }
    /**
     * get City from solr
     * @return array
     */
     public function getCity() {
        $dataReturn = array();
        $cityData = Mage::getModel('solr/document')->getTopCities();
        $cityMongo = Mage::getModel('salon/city');
        if (isset($cityData) && !empty($cityData)) {
            foreach( $cityData['facet_counts']['facet_fields']['city_facet'] as $city => $count) {
                $cityMongo = $cityMongo->load($city, 'city_name');
                if($city != $cityMongo->getData('city_name')) {
                    $dataReturn[$city] = $city;
                }
            }
        }
        return $dataReturn;
    } 
}
