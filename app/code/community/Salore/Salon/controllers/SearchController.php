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
class Salore_Salon_SearchController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        $this->loadLayout();
        $mode = $this->getRequest()->getParam('mode');
        if($mode === 'list') {
            $this->getLayout()->getBlock('salon_search')->setTemplate('salore/salon/search/list.phtml');
        }
        else if($mode === 'grid') {
            $this->getLayout()->getBlock('salon_search')->setTemplate('salore/salon/search/grid.phtml');
        }
        $this->renderLayout();
    }
}