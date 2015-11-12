<?php

namespace Application\Login\Form;

use Zend\Form\Form;


/**
 * ログインフォームのクラス
 */
class LoginForm extends Form
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // フォーム名の指定
        parent::__construct('login');
        // フォームの属性の指定
        $this->setAttribute('method', 'post');

        /*
         * ログイン名テキストボックス
         */
        $this->add(array(
            'name' => 'login_name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'loginName',
                'maxlength' => 11,
                'autocomplete' => 'off'
            ),
        ));

        /*
         * パスワードテキストボックス
         */
        $this->add(array(
            'name' => 'login_password',
            'type' => 'password',
            'attributes' => array(
                'id' => 'loginPw',
                'maxlength' => 20,
                'autocomplete' => 'off'
            ),
        ));

        /*
         * ログインボタン
         */
        $this->add(array(
            'name' => 'login_submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'ログイン',
                'id' => 'login_btn',
            ),
        ));
    }
}
