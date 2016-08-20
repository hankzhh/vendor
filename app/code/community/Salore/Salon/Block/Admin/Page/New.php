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
class Salore_Salon_Block_Admin_Page_New extends Mage_Core_Block_Template {
    /**
     * get page data of table page from mongodb
     * @return array
     */
    public function getPage() {
        $pageId = $this->getRequest()->getParam('id');
        $pageModel = Mage::getModel('salon/page');
        if (isset($pageId) && $pageId) {
            $pageModel->load($pageId, 'entity_id');
        }
        return $pageModel;
    }
    /**
     * get action save  in template form page salon admin 
     * @return string url
     */
    public function getAction() {
        return Mage::helper('salon')->getUrl('admin/page/save');
    }
}