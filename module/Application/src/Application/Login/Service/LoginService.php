<?php

namespace Application\Login\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\Result;

class LoginService
{

    /**
     * @var ServiceLocatorInterface 
     */
    protected $svLocator;

    /**
     * @var \Zend\Authentication\AuthenticationService 
     */
    protected $authService;

    /**
     * Content data of authentication result
     * @var array 
     */
    protected $authContent = array();

    /**
     * Identity value regex
     */
    const IDENDITY_VALUE_PATTERN = '/^[a-zA-Z0-9]+$/u';

    public function __construct(ServiceLocatorInterface $svLocator)
    {
        $this->svLocator = $svLocator;
    }

    /**
     * Get authentication service
     * @return \Zend\Authentication\AuthenticationService
     */
    private function getAuthService()
    {
        if (!$this->authService) {
            $this->authService = $this->svLocator->get('AuthService');
        }
        return $this->authService;
    }

    /**
     * 
     * @param AuthAdapter $authAdapter
     * @param type $name
     * @return boolean
     */
    private function isAuthenticated(AuthAdapter $authAdapter, $name)
    {
        $authService = $this->getAuthService();
        $rsAuth = $authService->authenticate($authAdapter);

        if ($rsAuth->getCode() == Result::SUCCESS) {
            $this->authContent[$name] = (array) $authAdapter->getResultRowObject(null, array('PASSWORD', 'BROWSE_PASSWORD'));
            return true;
        }
        return false;
    }

    /**
     * 
     * @param type $facilityId
     * @param type $ownerId
     * @param type $pass
     * @return boolean
     */
    public function checkLogin($facilityId, $ownerId, $pass)
    {
        //:: Check facility id & owner id value pattern
        if (!$this->isValidIdentityPattern($facilityId) || !$this->isValidIdentityPattern($ownerId)) {
            return false;
        }

        $dbAdapter = $this->svLocator->get('Zend\Db\Adapter\Adapter');

        // Check OWNER_ID
        $authAdapter = new AuthAdapter($dbAdapter
            , 'mst_owner', 'OWNER_ID', 'OWNER_ID'
            , '(?) AND DELETE_FLG = 0');

        $authAdapter
            ->setIdentity($ownerId)
            ->setCredential($ownerId);
        $chk = $this->isAuthenticated($authAdapter, 'OWNER');

        // Check FACILITY_ID
        $authAdapter1 = new AuthAdapter($dbAdapter
            , 'mst_facility', 'FACILITY_ID', 'PASSWORD'
            , '(?) AND DELETE_FLG = 0 AND OWNER_ID = ' . "'" . $ownerId . "'");

        $authAdapter1
            ->setIdentity($facilityId)
            ->setCredential($pass);
        $chk1 = $this->isAuthenticated($authAdapter1, 'FACILITY');

        if ($chk && $chk1) {
            //---------Set authentication storage
            $this->getAuthService()->getStorage()->write($this->authContent);
            return true;
        }
        return false;
    }

    /**
     * Add data to authentication storage
     * @param array $data
     */
    public function write2AuthStorage($data)
    {
        $curIdentity = $this->getAuthService()->getIdentity();
        if (is_array($data)) {
            $newIdentity = array_merge($curIdentity, $data);
            $this->authService->getStorage()->write($newIdentity);
        }
    }

    /**
     * Update value in authentication storage
     * @param string $key
     * @param string $subKey
     * @param type $value
     */
    private function updateAuthStorage($key, $subKey, $value)
    {
        $identity = $this->getAuthService()->getIdentity();
        if ($identity != null) {
            if (isset($identity[$key]) && isset($identity[$key][$subKey])) {
                $identity[$key][$subKey] = $value;
            }

            $this->authService->getStorage()->write($identity);
        }
    }

    /**
     * Update facility name in authentication storage
     * @param string $facilityName
     */
    public function updateFacilityName($facilityName)
    {
        $this->updateAuthStorage('FACILITY', 'FACILITY_NAME', $facilityName);
    }

    /**
     * Check identity value is matched pattern
     * @param type $idendityValue
     * @return int <b>1</b> if matched. <b>0</b> or <b>false</b> if not matched
     */
    private function isValidIdentityPattern($idendityValue)
    {
        return preg_match(self::IDENDITY_VALUE_PATTERN, $idendityValue);
    }
}
