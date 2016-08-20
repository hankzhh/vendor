<?php
class Salore_Salon_Block_Admin_Sort_Ajax_Service extends Salore_Salon_Block_Admin_Services_List {

    public function __construct() {
    
        parent::__construct();
    }
    public function prepareGetService(){
        $collection = Mage::getModel('salon/service')->getCollection();
    
        if( $this->getData('sort') ){
    
            $this->sort = $this->getData('sort');
            $this->direct = $this->getData('direct');
        }
        $collection->setOrder( $this->sort, strtoupper( $this->direct) );
    
        $this->setCollection($collection);
    
        $this->pageNum = $this->getData('p') ? $this->getData('p') : 1;
    
        $this->limit = 5;
    }
    
    public function getServiceCollection() {
    
        $this->prepareGetService();
    
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