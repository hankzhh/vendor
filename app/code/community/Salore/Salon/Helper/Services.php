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
class Salore_Salon_Helper_Services extends Mage_Core_Helper_Abstract
{
    public function getServiceUrl($serviceId)
    {
        $productUrl = Mage::getModel('catalog/product')->load($serviceId)->getProductUrl();
        return $productUrl;
    }
    public function getAddToReservationtUrl($product, $additional = array())
    {
        $salonKey = Mage::app()->getRequest()->getParam('salonkey');
        return Mage::getUrl('salon/'.$salonKey.'/reservation/', array('_query'=>array('service'=>$product['entity_id'])));
    }
    /**
     * check after rendering head tag
     * @return boolean
     */
    public function allowRenderHeadText()
    {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        if( $controllerName === 'service' || $controllerName === 'product')
            return false;
        return true;
    }
}