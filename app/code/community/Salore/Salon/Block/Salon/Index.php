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
class Salore_Salon_Block_Salon_Index extends Mage_Core_Block_Template {
    public static $pageTotal = 0;
    public $pageNum = null;
    public $limit = null;
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('salon/salon')->getCollection();
        $filterQuery = array('approve' => 1);
        $collection->addFilterQuery($filterQuery);
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 12;
        static::$pageTotal = ceil($this->getCollection()->getSize() / $this->limit);
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'salon.index.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('salon_index_pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    /**
     * get all information salon in mongodb
     * @return array salon after sort
     */
    public function getSalonArrAfterSort() {
        $dataReturn = array();
        $i = 1;
        $collection = $this->getCollection();
        $size = $collection->getSize();
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
        return $this->sortArrayByCreateDate($dataReturn);
    }
    /**
     * Sort array in ascending
     * @param  $returnData Array
     * @return Array Sorts
     */
    public function sortArrayByCreateDate($returnData) {
        $countSalon = count($returnData);
        for($i = 0 ; $i < $countSalon - 1 ; $i++ ) {
            $iTimestamp = Mage::getModel('core/date')->timestamp($returnData[$i]['created_at']);
            for($j = $i + 1 ; $j < $countSalon; $j++ ) {
                $jTimestamp = Mage::getModel('core/date')->timestamp($returnData[$j]['created_at']);
                if($iTimestamp < $jTimestamp) {
                    $temp = $returnData[$j];
                    $returnData[$j] = $returnData[$i];
                    $returnData[$i] = $temp;
                }
            }
        }
        return $returnData;
    }
    public function getTextForFaceBook($salonArr) {
        $textFace = '';
        foreach($salonArr as $salon) {
            if (isset($salon['salon_name']) && $salon['salon_name']) {
                $textFace .= Mage::getUrl(Mage::helper('salon')->transportText($salon['salon_name'])) . ' , ';
            }
        }
        $textFace = trim($textFace, ' , ');
        return $textFace;
    }
    
    public function getCategoryAfterSortByPopular() {
        $salonCollection = Mage::getModel('salon/salon')->getCollection();
        $prepareCategory = array();
        foreach($salonCollection as $salon)
        {
            if($salon->getCategory()){
                if(array_key_exists($salon->getCategory(), $prepareCategory)){
                    $prepareCategory[$salon->getCategory()]++;
                }
                else {
                    $prepareCategory[$salon->getCategory()] = 0;
                }
            }
        }
        arsort($prepareCategory);
        return $prepareCategory;
    }
    /**
     * get all categorydata from solr
     * @return json
     */
    public function getCategories()
    {
        $data = Mage::getModel('solr/document')->getCategories();
        return $data;
    }
}