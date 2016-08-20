<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Classified to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Block_Home extends Mage_Core_Block_Template {
    public $currentMode = null;
    public function __construct() {
        $this->currentMode = $this->getRequest()->getParam('mode');
    }
    public function checkClassifiedMode()
    {
        if('grid' === $this->currentMode)
            return 0;
        return 1;
    }
    public function getMenuLinkByMode($mode)
    {
        return ('grid' === $mode) ? strstr(Mage::helper('core/url')->getCurrentUrl(), 'mode=', true).'mode=grid' :  strstr(Mage::helper('core/url')->getCurrentUrl(), 'mode=', true).'mode=list';
    }
}