<?php

namespace Application\Application\Helper;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;


/**
 * アプリケーションヘルプのクラス
 */
class ApplicationHelper extends AbstractHelper
{
    /**
     * サービスロケータインタフェース変数の宣言
     * @var \Application\Application\Helper\ServiceLocatorInterface $svLocator
     */
    protected $svLocator;
    

    /**
     * コンストラクタ
     * @param \Application\Application\Helper\ServiceLocatorInterface $svLocator
     */
    public function __construct(ServiceLocatorInterface $svLocator)
    {
        $this->svLocator = $svLocator;
    }

    /**
     * ページのタイトルヘッダ名を取得する
     * @param string $requestName
     * @return string
     */
    public function getHeadTitle($requestName)
    {
        $config = $this->svLocator->get('Config');
        
        if (!isset($config['head_titles'])) {
            return '';
        }
              
        if (!isset($config['head_titles'][$requestName])) {
            return '';
        }
        
        return $config['head_titles'][$requestName];
    }
    
}
