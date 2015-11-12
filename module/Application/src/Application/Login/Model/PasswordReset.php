<?php

namespace Application\Login\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Db\TableGateway\TableGateway;

class PasswordReset implements InputFilterAwareInterface
{
    /*
     * Login input filter
     */

    protected $inputFilter;

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            // Login name filter
            $inputFilter->add(array(
                'name' => 'pass_facilityId',
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
     * @param type $facilityId
     * @param type $hashKey
     * @param type $adapter
     */
    public function savePasswordReset($facilityId, $hashKey, $timeExpired, $adapter)
    {
        $tablePassReset = new TableGateway('trn_password_reset', $adapter);
        $rowSet = $tablePassReset->select(array(
            'FACILITY_ID' => $facilityId,
            'DELETE_FLG' => 0,
        ));

        $row = $rowSet->current();
        if ($row) {
            //update Delete_flg = 1 of facility
            $dataUpdate = array('DELETE_FLG' => 1);
            $whereUpdate = array('FACILITY_ID' => $facilityId);
            $tablePassReset->update($dataUpdate, $whereUpdate);
        }

        //Create new row pass reset
        $data = array('FACILITY_ID' => $facilityId,
            'HASH_KEY' => $hashKey,
            'INSERT_TIME' => date('Y-m-d H:i:s'),
            'DELETE_FLG' => 0,
        );
        $tablePassReset->insert($data);
    }
}
