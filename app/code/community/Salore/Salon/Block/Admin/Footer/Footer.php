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
class Salore_Salon_Block_Admin_Footer_Footer extends Mage_Core_Block_Template {
    /**
     * get footerdata from mongodb
     * @return object
     */
    public function getFooter() {
        $footerObj = Mage::getModel('salon/footer')->getCollection();
        return $footerObj;
    }
    /**
     * get Action save on template form footer
     * @return string url
     */
    public function getAction() {
        return Mage::helper('salon')->getUrl('admin/footer/save');
    }
    /**
     * get footerdata from mongodb
     * @return array
     */
    public function getFooterObj() {
        $footerId = $this->getRequest()->getParam('id');
        $footerModel = Mage::getModel('salon/footer');
        if(isset($footerId) && $footerId) {
            $footerModel = $footerModel->load($footerId, 'entity_id');
            if ($footerModel->getEntityId()) {
                return $footerModel;
            }
        }
        return $footerModel;
    }
}
