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
class Salore_Salon_Block_Admin_Reservation_Render_Timeframe extends Salore_Salon_Block_Reservation_Form_List {
    public function __construct() {
        parent::__construct();
    }
    public function getFirstService() {
        $serviceId = $this->getRequest()->getParam('serviceId');
        if(isset($serviceId) && $serviceId) {
            $serviceModel = Mage::getModel('salon/service')->load($serviceId);
            if($serviceModel->getData('entity_id')) {
                return $serviceModel;
            }
        }
        else {
            foreach(Mage::getModel('salon/service')->getCollection() as $service) {
                return $service;
            }
        }
    }
}