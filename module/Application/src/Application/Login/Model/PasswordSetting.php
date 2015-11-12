<?php

namespace Application\Login\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Db\TableGateway\TableGateway;

class PasswordSetting implements InputFilterAwareInterface
{
    /*
     * Password input filter
     */

    protected $inputFilter;

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            /**
             * 事業所ID textbox
             */
            $inputFilter->add(array(
                'name' => 'facilityId',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'subject' => '事業所ID'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\Alphanumeric',
                    )
                ),
            ));

            /**
             * 新パスワードの入力
             */
            $inputFilter->add(array(
                'name' => 'newPassword',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'subject' => '新パスワード',
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
                            'subject' => '新しいパスワード'
                        ),
                    ),
                ),
            ));

            /**
             * 新パスワードの確認
             */
            $inputFilter->add(array(
                'name' => 'confirmPassword',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'subject' => '新パスワードの確認',
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function ($value, $context) {
                                $isValid = strcmp($value, $context['newPassword']) == 0;
                                return $isValid;
                        },
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'APPLICATION_007',
                            ),
                        ),
                    ),
                ),
            ));

            /*
             * Security CSRF
             */
            $inputFilter->add(array(
                'name' => 'csrf',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Csrf',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Csrf::NOT_SAME => "CMN_002",
                            ),
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     *
     * @param type $hashKey
     * @param type $adapter
     * @return type
     */
    public function getPasswordResetByHash($hashKey, $adapter)
    {
        $tablePassReset = new TableGateway('trn_password_reset', $adapter);
        $rowSet = $tablePassReset->select(array(
            'HASH_KEY' => $hashKey,
            'DELETE_FLG' => 0,
        ));

        $row = $rowSet->current();
        return $row;
    }

    /**
     *
     * @param type $hashKey
     * @param type $adapter
     * @return boolean
     */
    public function isValid_HashKey($hashKey, $timeExpired, $adapter)
    {
        $row = $this->getPasswordResetByHash($hashKey, $adapter);
        if (!$row) {
            return false;
        }
        //Check time expired.
        $second = 60;
        $timeHash = strtotime($row->INSERT_TIME) + ($timeExpired * $second);
        if (time() > $timeHash) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param type $facilityId
     * @param type $hashKey
     * @param type $adapter
     * @return type
     */
    public function isMatchFacilityHash($facilityId, $hashKey, $adapter)
    {
        $tablePassReset = new TableGateway('trn_password_reset', $adapter);
        $rowSet = $tablePassReset->select(array(
            'FACILITY_ID' => $facilityId,
            'HASH_KEY' => $hashKey,
            'DELETE_FLG' => 0,
        ));

        $row = $rowSet->current();

        if (!$row) {
            //update HaskKey Delete_flg = 1
            $dataUpdate = array('DELETE_FLG' => 1);
            $whereUpdate = array('HASH_KEY' => $hashKey);
            $tablePassReset->update($dataUpdate, $whereUpdate);
            return false;
        }

        return true;
    }

    /**
     *
     * @param type $facilityId
     * @param type $password
     * @param type $hashKey
     * @param type $adapter
     */
    public function updatePassword($facilityId, $password, $hashKey, $adapter)
    {
        //Update Pass reset
        $tablePassReset = new TableGateway('trn_password_reset', $adapter);
        $dataUpdate = array('DELETE_FLG' => 1);
        $tablePassReset->update($dataUpdate, array('HASH_KEY' => $hashKey));

        //Update password facility
        $tableFacility = new TableGateway('mst_facility', $adapter);
        $dataUpdate = array('PASSWORD' => $password);
        $tableFacility->update($dataUpdate, array('FACILITY_ID' => $facilityId));
    }
}
