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
class Salore_Salon_CategoryController extends Mage_Core_Controller_Front_Action
{
    
    public function indexAction() {
        $this->_forward('view');
    }
    
    public function viewAction() {
        $this->loadLayout ();
        
        $noResult = $this->getLayout ()->createBlock ( 'Salore_Salon_Block_Category_List_NoResult', 'category.noresult' )->setTemplate ( 'salore/salon/category/noResult.phtml' );
        
        $this->getLayout ()->getBlock ( 'content' )->append ( $noResult );
        
        $this->renderLayout ();
    }
}
