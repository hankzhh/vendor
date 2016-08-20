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
class Salore_Salon_Block_Customer_Classified_List extends Mage_Core_Block_Template {
    public $pageNum = null;
    public $limit = null;
    public $disable = '';
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('classified/posts')->getCollection();
        
        if( Mage::getSingleton('customer/session')->isLoggedIn() ) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $this->customerId = $customerData->getId();
            $filterQuery = array('customer_id' => array('$eq' => (int)$this->customerId ) );
            $collection->addFilterQuery($filterQuery);
        }
        
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 5;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'classified.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('classified.pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    public function getClassifedCollection() {
        $dataReturn = array();
        $i = 1;
        $collection = $this->getCollection();
        $size = $collection->getSize();
        $pages = ceil($size / $this->limit);
        $offset = ($this->pageNum - 1)  * $this->limit;
        $start = $offset + 1;
        foreach ($collection as $row)
        {
            if($start <= $i)
            {
                if ($i <= min(($offset + $this->limit), $size)) {
                    $dataReturn[] = $row;
                }
            }
            $i++;
        }
        if(count($dataReturn) <=0)
        {
            $this->disable = 'disabled';
        }
        return $dataReturn;
    }
    /**
     * sort array by create date
     * @param array $returnData
     * @return array
     */
    public function sortArrayByCreateDate($returnData) {
        $countService = count($returnData);
        for($i = 0 ; $i < $countService - 1 ; $i++ ) {
            $iTimestamp = $returnData[$i]['create_at'];
            for($j = $i + 1 ; $j < $countService; $j++ ) {
                $jTimestamp = $returnData[$j]['create_at'];
                if($iTimestamp < $jTimestamp) {
                    $temp = $returnData[$j];
                    $returnData[$j] = $returnData[$i];
                    $returnData[$i] = $temp;
                }
            }
        }
        return $returnData;
    }
    public function getDisable()
    {
        return $this->disable;
    }
}