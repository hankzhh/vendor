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
class Salore_Classified_Block_Posts_View extends Mage_Core_Block_Template {
    public $postsId = null;
    public function __construct()
    {
        $this->postsId = $this->getRequest()->getParam('id') ? $this->getRequest()->getParam('id') : 1;
    }
    public function getPosts()
    {
        return Mage::getModel('classified/posts')->load($this->postsId);
    }
    public function getCategoryName($categoryId = null)
    {
        if($categoryId)
        {
            return Mage::getModel('classified/category')->load($categoryId)->getData('title');
        }
        return null;
    }
}