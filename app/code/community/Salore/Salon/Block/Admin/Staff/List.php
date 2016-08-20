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
class Salore_Salon_Block_Admin_Staff_List extends Mage_Core_Block_Template {
    /**
     * get staff data of table staff from mongodb
     * @return object
     */
    public $pageNum = null;
    public $limit = null;
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('salon/staff')->getCollection();
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 5;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'staff.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('staff.pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    public function getStaffCollection() {
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
    public function checkStaffExist($contactCollection) {
        foreach ($contactCollection as $contact) {
    
            if(isset($contact['entity_id']) && $contact['entity_id']) {
                return true;
            }
        }
        return false;
    }
}