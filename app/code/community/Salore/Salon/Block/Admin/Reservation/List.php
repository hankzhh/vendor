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
class Salore_Salon_Block_Admin_Reservation_List extends Mage_Core_Block_Template {
    
    public $pageNum = null;
    public $limit = null;
    public $disable = '';
    public $from = 0;
    public $total = 0;
    public $to = 0;
    public $sort = 'order_id';
    public $direct = 'DESC';
    
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('salon/reservation')->getCollection();
        if( $this->getData( 'sort' ) ){
        
            $this->sort = $this->getData('sort');
            $this->direct = $this->getData('direct');
        }
        
        $collection->setOrder( $this->sort, $this->direct );
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 15;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'reservation.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('reservation_pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    
    public function getSort( ){
    
        return Mage::helper('salon')->getSort($this->direct);
    
    }
    /**
     * get reservation data from mongodb
     * @return array
     */
    public function getReservationCollection() {
        $dataReturn = array();
        $i = 1;
        $collection = $this->getCollection();
        $size = $collection->getSize();
        $pages = ceil($size / $this->limit);
        $offset = ($this->pageNum - 1)  * $this->limit;
        $start = $offset + 1;
        foreach ($collection as $row) {
            if($start <= $i) {
                if ($i <= min(($offset + $this->limit), $size)) {
                    $dataReturn[] = $row->getData();
                }
            }
            $i++;
        }
        return $dataReturn;
    }
    /**
     * 
     * @param unknown $staffId
     * @return string
     */
    public function getStaffNameById($staffId) {
        return Mage::getModel('salon/staff')->load($staffId, 'entity_id')->getName();
    }
}