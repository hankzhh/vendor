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

class Salore_Salon_Model_Url extends Mage_Core_Model_Url {
    /**
     * Build url by requested path and parameters
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null) {
        $currentShopKey = Mage::registry('currentsalon_url');
        if ($currentShopKey && !empty($currentShopKey))  {
            $url = $this->getBaseUrl() . $currentShopKey .'/'. trim($routePath,'/').'/';
        }
        else  {
            $url = $this->getBaseUrl() . trim($routePath,'/').'/';
        }
        
        $url = preg_replace('/\/\/$/', '/', $url);
        
        return $this->escape($url);
    }
}