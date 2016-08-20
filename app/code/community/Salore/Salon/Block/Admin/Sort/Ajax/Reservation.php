<?php
class Salore_Salon_Block_Admin_Sort_Ajax_Reservation extends Salore_Salon_Block_Admin_Reservation_List {

    public function __construct() {
    
        parent::__construct();
    }
    public function prepareGetReserVation(){
        
        $collection = Mage::getModel('salon/reservation')->getCollection();
    
        if( $this->getData('sort') ){
    
            $this->sort = $this->getData('sort');
            $this->direct = $this->getData('direct');
        }
        $collection->setOrder( $this->sort, strtoupper( $this->direct) );
    
        $this->setCollection($collection);
    
        $this->pageNum = $this->getData('p') ? $this->getData('p') : 1;
    
        $this->limit = 15;
    }
    
    public function getReservationCollection() {
    
        $this->prepareGetReserVation();
    
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
    
}