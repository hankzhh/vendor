<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Classified to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Block_Home_Thumbs extends Mage_Core_Block_Template {
    public $pageNum = null;
    public $limit = null;
    public $category = null;
    public function __construct()
    {
        parent::__construct();
        $this->category = $this->getRequest()->getParam('category') ? $this->getRequest()->getParam('category') : '1';
        $collection = Mage::getModel('classified/posts')->getCollection();
        $filterQuery = array('category' => $this->category);
        $collection->addFilterQuery($filterQuery);
        $this->setCollection($collection);
        $this->pageNum = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
        $this->limit = 5;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('salon/html_pager', 'classified.post.pager');
        $pager->setUseContainer(false);
        $pager->setShowPerPage(false);
        $pager->setShowAmounts(false);
        $pager->setAvailableLimit(array($this->limit=>$this->limit));
        $pager->setCollection($this->getCollection());
        $this->setChild('classified.post.pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    public function getPostsByCategory()
    {
        $postArr = array();
        $collection = $this->getCollection();
        $size = $collection->getSize();
        $pages = ceil($size / $this->limit);
        $offset = ($this->pageNum - 1)  * $this->limit;
        $start = $offset + 1;
        $i = 1;
        
        foreach($collection as $item) {
            if($start <= $i) {
                if ( $i <= min( ( $offset + $this->limit), $size ) ) {
                    $postArr[] = $item->getData();
                }
            }
            $i++;
        }
        return $postArr;
    }
}