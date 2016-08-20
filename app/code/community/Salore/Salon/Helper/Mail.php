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
class Salore_Salon_Helper_Mail extends Mage_Core_Helper_Abstract
{
    public function sendEmail($salon, $templateEmail)
    {
            $storeId = Mage::app()->getStore()->getId();
            $fullName = Mage::getStoreConfig('salon/settings/salon_fullname');
            $fromEmail = Mage::getStoreConfig('salon/settings/salon_email');
            $templateId = Mage::getStoreConfig('salon/settings/remind_email_template');
            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($salon->getEmail(), $salon->getFirstname());
            $mailer->addEmailInfo($emailInfo);
        
            $mailer->setSender(array('name' => $fullName, 'email' => $fromEmail));
            $mailer->setStoreId($storeId);
            $mailer->setTemplateId($templateEmail);
            $mailer->setTemplateParams(array('salon'=>$salon));
            $mailer->send();
    }
    public function sendEmailForModerator($salon)
    {
        $storeId = Mage::app()->getStore()->getId();
        $fullName = Mage::getStoreConfig('salon/settings/salon_fullname');
        $fromEmail = Mage::getStoreConfig('salon/settings/salon_email');
        $moderators = str_replace(' ', '', Mage::getStoreConfig('salon/settings/salon_moderator'));
        $moderatorsArr = explode(',', $moderators);
        if (isset($moderatorsArr) && !empty($moderatorsArr)) {
            foreach ($moderatorsArr as $moderator)
            {
                $mailer = Mage::getModel('core/email_template_mailer');
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($moderator, 'moderator');
                $mailer->addEmailInfo($emailInfo);
                $mailer->setSender(array('name' => $fullName, 'email' => $fromEmail));
                $mailer->setStoreId($storeId);
                $mailer->setTemplateId('moderator_active_email_template');
                $mailer->setTemplateParams(array('salon'=>$salon));
                $mailer->send();
            }
        }
    }
}