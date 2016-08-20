<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_FavouriteController extends Mage_Core_Controller_Front_Action
{
    public function addFavouriteAction() {
        $response = array('status' => 'ERROR', 'message' => 'This salon have already added to favourite list');
        $id = Mage::getSingleton('customer/session')->getId();
        $salonUrl = $this->getRequest()->getParam('salonUrl');
        $salonName = $this->getRequest()->getParam('salonName');
        $salonLogo = $this->getRequest()->getParam('salonLogo');
        $favourModel = Mage::getModel('salon/favourite');
        $favourCookie = Mage::getModel('core/cookie')->get('salore_favourite');
        if(isset($salonUrl) && $salonUrl) {
            if(isset($id) && $id) {
                //save to mongo
                $favourModel->setCustomerId($id);
                $salonFavourLink = Mage::getUrl('').$salonUrl;
                if(!$favourModel->load($salonUrl, 'salon_url')->getData('salon_url')) {
                    $favourModel->setData('salon_name', $salonName);
                    $favourModel->setData('logo', $salonLogo);
                    $favourModel->setData('salon_url', $salonUrl);
                    $favourModel->setData('customer_id', $id);
                    $favourModel->setData('entity_id', uniqid('favourite'));
                    try {
                        $favourModel->save();
                        if(strpos($favourCookie, $favourModel->getData('salon_url')) === false)
                        {
                            $response['status'] = 'SUCCESS';
                        }
                    } catch (Exception $e) {
                        $response['message'] = $e->getMessage();
                    }
                }
            }
            else
            {
                //create cookie
                if(strpos($favourCookie, $salonUrl) === false)
                {
                    $response['status'] = 'COOKIE';
                    $favourCookie .= (';' . $salonUrl . ',' . $salonName .','. $salonLogo . ';');
                    $favourCookie = trim($favourCookie, ';');
                    Mage::getModel('core/cookie')->set('salore_favourite', $favourCookie);
                }
            }
        }
        echo json_encode($response);
        return ;
    }
    public function deleteAction() {
        $response = array('status' => 'ERROR', 'message' => 'The system have met a problem!');
        $salonUrl = $this->getRequest()->getParam('salonUrl');
        $customerId = Mage::getSingleton('customer/session')->getId();
        $favourModel = Mage::getModel('salon/favourite');
        $favourCookie = Mage::getModel('core/cookie')->get('salore_favourite');
        if($customerId) {
            $favourModel->setCustomerId($customerId);
            $favourModel->load($salonUrl, 'salon_url');
            if($favourModel->getData('entity_id')) {
                try {
                    $favourModel->delete();
                    $response['status'] = 'SUCCESS';
                } catch (Exception $e) {
                    $response['message'] = $e->getMessage();
                }
            }
        }
        if($favourCookie) {
            $favourTemp = explode(';', $favourCookie);
            $flag = false;
            foreach($favourTemp as $index => $favour) {
                $favourChild = explode(',', $favour);
                if(isset($favourChild[0]) && $favourChild[0] == $salonUrl) {
                    //var_dump($salonUrl); die();
                    $flag = $index;
                }
            }
            if(is_numeric($flag)) {
                unset($favourTemp[$flag]);
            }
            $favourCookie = implode(';', $favourTemp);
            $favourCookie = Mage::getModel('core/cookie')->set('salore_favourite', $favourCookie);
            $response['status'] = 'SUCCESS';
        }
        echo json_encode($response);
        return ;
    }
}