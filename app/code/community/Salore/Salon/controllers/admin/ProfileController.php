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

class Salore_Salon_Admin_ProfileController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * update information user admin to mongodb from form data
     */
    public function saveAction() {
            $salonParams = $this->getRequest()->getParams();
            $salon = Mage::registry('currentsalon');
            $salonId = $salon->getId();
            if(isset($salonId) && $salonId) {
                if(isset($salonParams['firstname'])  &&  $salonParams['firstname']) {    
                    $salon->setData ('firstname' , $salonParams['firstname']);
                }
                if(isset($salonParams['lastname'])  &&  $salonParams['lastname']) {
                    $salon->setData ('lastname' , $salonParams['lastname']);
                }
                try {
                    $salon->setData('updated_at', Mage::app()->getLocale()->storeTimeStamp());
                    $salon->save();
                    Mage::getSingleton('core/session')->addSuccess($this->__('User Profile have saved successfully!'));
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::helper('salon')->getUrl('admin/profile'));
            }
            else {
                Mage::getSingleton('core/session')->addError('User Information not Exits');
            }
    }
    
}