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
class Salore_Salon_Block_Salon_Favourite extends Mage_Core_Block_Template {
    public function getSalonArrAfterSort() {
        $favourCookie = Mage::getModel('core/cookie')->get('salore_favourite');
        $favourModel = Mage::getModel('salon/favourite');
        
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $newArray = array();
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $favourModel->setCustomerId($customer->getId());
            $favourModel = $favourModel->getCollection();
            
            foreach ($favourModel as $favour) {
                $data[] = $favour->getData();
                $favourCookieFinal[$favour->getData('salon_url')] = array($favour->getData('salon_name'));
                foreach( $favourCookieFinal[$favour->getData('salon_url')] as $val) {
                    array_push($newArray, $val);
                }
            }
             $filterQuery = array('salon_name' => array('$in' => $newArray ));
            $salonFavouriteCollection = Mage::getModel('salon/salon')->getCollection();
            $salonFavouriteCollection->addFilterQuery($filterQuery);
            $salonCollection = Mage::getModel('salon/salon')->getCollection();
            // 'approve' => 1 
            $salonCollection->addFilterQuery(array('salon_name' => array('$nin' => $newArray)));
            $dataReturn = array($salonFavouriteCollection , $salonCollection);
            $returnData = array();
            
            foreach ($dataReturn as $collection) {
                
                foreach ($collection as $salon) {
                    if(isset($salon['created_at']) && $salon['created_at'])
                    {
                        $returnData[] = $salon->getData();
                    }
                }
            } 
            return $this->sortArrayByCreateDate($returnData);
            
        }
        else if($favourCookie) {
            $favourTemp = explode(';', $favourCookie);
            $newArray = array();
            
            foreach ($favourTemp as $salonUrl =>$favour) {
                
                $favour = explode(',', $favour);
                 $favourCookieFinal[$favour[0]] = array($favour[1]);

                 foreach( $favourCookieFinal[$favour[0]] as $val) {
                     array_push($newArray, $val);
                 }
            }
            //'approve' => 1 
            $filterQuery = array('salon_name' => array('$in' => $newArray));
            $salonFavouriteCollection = Mage::getModel('salon/salon')->getCollection();
            $salonFavouriteCollection->addFilterQuery($filterQuery);
            $salonCollection = Mage::getModel('salon/salon')->getCollection();
            $salonCollection->addFilterQuery(array('salon_name' => array('$nin' => $newArray )));
            $dataReturn = array($salonFavouriteCollection , $salonCollection);
            $returnData = array();
            
            foreach ($dataReturn as $collection) {
                
                foreach ($collection as $salon)
                {
                    if(isset($salon['created_at']) && $salon['created_at'])
                    {
                        $returnData[] = $salon->getData();
                    }
                }
            } 
            return $this->sortArrayByCreateDate($returnData);
        }
        else {
            $returnData = array();
            $collection = Mage::getModel('salon/salon')->getCollection();
            $filter = array('approve' => 1);
            $collection->addFilterQuery($filter);
            foreach ($collection as $salon)
            {
                if(isset($salon['created_at']) && $salon['created_at'])
                {
                    $returnData[] = $salon->getData();
                }
            }
            return $this->sortArrayByCreateDate($returnData);
        }
    }
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
    
    public function getCategoryCollection() {
        return Mage::getModel('salon/category')->getCollection();
    }
    
}