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

class Salore_Salon_Admin_ThemeController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * save theme information from form data to mongodb
     */
    public function saveAction() {
        $salonObj = Mage::registry('currentsalon');
        $postArr = Mage::app()->getRequest()->getParams();
        if (isset($postArr['theme']) && $salonObj->getEntityId()) {
            $salonObj->setData('theme', $postArr['theme']);
            try {
                $salonObj->save();
                Mage::getSingleton('core/session')->addSuccess($this->__('Thank you, your salon theme is saved successfully.'));
                $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/theme'));
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Opp! Your salon theme is not existing.'));
            $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/theme'));
        }
    }
}