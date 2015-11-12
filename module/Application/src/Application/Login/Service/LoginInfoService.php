<?php

namespace Application\Login\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationServiceInterface;

class LoginInfoService
{

    /**
     * @var ServiceLocatorInterface 
     */
    protected $svLocator;

    /**
     * @var \Zend\Authentication\AuthenticationService 
     */
    protected $authService;

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
     * Get logged in info value
     * @param string $key
     * @param string $subKey
     * @return type
     */
    public function getLoginInfoValue($key, $subKey)
    {
        if ($this->getAuthService()->hasIdentity()) {
            $idStorage = $this->authService->getIdentity();
            if (isset($idStorage[$key]) && isset($idStorage[$key][$subKey])) {
                return $idStorage[$key][$subKey];
            }
        }
        return null;
    }

    /**
     * Get logged in facility name
     * @return string
     */
    public function getFacilityName()
    {
        return $this->getLoginInfoValue('FACILITY', 'FACILITY_NAME');
    }

    /**
     * Get logged in facility id
     * @return string
     */
    public function getFacilityId()
    {
        return $this->getLoginInfoValue('FACILITY', 'FACILITY_ID');
    }

    /**
     * Get logged in owner name
     * @return string
     */
    public function getOwnerName()
    {
        return $this->getLoginInfoValue('OWNER', 'OWNER_NAME');
    }
    
    /**
     * Get logged in owner name
     * @return string
     */
    public function getOwnerId()
    {
        return $this->getLoginInfoValue('OWNER', 'OWNER_ID');
    }
    
    /**
     * Encrypt password string
     * @param string $pass
     * @return string
     */
    public function getEncryptPassword($pass)
    {
        return md5($pass);
    }
}
