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
class Salore_Salon_Block_Customer_Classified_Delete_Ajax_Pagination extends Salore_Salon_Block_Customer_Classified_List {

    public function __construct() {
        parent::__construct();
    }
    
    public function getPagination(){
        
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'classified.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        return $pager->toHtml();
    }
}