<?php

namespace Application\Login\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;


/**
 * ログインモデルのクラス
 */
class Login implements InputFilterAwareInterface
{
    /**
     * インプットのフィルタ
     * @var type 
     */
    protected $inputFilter;

    /**
     * インプットのフィルタの取得
     * @return type
     */
    public function getInputFilter()
    {
        if (!isset($this->inputFilter)) {
            // インプットのフィルタの初期化
            $inputFilter = new InputFilter();

            // ログイン名
            $inputFilter->add(array(
                'name' => 'login_name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'ログインID'
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'max' => 11,
                            'subject' => 'ログインID'
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function ($value) {
                                $isValid = preg_match("/^[a-zA-Z0-9@]+$/u",$value);
                                return $isValid;
                            },
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'APPLICATION_013',
                            ),
                        ),
                    ),
                ),
            ));

            // パスワード
            $inputFilter->add(array(
                'name' => 'login_password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'APPLICATION_003'
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\Alphanumeric',
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'min' => 8,
                            'max' => 20,
                            'subject' => 'パスワード'
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * インプットのフィルタの設定
     * @param InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
}
