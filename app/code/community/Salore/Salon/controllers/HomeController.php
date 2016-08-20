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
class Salore_Salon_HomeController extends Mage_Core_Controller_Front_Action
{
    public $_salon = null;
    public function _construct() {
        $this->_salon = Mage::registry('currentsalon');
    }
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function pendingAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * check valid service, date, staff before reservation
     */
    public function checkingAction() {
         $responseArr = array();
         $salonId = $this->_salon->getEntityId();
         $ajxArg = $this->getRequest()->getParams();
         $timeframe = $this->countTimeFrame($ajxArg['dateSelected'], $ajxArg['serviceId']);
         $timeframeArr = array();
         $reservationCollection = Mage::getModel('salon/reservation')->getCollection();
         $filter = array('time_stamp' => Mage::getModel('core/date')->timestamp($ajxArg['dateSelected'] . ' 00:00:00'),
                 'status' => 'complete',
                 'staff_id' => $ajxArg['staffId']
         );
         $reservationCollection->addFilterQuery($filter);
         foreach($reservationCollection as $reservation) {
             foreach (explode(',', $reservation->getTimeFrame()) as $time) {
                 $timeframeArr[] = $time;
             }
         }
         if ($timeframe && $timeframe != count(array_unique($timeframeArr))) {
             $responseArr['status'] = 'available';
            echo json_encode($responseArr);
            return ;
        }
        else if(!$timeframe) {
            $responseArr['status'] = 'not';
            $responseArr['message'] = '<p class="text-danger">This salon have not already work!</p>';
            echo json_encode($responseArr);
            return ;
        }
        $responseArr['status'] = 'not';
        $responseArr['message'] = '<p class="text-danger">The selected date is not available! Please select other date!</p>';
        echo json_encode($responseArr);
        return ;
    }
    /**
     * count timeframe number before checking available service
     * @param date $date
     * @param string $serviceId
     * @return number|NULL
     */
    public function countTimeFrame($date, $serviceId) {
        $date =  date( 'l', strtotime($date));
        $salonWorkingtime = $this->_salon->getData('workingtime');
        $startTime = $salonWorkingtime[$date]['timestart'];
        $startTimestamp = Mage::getModel('core/date')->timestamp("20-06-2014 {$startTime}:00");
        $endTime = $salonWorkingtime[$date]['timeend'];
        $endTimestamp = Mage::getModel('core/date')->timestamp("20-06-2014 {$endTime}:00");
        $workingtime = round(($endTimestamp - $startTimestamp)/3600);
        $duration = Mage::getModel('salon/service')->load($serviceId, 'entity_id')->getDuration();
        if($duration)
            return round(($workingtime*60)/$duration);
        return null;
    }
}