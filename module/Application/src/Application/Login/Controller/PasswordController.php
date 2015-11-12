<?php

namespace Application\Login\Controller;

use Application\Application\Controller\BackEndController;
use Application\Login\Form\PasswordForm;
use Application\Login\Form\PasswordResetForm;
use Application\Login\Model\PasswordReset;
use Application\Login\Form\PasswordSettingForm;
use Application\Login\Model\PasswordSetting;
use Zend\View\Model\ViewModel;
use Kdl\Mail\Mailer;

class PasswordController extends BackEndController
{

    /**
     * @var \Belongings\Facility\Model\FacilityTable
     */
    protected $facilityTable;

    /**
     * Get facility table
     * @return \Belongings\Facility\Model\FacilityTable
     */
    private function getFacilityTable()
    {
        if (!$this->facilityTable) {
            $sm = $this->getServiceLocator();
            $this->facilityTable = $sm->get('Facility\Model\FacilityTable');
        }
        return $this->facilityTable;
    }

    /**
     *
     * @return ViewModel
     */
    public function changePasswordAction()
    {
        $passwordForm = new PasswordForm();
        $messageInfo = null;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $passwordForm->setData($request->getPost());

            if ($passwordForm->isValid()) {
                $facilityId = $this->getLoginInfoSv()->getFacilityId();
                $ownerId = $this->getLoginInfoSv()->getOwnerId();

                $formData = $passwordForm->getData();

                $oldPassword = isset($formData['oldPassword']) ? $formData['oldPassword'] : null;
                $confrimPassword = isset($formData['confirmPassword']) ? $formData['confirmPassword'] : null;

                $oldPassword = $this->getLoginInfoSv()->getEncryptPassword($oldPassword);
                $confrimPassword = $this->getLoginInfoSv()->getEncryptPassword($confrimPassword);

                if ($this->getFacilityTable()->checkOldPassword($facilityId, $ownerId, $oldPassword)) {
                    $this->getFacilityTable()->updatePassword($facilityId, $ownerId, $confrimPassword);

                    //::::: Check login with new password
                    $this->getLoginService()->checkLogin($facilityId, $ownerId, $confrimPassword);
                    $messageInfo = "APPLICATION_012";

                } else {
                    $translator = $this->getServiceLocator()->get('translator');
                    $passwordForm->get('oldPassword')->setMessages(array(
                        $translator->translate('APPLICATION_006')
                    ));
                }
            }
        }

        //:::: Change layout
        $this->layout('layout/layout');

        return new ViewModel(array(
            'passwordForm' => $passwordForm,
            'messageInfo' => $messageInfo
        ));
    }

    public function passwordResetAction()
    {
        $form = new PasswordResetForm();
        $passwordReset = new PasswordReset();
        $messageInfo = null;
        $messageError = null;

        $request = $this->getRequest();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $translator = $this->getServiceLocator()->get('translator');
        $configs = $this->getServiceLocator()->get('config');
        $timeExpired = $configs['time_manager']['time_password_expired'];

        if ($request->isPost()) {
            $form->setInputFilter($passwordReset->getInputFilter());
            $formData = $request->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $facilityRow = $this->getFacilityTable()->findFacilityById($formData->pass_facilityId);
                if ($facilityRow) {
                    $facilityId = $facilityRow->FACILITY_ID;
                    $facilityName = $facilityRow->FACILITY_NAME;
                    $mailAdrress = $facilityRow->FACILITY_MAIL_ADDRESS;
                    $hashKey = md5($facilityId + time());

                    //Save
                    $passwordReset->savePasswordReset($facilityId, $hashKey, $timeExpired, $adapter);

                    //send mail
                    $mailTemplate = isset($configs['mail_templates']['password_reset']) ?
                        $configs['mail_templates']['password_reset'] : array();

                    $subject = isset($mailTemplate['subject']) ? $mailTemplate['subject'] : '';
                    $fileBody = isset($mailTemplate['body']) ? $mailTemplate['body'] : '';
                    $toAddresses = array($mailAdrress);

                    //$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
                    $url = "https://" . $_SERVER['HTTP_HOST'] . "/password/password_setting?hashKey=" . $hashKey;
                    $subject = str_replace('__facility_name_placeholder__', $facilityName, $subject);


                    $mail = new Mailer($this->getServiceLocator());
                    $body = $mail->getBodyFromTemplate($fileBody);
                    if ($body != false) {
                        $body = str_replace('__facility_name_placeholder__', $facilityName, $body);
                        $body = str_replace('__url_placeholder__', $url, $body);
                    }

                    if ($body != '' && $mailAdrress != '' && $mail->sendMail($subject, $body, $toAddresses)) {
                        $messageInfo = 'APPLICATION_010';
                    } else {
                        $messageError = "APPLICATION_011";
                    }
                } else {
                    $form->get('pass_facilityId')->setMessages(array($translator->translate('APPLICATION_009')));
                }
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'messageInfo' => $messageInfo,
            'messageError' => $messageError
        ));
    }

    public function passwordSettingAction()
    {
        $form = new PasswordSettingForm();
        $passSetting = new PasswordSetting();
        $error_HashKey = null;
        $isSuccess = false;

        $request = $this->getRequest();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $translator = $this->getServiceLocator()->get('translator');
        $hashKey = $this->params()->fromQuery('hashKey');

        $configs = $this->getServiceLocator()->get('config');
        $timeExpired = $configs['time_manager']['time_password_expired'];

        if ($passSetting->isValid_HashKey($hashKey, $timeExpired, $adapter) == false) {
            $error_HashKey = 'APPLICATION_008';
        }

        if ($request->isPost()) {
            $form->setInputFilter($passSetting->getInputFilter());
            $formData = $request->getPost();
            $form->setData($formData);

            $facilityId = $formData->facilityId;
            $password = $this->getLoginInfoSv()->getEncryptPassword($formData->newPassword);

            if ($form->isValid()) {
                $passResetRow = $passSetting->isMatchFacilityHash($facilityId, $hashKey, $adapter);
                if (!$passResetRow) {
                    $error_HashKey = 'APPLICATION_008';
                } else {
                    $passSetting->updatePassword($facilityId, $password, $hashKey, $adapter);
                    $isSuccess = true;
                }
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'error_HashKey' => $error_HashKey,
            'isSuccess' => $isSuccess,
            'hashKey' => $hashKey
        ));
    }
}
