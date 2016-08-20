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
class Salore_Salon_Block_Product_List extends Mage_Core_Block_Template {
    
    public $pageNum = null;
    public $limit = null;
    public $isHome = false;
    
    
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('salon/product')->getCollection();
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 5;
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'product.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('product.pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    
    /**
     * Get all the products in MongoDB
     * @return object
     */
    public function getProductFromMongoDb() {
        $productArr = array();
        $collection = $this->getCollection();
        $size = $collection->getSize();
        $pages = ceil($size / $this->limit);
        $offset = ($this->pageNum - 1)  * $this->limit;
        $start = $offset + 1;
        $i = 1;
        
        foreach($collection as $item) {
            if($start <= $i) {
                if ( $i <= min( ( $offset + $this->limit), $size ) ) {
                    $productArr[] = $item->getData();
                }
            }
            $i++;
        }
        
        if(Mage::app()->getRequest()->getControllerName() === 'home') {
            $productArr = $this->getProductByPrice($productArr);
            $this->isHome = true;
        }
        
        return $productArr;
    }
    
    public function getProductByPrice($productArr) {
        $productReturn = $productArr;
        $i = 0;
        foreach($productArr as $product) {
            if( isset($product['special_price'] ) ) {
                unset($productReturn[$i]);
                array_unshift($productReturn, $product);
            }
            $i++;
        }
        return array_slice($productReturn, 0, 4);
    }
    /**
     * Get all the products in MongoDB
     * @return array
     */
    public function getProduct() {
        $productId = $this->getRequest()->getParam('id');
        $productMongo = Mage::getModel('salon/product');
        if(isset($productId) && $productId) {
            $productMongo = $productMongo->load($productId , 'entity_id');
            if($productMongo->getEntityId() == $productId) {
                return $productMongo;
            }
        }
        return $productMongo;
    }
}