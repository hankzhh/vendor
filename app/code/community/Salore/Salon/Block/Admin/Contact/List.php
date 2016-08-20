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
class Salore_Salon_Block_Admin_Contact_List extends Mage_Core_Block_Template {
    public $pageNum = null;
    public $limit = null;
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('salon/contact')->getCollection();
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 15;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'contact.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('contact.pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    public function getContactCollection() {
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
                    $dataReturn[] = $row->getData();
                }
            }
            $i++;
        }
        return $this->sortArrayByCreateDate($dataReturn);
    }
    public function checkContactExist($contactCollection) {
        foreach ($contactCollection as $contact) {
            if(isset($contact['entity_id']) && $contact['entity_id']) {
                return true;
            }
        }
        return false;
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
}
    