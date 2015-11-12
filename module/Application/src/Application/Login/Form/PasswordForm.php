<?php

namespace Application\Login\Form;

use Application\Login\Form\PasswordInputFilter;
use Kdl\Form\BaseUpdateForm;

class PasswordForm extends BaseUpdateForm
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setInputFilter(new PasswordInputFilter());

        /*
         * 現在のパスワード textbox
         */
        $this->add(array(
            'name' => 'oldPassword',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'maxlength' => 20,
                'autocomplete' => 'off',
                'class' => 'form-control',
            ),
        ));

        /*
         * 新パスワードの入力 textbox
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
         * 新パスワードの確認 textbox
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
            'name' => 'updateBtn',
            'attributes' => array(
                'type' => 'submit',
                'value' => '更新',
                'class' => 'btn btn-lg btn-primary',
            ),
        ));

    }
}
