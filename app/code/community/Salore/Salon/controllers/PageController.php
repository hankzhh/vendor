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
class Salore_Salon_PageController extends Mage_Core_Controller_Front_Action
{
    public function viewAction() {
        $pageId = $this->getRequest()->getParam('id');
        if (!isset($pageId) || !$pageId) {
            $this->_redirectUrl(Mage::helper('salon')->getUrl());
        }
        $this->loadLayout();
        $root = $this->getLayout()->getBlock('root');
        $template = "page/2columns-left.phtml";
        $root->setTemplate($template);
        $block = $this->getLayout()->createBlock('salon/page_view','page.view')->setTemplate('salore/salon/home/page/view.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
}