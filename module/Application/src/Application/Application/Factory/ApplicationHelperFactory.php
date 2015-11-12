<?php

namespace Application\Application\Factory;

use Application\Application\Helper\ApplicationHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * アプリケーションヘルプファクトリーのクラス
 */
class ApplicationHelperFactory implements FactoryInterface
{
    /**
     * アプリケーションヘルプサービスの作成
     * @param ServiceLocatorInterface $serviceLocator
     * @return ApplicationHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationHelper($serviceLocator->getServiceLocator());
    }
}
