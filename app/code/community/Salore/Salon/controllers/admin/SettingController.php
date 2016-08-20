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

class Salore_Salon_Admin_SettingController extends Salore_Salon_Admin_BaseController {
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * save salon information to mongodb from form data
     */
    public function saveAction() {
            $customerId = Mage::getSingleton('customer/session')->getId();
            $salon = Mage::registry('currentsalon');
            $salonId = $salon->getId();
            $salonParams = $this->getRequest()->getParams();
            if(isset($salonId) && $salonId) {
                 if (isset($_FILES['salon_logo']['name']) && !empty($_FILES['salon_logo']['name'])) {
                       $logoFile = $_FILES['salon_logo']['name'];
                       $path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).DS.'salore'. DS . 'logo' .DS.$customerId . DS ;  
                       $logoDir = Mage::helper ( 'salon' )->getImageDir($customerId, 'logo');
                       Mage::helper ( 'salon' )->createImageAfterUpload($logoFile ,'salon_logo' ,$path);
                       Mage::helper ( 'salon' )->resizeImage1( $path , $logoFile , 44 ,'logo.png');
                       $salon->setData('logo', $logoDir);
                } 
                 if (isset($_FILES['img_represent']['name']) && $_FILES['img_represent']['name'] != null) {
                       $imgRepresentFile = $_FILES['img_represent']['name'];
                       $path = Mage::getBaseDir('media').DS.'salore'. DS . 'img_represent' .DS.$customerId  . DS;  
                       $imgRepresentDir = Mage::helper ( 'salon' )->getImageDir($customerId, 'img_represent');
                       Mage::helper ( 'salon' )->createImageAfterUpload($imgRepresentFile,'img_represent' , $path);
                       Mage::helper ( 'salon' )->resizeImage( $path ,$imgRepresentFile ,200 , 300 ,'img_represent.png' );
                       $salon->setData('img_represent', $imgRepresentDir);
                } 
                if(isset($salonParams['salon_name'])  &&  $salonParams['salon_name']) {    
                    $salon->setData ('salon_name' , $salonParams['salon_name']);
                }
                if(isset($salonParams['category'])  &&  $salonParams['category']) {
                    $salon->setData ('category' , $salonParams['category']);
                }
                if(isset($salonParams['salon_description']) && $salonParams['salon_description']) {
                    $salon->setData('salon_description' , $salonParams['salon_description']);
                }
                if(isset($salonParams['days']) && $salonParams['days'] ) {
                    $salon->setData('workingtime' , $salonParams['days']);
                }
                if(isset($salonParams['work_start_time']) && $salonParams['work_start_time']) {
                    $salon->setData('work_start_time' , $salonParams['work_start_time']);
                }
                if(isset($salonParams['work_end_time']) && $salonParams['work_end_time']) {
                    $salon->setData('work_end_time' , $salonParams['work_end_time']);
                } 
                try {
                    $salon->setData('updated_at', Mage::app()->getLocale()->storeTimeStamp());
                    $salon->save();
                    $this->changeFooterInformation($salon);
                    Mage::getSingleton('core/session')->addSuccess($this->__('Salon information have saved successfully!'));
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::helper('salon')->getUrl('admin/salon/setting'));
            }
    }
    /**
     * save information of working time 
     * @param  object $salon
     */
    public function changeFooterInformation($salon) {
        $footer =  Mage::getModel('salon/footer')->load('footer_250190');
        $workingTime = $salon->getWorkingtime();
        $content = array();
        $i = 0;
        if($footer->getEntityId()) {
            foreach ($workingTime as $day => $timeframe) {
                $content[$i]['item'] = "{$day} {$timeframe['timestart']}-{$timeframe['timeend']}";
                if(isset($timeframe['off'])) {
                    $content[$i]['off'] = $timeframe['off'];
                    $content[$i]['item'] = "{$day} closed";
                }
                $i++;
            }
            $footer->setData('content', $content);
            try {
    
                $footer->save();
            } catch (Exception $e) {
                echo $e->getMessage(); ;
            }
        }
    }
}