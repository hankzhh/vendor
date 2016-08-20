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
class Salore_Salon_Block_Admin_Product_List extends Mage_Core_Block_Template {
    public $pageNum = null;
    public $limit = null;
    public $disable = '';
    public $from = 0;
    public $total = 0;
    public $to = 0;
    public $sort = 'update_at';
    public $direct = 'ASC';
    
    public function __construct() {
        parent::__construct();
        
        $collection = Mage::getModel('salon/product')->getCollection();
        
        if( $this->getData( 'sort' ) ){
                
            $this->sort = $this->getData('sort');
            $this->direct = $this->getData('direct');
        }
        
        $collection->setOrder( $this->sort, $this->direct );
        
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
    public function getSort( ){
    
        return Mage::helper('salon')->getSort($this->direct);
    
    }
    
    public function getProductCollectionFromMongo() {
    $dataReturn = array();
        $i = 1;
        $collection = $this->getCollection();
        $size = $collection->getSize();
        $this->total = $size;

        $pages = ceil($size / $this->limit);
        $offset = ($this->pageNum - 1)  * $this->limit;
        $start = $offset + 1;
        $this->from = $start;
        $this->to = min( $offset + $this->limit, $this->total);
        foreach ($collection as $row) {
            if($start <= $i) {
                if ($i <= min(($offset + $this->limit), $size)) {
                    $dataReturn[] = $row->getData();
                }
            }
            $i++;
        }

        if( count( $dataReturn ) <=0 ) {
            $this->disable = 'disabled';
        }
        return $dataReturn;
    }
    
    public function checkProductExist($productCollection) {
        $flag = false;
        
        foreach ( $productCollection as $product ) {
            if( isset( $product['entity_id']) && $product['entity_id']) {
                $flag =  true;
            } else {
                return false;

            }
        }
        
        if( $flag ) {
            $this->getStaticPage();
            return $flag;
        }
    }
    
    public function getDisable() {
        return $this->disable;
    }
    
    public function getStaticPage(){
        $data = array(    'from' => $this->from,
                'total'    => $this->total,
                'to'    => $this->to,
        );
        $this->setData( $data );
    }
}