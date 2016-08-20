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

class Salore_Salon_Admin_DomainController extends Salore_Salon_Admin_BaseController
{
    public function settingAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function saveAction()
    {
        $domain = $this->getRequest()->getParam('domain');
        $url = Mage::helper('salon')->getUrl('admin/domain/setting');
        $salonObj = Mage::registry('currentsalon');
        if(isset($domain) && $domain){
            //$domainExist = Mage::helper('salon')->checkIsDomainRegistered($domain);
            if(preg_match('/^[-a-z0-9]+(\.[a-z]{2,6}){1,2}$/', strtolower($domain)))
            {
                try {
                    $salonObj->setData('domain', $domain);
                    $salonObj->save();
                    Mage::getSingleton('core/session')->addSuccess('Your domain have saved succesfully!');
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }    
            }
            else 
            {
                Mage::getSingleton('core/session')->addError('Your domain is not exist or invalid!');
            }
        }
        else
        {
            Mage::getSingleton('core/session')->addError('Please enter your valid domain!');
        }
        $this->_redirectUrl($url);
    }
}