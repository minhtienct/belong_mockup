<?php

namespace Application\Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Application\Service\SystemLog;


/**
 * システムログファクトリーのクラス
 */
class SystemLogFactory implements FactoryInterface
{
    /**
     * システムログサービスの作成
     * @param ServiceLocatorInterface $serviceLocator
     * @return SystemLog
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SystemLog($serviceLocator);
    }
}
