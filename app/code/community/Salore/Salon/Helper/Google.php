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
class Salore_Salon_Helper_Google extends Mage_Core_Helper_Abstract
{
    const GOOGLE_MAPS_HOST = 'maps.googleapis.com';
    /**
     * Address Street, zipcode city, country
     * @param unknown $address
     * @return Salore_Salon_Helper_Data
     */
    public function getCoordinates($addressLine)
    {
        $client = new Zend_Http_Client();
        $client->setUri('http://' . self::GOOGLE_MAPS_HOST . '/maps/api/geocode/json');
        $client->setMethod(Zend_Http_Client::GET);
        $client->setParameterGet('address', $addressLine);
        $client->setParameterGet('sensor', 'false');
    
        $response = $client->request();
        
        $coordinates = null;
    
        if ($response->isSuccessful() && $response->getStatus() == 200)
        {
            $_response = json_decode($response->getBody());
            $_coordinates = $_response->results[0]->geometry->location;
    
            if (is_object($_coordinates) && count(get_object_vars($_coordinates)) >= 2) {
    
                try {
                    $coordinates = array('lng' => $_coordinates->lng, 'lat' => $_coordinates->lat);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return $coordinates;
    }
}