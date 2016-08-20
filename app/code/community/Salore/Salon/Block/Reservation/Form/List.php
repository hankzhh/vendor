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
class Salore_Salon_Block_Reservation_Form_List extends Mage_Core_Block_Template {
    public $_params = array();
    public $dateBooking = '';
    public $salon = '';
    public $listServiceId = '';
    public $_day = null;
    public function __construct() {
        $this->_params = Mage::app()->getRequest()->getParams();
        isset($this->_params['staffId']) ? $this->_params['staffId'] = $this->_params['staffId'] : $this->_params['staffId'] = $this->getFirstStaffInReservation();
        $this->salon = Mage::registry('currentsalon');
        $this->_day = date('l');
        $this->dateBooking = date("d-m-Y");
        if (isset($this->_params['dateSelected']) && $this->_params['dateSelected'])
        {
            $this->dateBooking = $this->_params['dateSelected'];
            $this->_day =  date( 'l', strtotime($this->_params['dateSelected']));
        }
    }
    /**
     * @return string entityid of staff
     */
    public function getFirstStaffInReservation() {
        foreach(Mage::getModel('salon/staff')->getCollection() as $staff) {
            return $staff->getEntityId();
        }
    }
    /**
     * get all service in workday of salon
     * @return object 
     */
    public function getServiceCollection() {
        $checkSalonOff = $this->salon->getData('workingtime');
        $checkSalonOff = $checkSalonOff[$this->_day];
        if (isset($checkSalonOff['off']) && $checkSalonOff['off']) {
            return false;
        }
        $_serviceCollection = Mage::getModel('salon/service')->getCollection();
        $serviceId = $this->getRequest()->getParam('serviceId');
        $filterQuery = array('salon_id' => $this->salon->getEntityId());
        if (isset($serviceId) && $serviceId && 'all' != $serviceId) {
            $filterQuery['entity_id'] = $serviceId;
        }
        $_serviceCollection->addFilterQuery($filterQuery);
        return $_serviceCollection;
    }
    /**
     * This function is deprecated
     */
    public function getSalon() {
        return $this->salon;
    }
    public function getWorkingTime() {
        $startTime = $this->salon->getData('workingtime');
        $startTime = $startTime[$this->_day]['timestart'];
        $startTimestamp = Mage::getModel('core/date')->timestamp("20-06-2014 {$startTime}:00");
        $endTime = $this->salon->getData('workingtime');
        $endTime = $endTime[$this->_day]['timeend'];
        $endTimestamp = Mage::getModel('core/date')->timestamp("20-06-2014 {$endTime}:00");
        return round(($endTimestamp - $startTimestamp)/3600);
    }
    /**
     * get Datetime of salon
     * @return DateTime
     */
    public function getDateTime() {
        $dateTime = new DateTime('07:00:00');
        $workingTime = $this->salon->getData('workingtime');
        if ($workingTime[$this->_day]['timestart']) {
            $dateTime = new DateTime($workingTime[$this->_day]['timestart'] . ':00');
        }
    
        return $dateTime;
    }
    /**
     *
     * @param  $duration
     * @param  $realTime
     * @return int time
     */
    public function getTimeFrame($duration, $realTime) {
        return round(($realTime*60)/$duration);
    }
    /**
     *
     * @return multitype:
     */
    public function getTimeFrameAfterFilter() {
        $timetmp = Mage::getModel('core/date');
        $serviceCollection = $this->getServiceCollection();
        $dataReturn = array();
        foreach ($serviceCollection as $service) {
            $reservationCollection = Mage::getModel('salon/reservation')->getCollection();
            $reservationCollection->addFilterQuery(array('service_id' => $service->getEntityId(),
                    'time_stamp' => $timetmp->timestamp($this->dateBooking . ' 00:00:00'),
                    'status'    => 'complete',
            ));
            foreach ($reservationCollection as $reservation) {
                $timeFrame = explode(',', $reservation->getTimeFrame());
                foreach ($timeFrame as $time) {
                    $dataReturn[$reservation->getServiceId()][$time] = $reservation->getStaffId() ;
                }
            }
        }
        return $dataReturn;
    }
    /**
     *
     * @param  $service
     * @param  $timeIndex
     * @return string
     */
    public function getElementId($service, $timeIndex) {
        return 'service-'.$service->getEntityId().'-timeframe-'. $timeIndex ;
    }
    /**
     * Check information booking in Salon
     * @param  $serviceId
     * @param  $timeFrameString
     * @return string 'book' or 'booked'
     */
    public function checkInfoBooked($serviceId, $timeFrameString) {
        $response = array();
        $timeFrameArr = $this->getTimeFrameAfterFilter();
        if (isset($timeFrameArr[$serviceId]) && array_key_exists($timeFrameString, $timeFrameArr[$serviceId])) {
            $response['class'] = 'booked';
            if($this->_params['staffId'] == $timeFrameArr[$serviceId][$timeFrameString]) {
                $response['time'] = $timeFrameString;
            }
            return $response;
        }
        return $response['class'] = 'book';
    }
    /**
     * Get Timeend of salon
     * @return string timeend
     */
    public function getTimeEnd() {
        $workingTime = $this->salon->getData('workingtime');
        return $workingTime[$this->_day]['timeend'];
    }
    public function offTimeframebyStaff() {
        $response = array();
        $reservationCollection = Mage::getModel('salon/reservation')->getCollection();
        $filter = array('time_stamp' => Mage::getModel('core/date')->timestamp($this->dateBooking . ' 00:00:00'),
                'status' => 'complete',
                'staff_id' => $this->_params['staffId']
        );
        $reservationCollection->addFilterQuery($filter);
        foreach($reservationCollection as $reservation) {
            foreach (explode(',', $reservation->getTimeFrame()) as $time) {
                $response[] = $time;
            }
        }
        return array_unique($response);
    }
}