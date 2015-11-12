<?php

namespace Application\Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\Application\Service\HtmlToPdf;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * HTMLからPDFに変換のクラス
 */
class HtmlToPdfFactory implements FactoryInterface
{
    /**
     * HTMLからPDFに変換のサービスの作成
     * @param ServiceLocatorInterface $serviceLocator
     * @return HtmlToPdf
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new HtmlToPdf($serviceLocator);
    }
}
