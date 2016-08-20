<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Admin_Login extends Mage_Core_Block_Template {
    /**
     * get action on form login
     * @return string url
     */
    public function getPostActionUrl() {
        return Mage::helper('salon')->getUrl('admin/login/post');
    }
}