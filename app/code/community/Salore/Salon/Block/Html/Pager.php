<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Salore_Salon_Block_Html_Pager extends Mage_Page_Block_Html_Pager {
    /**
     * 
     * @param unknown $params
     * @return string
     */
    public function getPagerUrl($params=array()) {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $urlArgument = Mage::app()->getRequest()->getParams();
        if(Mage::registry('currentsalon'))
        {
            $prepareUrl = Mage::app()->getRequest()->getControllerName();
            $prepareUrl = str_replace('_' , '/' , $prepareUrl);
            return Mage::helper('salon')->getUrl().$prepareUrl."?p={$params['p']}";
        }
        else 
        {
            if('classified' === Mage::app()->getRequest()->getRouteName()) {
                $urlCurrent = strpos(Mage::helper('core/url')->getCurrentUrl(), '&p=') ? strstr(Mage::helper('core/url')->getCurrentUrl(), '&p=', true) : Mage::helper('core/url')->getCurrentUrl();
                return $urlCurrent."&p={$params['p']}";
            }
            else if( $controllerName === 'search' && isset($urlArgument['mode'])) {
                $urlCurrent = strstr(Mage::helper('core/url')->getCurrentUrl(), 'list', true);
                $prepareUrl = $urlCurrent . 'list' . "&p={$params['p']}";
                if ('grid' === $urlArgument['mode'] ) {
                    $urlCurrent = strstr(Mage::helper('core/url')->getCurrentUrl(), 'grid', true);
                    $prepareUrl = $urlCurrent . 'grid' . "&p={$params['p']}";
                }
                return $prepareUrl;
            } else if( $controllerName == 'classified') {
            
                $urlCurrent = strstr(Mage::helper('core/url')->getCurrentUrl(), '/save/', true);
            
                return $urlCurrent."?p={$params['p']}";
            } else if( $controllerName == 'index') {
                $urlCurrent = strstr(Mage::helper('core/url')->getCurrentUrl(), ' ', true);
                return $urlCurrent."?p={$params['p']}";
            }
        }
        return $this->getUrl('*/*/*', $urlParams);
    }
    /**
     * 
     * @param unknown $collection
     * @return Salore_Salon_Block_Html_Pager
     */
    public function setCollection($collection) {
        $this->_collection = $collection
        ->setCurPage($this->getCurrentPage());
        // If not int - then not limit
        if ((int) $this->getLimit()) {
            $this->_collection->setPageSize($this->getLimit());
        }
        $this->_setFrameInitialized(false);
    
        return $this;
    }
    
    /**
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getCollection() {
        return $this->_collection;
    }
    
    public function getCurrentPage() {
        $pCollection = parent::getCurrentPage();
        $page = (int)$this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        if( (int)$page > 0 && ( $page !== $pCollection) ) {
            return $page;
        } else {
            return $pCollection;
        }
    }
}
