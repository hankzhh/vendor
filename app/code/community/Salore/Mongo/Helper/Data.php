<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Mongo to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Mongo_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILE_NAME = 'Salore_Mongo.log';
    
    public function log( $message ) {
        try {
            Mage::log(print_r($message, true), null, static::LOG_FILE_NAME);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}