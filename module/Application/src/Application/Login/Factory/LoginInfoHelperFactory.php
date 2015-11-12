<?php

namespace Application\Login\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\Login\Helper\LoginInfoHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ログイン情報ヘルプファクトリーのクラス
 */
class LoginInfoHelperFactory implements FactoryInterface
{
    /**
     * ログイン情報ヘルプサービスの作成
     * @param ServiceLocatorInterface $serviceLocator
     * @return LoginInfoHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new LoginInfoHelper($serviceLocator->getServiceLocator());
    }
}
