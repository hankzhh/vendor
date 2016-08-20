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
class Salore_Salon_Block_Service_View extends Mage_Core_Block_Template {
    /**
     * get all servicedata from mongodb
     * @return array
     */
    public function getService() {
        $serviceId = $this->getRequest()->getParam('id');
        $serviceMongo = Mage::getModel('salon/service');
        if(isset($serviceId) && $serviceId) {
            $serviceMongo = $serviceMongo->load($serviceId , 'entity_id');
            if($serviceMongo->getEntityId() == $serviceId) {
                return $serviceMongo;
            }
        }
        return $serviceMongo;
    }
}