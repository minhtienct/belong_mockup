<?php

namespace Application\Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;


/**
 * バックエンドコントローラの共通クラス
 */
class BackEndController extends AbstractActionController
{
    /**
     * ログイン情報サービス
     * @var \Application\Login\Service\LoginInfoService
     */
    protected $loginInfoSv;

    /**
     * ログサービス
     * @var Application\Application\Service\SystemLog;
     */
    protected $logSv;

    /**
     * ログインサービス
     * @var \Application\Login\Service\LoginService
     */
    protected $loginSv;
    
    /**
     * ビューヘルプ情報
     * @var type 
     */
    protected $viewHelperInfo;



    /**
     * ログイン情報サービスの取得
     * @return \Application\Login\Service\LoginInfoService
     */
    public function getLoginInfoSv()
    {
        if (!isset($this->loginInfoSv)) {
            $this->loginInfoSv = $this->getServiceLocator()->get('LoginInfoService');
        }
        
        return $this->loginInfoSv;
    }

    /**
     * $helperNameの値によって、ビューヘルプの取得
     * @param type $viewHelperName
     * @return type
     */
    protected function getViewHelper($viewHelperName)
    {
        if (!isset($this->viewHelperInfo)) {
            $this->viewHelperInfo = $this->getServiceLocator()->get('viewhelpermanager')->get($viewHelperName);
        }
        
        return $this->viewHelperInfo;
    }

    /**
     * ログサービスの取得
     * @return \Application\Application\Service\SystemLog
     */
    public function getLogService()
    {
        if (!isset($this->logSv)) {
            $this->logSv = $this->getServiceLocator()->get('SystemLogService');
        }
        
        return $this->logSv;
    }

    /**
     * ログインサービスの取得
     * @return \Application\Login\Service\LoginService
     */
    public function getLoginService()
    {
        if (!isset($this->loginSv)) {
            $this->loginSv = $this->getServiceLocator()->get('LoginService');
        }
        
        return $this->loginSv;
    }    
}
