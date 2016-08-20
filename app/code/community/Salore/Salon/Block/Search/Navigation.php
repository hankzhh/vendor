<?php
class Salore_Salon_Block_Search_Navigation extends Mage_Core_Block_Template {
    public $modeCurrent = null;
    public function __construct() {
        $this->modeCurrent = $this->getRequest()->getParam('mode');
    }
    /**
     *
     * @return multitype:Ambigous <string, Ambigous> Ambigous <string, Ambigous, NULL>
     */
    public function prepareMenu() {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $menu = array(
                        'list' => $this->modeCurrent ? $this->prepareUrl('list') : Mage::helper('core/url')->getCurrentUrl() . '&mode=list',
                        'grid' => $this->modeCurrent ? $this->prepareUrl('grid') : Mage::helper('core/url')->getCurrentUrl() . '&mode=grid',
                        'map' => $this->modeCurrent ? $this->prepareUrl('map') : Mage::helper('core/url')->getCurrentUrl() . '&mode=map',
        );
        return $menu;
    }
    /**
     *
     * @param unknown $mode
     * @return string
     */
    public function checkActive($mode) {
        if((!$this->modeCurrent && $mode === 'map') || ($this->modeCurrent == $mode))
            return 'class="active"';
        return '';
    }
    public function prepareUrl($mode) {
        $finalUrl = null;
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $fqParam = $this->getRequest()->getParam('fq');
        $pos = strpos($currentUrl, 'mode=');
        $tempStr = substr($currentUrl, $pos);
        $pointReplace = strstr($tempStr, '&', true);
        if($pointReplace) {
            $finalUrl = str_replace($pointReplace, "mode={$mode}", $currentUrl);
        }
        else {
            $finalUrl = strstr($currentUrl, 'mode=', true) . "mode={$mode}";
        }
        return $finalUrl;
    }
}