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
class Salore_Salon_Model_Staff extends Salore_Mongo_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('salon/staff');
    }
    public function createSampleStaff() {
        $staffMongo = Mage::getModel('salon/staff');
        for($i=1 ; $i<=2 ; $i++) {
            $staffMongo->setData('name' , "Sample  Staff{$i}");
            $staffMongo->setData('age', '20');
            $staffMongo->setData('entity_id', uniqid());
            $staffMongo->setData('year', '2');
            $staffMongo->setData('sex', 'female');
            $staffMongo->setData('created_at', strtotime ( 'now' ));
            $staffMongo->save();
            $staffMongo->clearInstance();
        }
    }
    public function setSalonUrl($salonUrl) {
        $this->_getResource()->setSalonUrl($salonUrl);
    }
}