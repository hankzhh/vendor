<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Service_List extends Salore_Salon_Block_Product_List {
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('salon/service')->getCollection();
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 5;
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
    }
    
    public function getServicesFromMongoDb() {
        $servicesArr = array();
        $collection = $this->getCollection();
        $size = $collection->getSize();
        $pages = ceil($size / $this->limit);
        $offset = ($this->pageNum - 1)  * $this->limit;
        $start = $offset + 1;
        $i = 1;
        
        foreach($collection as $item) {
            if($start <= $i) {
                if ( $i <= min( ( $offset + $this->limit), $size ) ) {
                    $servicesArr[] = $item->getData();
                }
            }
            $i++;
        }
        
        if(Mage::app()->getRequest()->getControllerName() === 'home') {
            $servicesArr = $this->getServiceByPrice($servicesArr);
            $this->isHome = true;
        }
        
        return $servicesArr;
    }
    public function getServiceByPrice($servicesArr) {
        $serviceReturn = $servicesArr;
        $i = 0;
        foreach($servicesArr as $service) {
            if(isset($service['special_price'])) {
                unset($serviceReturn[$i]);
                array_unshift($serviceReturn, $service);
            }
            $i++;
        }
        return array_slice($serviceReturn, 0, 4);
    }
    
}
