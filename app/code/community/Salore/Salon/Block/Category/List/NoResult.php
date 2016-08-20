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
class Salore_Salon_Block_Category_List_NoResult extends Salore_Salon_Block_Category_List {
    
    public $categoryName = null;
    public function __construct() {
        
        $idCategory = $this->getRequest()->getParam('id');
        
        $this->getCategoryName( $idCategory );
        
        parent::__construct();
    }
    
    protected function getCategoryName($id){
        $categoryObj = Mage::getModel('salon/category')->load((int)$id);
        
        if( $categoryObj->getCategoryName() ) {
            $this->categoryName = $categoryObj->getCategoryName();
        }
    }
    

    
}