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
class Salore_Salon_Block_Admin_Theme_List extends Mage_Core_Block_Template {
    /**
     * get dir design in module salore
     * @return string
     */
    public function  getDir() {
         $path = Mage::getBaseDir('design');
         $package = array_diff(scandir($path . DS . 'frontend' . DS . 'salore'), array('..', '.'));
         return $package;
    }
    /**
     * get action save on template form theme
     * @return string url
     */
    public function getAction() {
        return $this->helper('salon')->getUrl('admin/theme/save');
    }
    /**
     * get path dir design
     * @return string url
     */
    public function getPath() {
        $path = Mage::getBaseDir('design');
        return $path;
    }
    /**
     * 
     * @param unknown $theme
     * @param string $package
     * @return array contain images src
     */
    public function getScreenshots($theme, $package = 'salore') {
        $srcArr = array();
        $themeDir = Mage::getBaseDir('skin') . DS . 'frontend' . DS . $package . DS . $theme;
        $screenshotsDir = $themeDir . DS . 'screenshots';
        if (in_array('screenshots', array_diff(scandir($themeDir), array('..', '.')))) {
            $fileArr = array_diff(scandir($screenshotsDir), array('..', '.'));
            $uri = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'skin/' . 'frontend/' . $package . '/' . $theme . '/screenshots';
            foreach ($fileArr as $fileImg) {
                $srcArr[] = $uri . '/' . $fileImg;
            }
            return $srcArr;
        }
        return $srcArr;
    }
    /**
     * check exist theme
     * @return string
     */
    public function checkExistingThemeCurrent() {
        $salonObj = Mage::registry('currentsalon');
        $themeCurrent = '';
        $themeCurrent = $salonObj->getTheme();
        if ($themeCurrent && !is_null($themeCurrent)) {
            return $themeCurrent;
        }
        return $themeCurrent;
    }
}