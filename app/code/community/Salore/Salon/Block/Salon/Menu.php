<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Block_Salon_Menu extends Mage_Core_Block_Template {
    public function getMenuCollection() {
        $returndata = array();
        $collection = Mage::getModel('salon/menu')->getCollection();
        $filterQuery = array('active' => '1');
        $collection->addFilterQuery($filterQuery);  
        $collection->setOrder('position', 'ASC');
        return $collection;
    }
}