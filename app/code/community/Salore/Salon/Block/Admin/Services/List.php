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
class Salore_Salon_Block_Admin_Services_List extends Mage_Core_Block_Template {
    protected $_customerId;
    public $pageNum = null;
    public $limit = null;
    public $disable = '';
    public $from = 0;
    public $total = 0;
    public $to = 0;
    public $sort = 'update_at';
    public $direct = 'DESC';
    
    public function __construct() {
        
        parent::__construct();
        
        $collection = Mage::getModel('salon/service')->getCollection();
        
        if( $this->getData( 'sort' ) ){
                
            $this->sort = $this->getData('sort');
            $this->direct = $this->getData('direct');
        }
        
        $collection->setOrder( $this->sort, $this->direct );
        
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 5;
    }
    
    public function getSort( ){
    
        return Mage::helper('salon')->getSort($this->direct);
    
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'service.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('service.pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    public function getServiceCollection() {
        
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
                
                if ($i <= min(( $offset + $this->limit ), $size ) ) {
                    $dataReturn[] = $row;
                }
            }
            $i++;
        }
        
        if( count( $dataReturn ) <=0 ) {
            $this->disable = 'disabled';
        }
        
        return $this->sortArrayByCreateDate($dataReturn);
    }
    public function checkserviceExist($serviceCollection) {
        
        $flag = false;
        
        foreach ( $serviceCollection as $service ) {
            if( isset( $service['entity_id']) && $service['entity_id']) {
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
    
    
    public function sortArrayByCreateDate($returnData) {
        $countMenu = count($returnData);
        for($i = 0 ; $i < $countMenu - 1 ; $i++ ) {
            $iTimestamp = $returnData[$i]['created_at'];
            for($j = $i + 1 ; $j < $countMenu; $j++ ) {
                $jTimestamp = $returnData[$j]['created_at'];
                if($iTimestamp < $jTimestamp) {
                    $temp = $returnData[$i];
                    $returnData[$i] = $returnData[$j];
                    $returnData[$j] = $temp;
                }
            }
        }
        return $returnData;
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