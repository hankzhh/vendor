<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Admin_Password_New extends Mage_Core_Block_Template {
    /**
     * get email of curentsalon from table salon in mongodb
     * @return string | boolean
     */
    public function getEmail() {
        $salonObj = Mage::registry('currentsalon');
        $email = $salonObj->getEmail();
        if (isset($email) && $email) {
            return $email;
        }
        return false;
    }
    /**
     * get action url on template password
     * @return string
     */
    public function getActionUrl() {
        return Mage::helper('salon')->getUrl('admin/password/changePassword');
    }
}
