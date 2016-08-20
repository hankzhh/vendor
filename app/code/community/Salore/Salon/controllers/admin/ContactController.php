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

class Salore_Salon_Admin_ContactController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * edit contact information in mongodb after reading by ajax
     */
    public function ajaxreadAction() {
        $reponse = array();
        $contactModel =  Mage::getModel('salon/contact');
        $contactId = Mage::app()->getRequest()->getParam('id');
        if (isset($contactId) && $contactId) {
                    $contactModel->load($contactId , 'entity_id');
                    if(!$contactModel->getData('style')) {
                        try {
                            $contactModel->setData('read_id' , $contactId);
                            $contactModel->setData('style' ,'opacity: 0.4;' );
                            $contactModel->save();
                            $response['status'] = 'SUCCESS';
                            $response['message'] = "Message has been read";
                            echo json_encode($response);
                            return ;
                        } catch (Exception $e) {
                            Mage::log($e->getMessage() , null , 'contactadmin.log');
                        }                        
                    }
                    else {
                        $response['status'] = 'ERROR';
                        echo json_encode($response);
                        return ;
                    }
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = 'This contact is already exist!';
            echo json_encode($response);
            return ;
        }
    }
    /**
     * delete a contact in mongodb by ajax
     */
    public function ajaxdeleteAction() {
            $response = array();
            $contactModel =  Mage::getModel('salon/contact');
            $contactId = Mage::app()->getRequest()->getParam('id');
            if (isset($contactId) && $contactId) {
                $contactModel->load($contactId , 'entity_id');
                    try {
                        $contactModel->delete();
                        $response['status'] = 'SUCCESS';
                        $response['message'] = " The message was deleted from the your salon!";
                        echo json_encode($response);
                        return ;
                    } catch (Exception $e) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $e->getMessage();
                        echo json_encode($response);
                        return ;
                    }    
                
            }
            else {
                $response['status'] = 'ERROR';
                $response['message'] = 'This contact is already exist!';
                echo json_encode($response);
                return ;
            }
    }
    /**
     * return a record of contact from mongod by ajax
     */
    public function ajaxGetMessageAction() {
        $response = array();
        $messId = $this->getRequest()->getParam('id');
        $contactModel =  Mage::getModel('salon/contact');
        if (isset($messId) && $messId) {
            $contactModel->load($messId, 'entity_id');
            if($contactModel->getEntityId()) {
                try {
                    $contactModel->setData('style' ,'opacity: 0.4;' );
                    $contactModel->save();
                } catch (Exception $e) {
                    echo $this->__($e->getMessage());
                }
                
                $response = $contactModel->getData();
                $totalContact = Mage::helper('salon')->checkContact();
                if($totalContact < 5)
                {
                    $response['allow_sub'] = $totalContact;
                }
                $response['status'] = 'SUCCESS';
                echo json_encode($response);
                return ;
            }
                
        }
        $response['status'] = 'ERROR';
        $response['message'] = 'This message is already not exist!';
        echo json_encode($response);
        return ;
    }
}