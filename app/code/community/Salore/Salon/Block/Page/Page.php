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
class Salore_Salon_Block_Page_View extends Mage_Core_Block_Template {
    /**
     * Get content page from mongodb
     * @return array |boolean
     */
    public function getPageContent() {
        $pageModel = Mage::getModel('salon/page');
        $pageId = $this->getRequest()->getParam('id');
        if (isset($pageId) && $pageId) {
            $pageModel = $pageModel->load($pageId, 'entity_id');
            if ($pageModel->getEntityId()) {
                return $pageModel;
            }
        }
        return false;
    }
}