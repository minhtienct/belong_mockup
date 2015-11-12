<?php

namespace Application\Login\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginInfoHelper extends AbstractHelper
{

    /**
     * @var \Application\Login\Service\LoginInfoService 
     */
    protected $loginInfoSv;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $svLocator;

    public function __construct(ServiceLocatorInterface $svLocator)
    {
        $this->svLocator = $svLocator;
    }

    /**
     * Get login info service
     * @return \Application\Login\Service\LoginInfoService
     */
    private function getLoginInfoSv()
    {
        if (!$this->loginInfoSv) {
            $this->loginInfoSv = $this->svLocator->get('LoginInfoService');
        }
        return $this->loginInfoSv;
    }

    /**
     * Get login facility name
     * @return null|string
     */
    public function getFacilityName()
    {
        return $this->getLoginInfoSv()->getFacilityName();
    }
    
    /**
     * Get login facility id
     * @return null|string
     */
    public function getFacilityId()
    {
        return $this->getLoginInfoSv()->getFacilityId();
    }
    
    /**
     * Get login owner name
     * @return null|string
     */
    public function getOwnerName()
    {
        return $this->getLoginInfoSv()->getOwnerName();
    }
}
