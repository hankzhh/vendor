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
class Salore_Salon_Block_Category_List extends Salore_Salon_Block_Salon_Index {
    
    protected $emptySalon = false;
    public $categoryId = null;
    public $categoryName = null;
    public function __construct() {
         $collection = Mage::getModel('salon/salon')->getCollection();
         
        $this->categoryId = $this->getRequest()->getParam('id');
        
         $this->categoryName = Mage::getModel('salon/category')->load($this->categoryId, 'entity_id')->getData('category_name');
         
        $filterQuery = array('category' => array('$eq' => $this->categoryId ), 'approve' => 1 );
         
         $collection->addFilterQuery( $filterQuery);
         
         $this->setCollection($collection);
         
         $sizeOfCollection = $collection->getSize();
         
         if( $sizeOfCollection <= 0 ){
             
             $this->emptySalon = true;
         }
             
         $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
         
         $this->limit = 12;
         
         parent::$pageTotal = ceil($sizeOfCollection / $this->limit);
     }
    
    protected function _prepareLayout() {
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle($this->categoryName); 
        parent::_prepareLayout();
    }
}
