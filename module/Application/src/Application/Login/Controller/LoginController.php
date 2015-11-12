<?php

namespace Application\Login\Controller;

use Zend\View\Model\ViewModel;
use Application\Login\Model\Login;
use Application\Login\Form\LoginForm;
use Zend\Session\SessionManager;
use Application\Application\Controller\BackEndController;


/**
 * ログインコントローラのクラス
 */
class LoginController extends BackEndController
{
    /**
     * 認証サービス情報
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * 認証サービス情報の取得
     * @return Zend\Authentication\AuthenticationService
     */
    private function getAuthService()
    {
        if (!isset($this->authService)) {
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        
        return $this->authService;
    }

    /**
     * ログインアクション
     * @return ViewModel
     */
    public function loginViewAction()
    {
        // 認証情報がある場合、customerページにリダイレクトする
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('customer');
        }

        // ログインフォーム初期化
        $loginForm = new LoginForm();
        // Request情報の取得
        $reguest = $this->getRequest();
        // エラーメッセージ
        $msg = null;

        // METHODがPOSTの場合
        if ($reguest->isPost()) {
            // ログインモデルの初期化
            $login = new Login();

            $loginForm->setInputFilter($login->getInputFilter());var_dump($reguest->getPost());die;
            $loginForm->setData($reguest->getPost());

            if ($loginForm->isValid()) {
                $data = $loginForm->getData();

                $id = explode('@', $data['login_name']);

                if (!isset($id[0]) || $id[0] == '' || !isset($id[1]) || $id[1] == '' || count($id) > 2) {
                    // $id must be 2 part: FACILITY_ID & OWNER_ID
                    $msg = 'APPLICATION_001';
                } else {
                    $facilityId = $id[0];
                    $ownerId = $id[1];
                    $password = $this->getLoginInfoSv()->getEncryptPassword($data['login_password']);
                    $checkLogin = $this->getLoginService()->checkLogin($facilityId, $ownerId, $password);

                    if ($checkLogin) {
                        $manager = new SessionManager();
                        $manager->regenerateId(true);

                        return $this->redirect()->toRoute('customer');
                    } else {
                        $msg = 'APPLICATION_001';
                    }
                }
            }
        }
        
        return new ViewModel(array('loginFrm' => $loginForm, 'msg' => $msg));
    }

    /**
     * Logout action
     * @return ViewModel
     */
    public function logoutAction()
    {
        $auth = $this->getAuthService();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }

        return $this->redirect()->toRoute('home');
    }
}
