<?php

namespace Application\Login\Form;

use Zend\Form\Form;

class PasswordSettingForm extends Form
{

    public function __construct()
    {
        parent::__construct('password_setting');
        $this->setAttribute('method', 'post');

        /*
         * 事業所ID textbox
         */
        $this->add(array(
            'name' => 'facilityId',
            'type' => 'text',
            'attributes' => array(
                'autocomplete' => 'off',
                'maxlength' => 5,
                'class' => 'form-control',
            ),
        ));

        /*
         * 新しいパスワード textbox
         */
        $this->add(array(
            'name' => 'newPassword',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'minlength' => 8,
                'maxlength' => 20,
                'autocomplete' => 'off',
                'class' => 'form-control',
            ),
        ));

        /*
         * パスワード確認 textbox
         */
        $this->add(array(
            'name' => 'confirmPassword',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'minlength' => 8,
                'maxlength' => 20,
                'autocomplete' => 'off',
                'class' => 'form-control',
            ),
        ));

        /*
         * 更新 button
         */
        $this->add(array(
            'name' => 'btnUpdate',
            'attributes' => array(
                'type' => 'submit',
                'value' => '更新',
                'class' => 'btn btn-lg btn-primary',
            ),
        ));

        /*
         * Security CSRF
         */
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                )
            )
        ));
    }
}
