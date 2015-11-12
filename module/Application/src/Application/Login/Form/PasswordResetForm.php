<?php

namespace Application\Login\Form;

use Zend\Form\Form;

class PasswordResetForm extends Form
{

    public function __construct()
    {
        parent::__construct('password_reset');
        $this->setAttribute('method', 'post');

        /*
         * FACILITY_ID
         */
        $this->add(array(
            'name' => 'pass_facilityId',
            'type' => 'text',
            'attributes' => array(
                'id' => 'pass_facilityId',
                'maxlength' => 5,
                'placeholder' => ' 事業所ID',
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

        /*
         * 送信 button
         */
        $this->add(array(
            'name' => 'pass_submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => '送信',
                'id' => 'pass_submit',
            ),
        ));
    }
}
