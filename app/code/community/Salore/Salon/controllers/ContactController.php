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
require_once Mage::getModuleDir('controllers', 'Mage_Contacts').DS.'IndexController.php';
class Salore_Salon_ContactController extends Mage_Contacts_IndexController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * save contact information to mongodb after set data from form
     */
    public function postAction() {
        $error = array('message' => null);
        $contactParams = $this->getRequest()->getParams();
        $contactMongo = Mage::getModel('salon/contact');
        $salonData = Mage::registry('currentsalon')->getData();
         //if ($this->checkFormData($contactMongo, $contactParams, $error)) { 
            $salonId = Mage::registry('currentsalon')->getEntityId();
            if (isset($salonId) && $salonId) {
                $contactMongo->setData('salon_id' , $salonId );
                $contactMongo->setData('entity_id' , uniqid('message'));
                $contactMongo->setData('create_at' , strtotime('now'));
                $contactMongo->setData('create_date' ,  Mage:: getModel( 'core/date')->timestamp(time()));
                try {
                    $this->checkFormData($contactMongo, $contactParams, $error);
                    $contactMongo->save();
                    $mail = Mage::getModel('core/email');
                    $toName = $salonData['firstname']. ' ' .$salonData['lastname'];
                    $mail->setToName($toName);
                    $mail->setToEmail($salonData['email']);
                    $mail->setBody($contactParams['comment']);
                    $mail->setSubject($contactParams['subject']);
                    $mail->setFromEmail($contactParams['email']);
                    $mail->setFromName($contactParams['name']);
                    $mail->setType('text');
                    $mail->send();
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
            }
            else {
                Mage::getSingleton('core/session')->addError($this->__('This salon is not exist!'));
            }
        //}
        // else {
        //    Mage::getSingleton('core/session')->setData('salon_contact_data', $contactParams);
        //    Mage::getSingleton('core/session')->addError($error['message']);
        //} 
        //$this->_redirectReferer();
            $url = Mage::helper('salon')->getUrl('contact');
            $this->_redirectUrl($url);
    }
    public function checkFormData($contactMongo, $contactParams, &$error) {
        if(isset($contactParams['name']) && $contactParams['name'] ) {
            $contactMongo->setData('contactname' , $contactParams['name'] );
        }
        else {
            $error['message'] .= $this->__('Please enter your name!');
        }
        if (isset($contactParams['email']) && $contactParams['email']) {
            $contactMongo->setData('email' , $contactParams['email'] );
        }
        else {
            $error['message'] .= '</br>' . $this->__('Please enter your email!');
        }
        if (isset($contactParams['subject']) && $contactParams['subject']) {
            $contactMongo->setData('subject' , $contactParams['subject'] );
        }
        else {
            $error['message'] .= '</br>' . $this->__('Please enter subject!');
        }
        if (isset($contactParams['telephone']) && $contactParams['telephone']) {
            $contactMongo->setData('telephone' , $contactParams['telephone'] );
        }
        else {
            $error['message'] .= '</br>' . $this->__('Please enter your telephone!');
        }
        if (isset($contactParams['comment']) && $contactParams['comment']) {
            $contactMongo->setData('comment' , $contactParams['comment'] );
        }
        else {
            $error['message'] .= '</br>' . $this->__('Please enter your comment!');
        }
        if(isset($error['message'])); {
            $error['message'] = trim($error['message'], '</br>');
            return false;
        }
        return true;
    }
}