<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\View\Model\ViewModel;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;

/**
 * モジュール名のクラス
 */
class Module
{
    /**
     * onBootstrap
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        // 共通の設定構成の取得
        $config = $e->getApplication()
                    ->getServiceManager()
                    ->get('Configuration');
        
        // セッションの構成。session_configがglobalファイルに宣言されていた
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config['session_config']);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();
        
        /**
         * Optional: If you later want to use namespaces, you can already store the
         * Manager in the shared (static) Container (=namespace) field
         */
        Container::setDefaultManager($sessionManager);

        $eventManager = $e->getApplication()->getEventManager();

        //--------Set to get layout from 'module_layouts' config key
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller = $e->getTarget();
            // コントローラのパースの取得
            $controllerClass = get_class($controller);
            // モジュール名の取得
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            // 共通の設定構成の取得
            $config = $e->getApplication()->getServiceManager()->get('config');
            
            // ﾚｲｱｳﾄの設定
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);

        //--------Set event to check authentication
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAuthenticated'));

        //--------Set event to catch dispatch error & render error
        $eventManager->attach(array(MvcEvent::EVENT_DISPATCH_ERROR, MvcEvent::EVENT_RENDER_ERROR, MvcEvent::EVENT_RENDER),
                              array($this, 'handleError'));
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * module.config.phpのパースの取得
     * @return module.config.phpのパース
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * 自動ロードの設定構成の取得
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * Authenticatedのチェック
     * @param MvcEvent $e
     * @return $response || null
     */
    public function checkAuthenticated(MvcEvent $e)
    {
        $ignoreCheck = array('api', 'api_test', 'revoke_token', 'home');

        if ($e->getRouteMatch()->getMatchedRouteName() == 'password' &&
           ($e->getRouteMatch()->getParam('action') == 'password_reset' ||
            $e->getRouteMatch()->getParam('action') == 'password_setting'))
        {
            return;
        }
        
        if (in_array($e->getRouteMatch()->getMatchedRouteName(), $ignoreCheck)) {
            return;
        }

        if (!$e->getApplication()->getServiceManager()->get('AuthService')->hasIdentity()) {
            $url = $e->getRouter()->assemble(array(), array('name' => 'home'));
            $response=$e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $response->sendHeaders();
            $e->stopPropagation(true);
            return $response;
        }
    }

    /**
     * ハドルエラーのチェック
     * @param MvcEvent $e
     * @return null
     */
    public function handleError(MvcEvent $e)
    {
        $error = $e->getError();
        $response = $e->getResponse();

        if ($response instanceof Response) {
            return;
        }

        $statusCode = $response->getStatusCode();
        if ( $statusCode == 200){
            return;
        }

        $logSv = $e->getApplication()->getServiceManager()->get('SystemLogService');
        $exception = $e->getParam('exception');
        if (null != $exception) {
            $logSv->err($exception->getMessage());
            $logSv->err($exception->getTraceAsString());
        }
        if ($e->getRouteMatch()->getMatchedRouteName() == 'api') {
            exit();
        }

        $evn = getenv('APPLICATION_ENV');

        if ($evn == "production") {
            /*
            $baseModel = new ViewModel();
            $baseModel->setTemplate('layout/layout');

            $model = new ViewModel();
            if ($statusCode == 404) {
                $model->setTemplate('error/production/404');
            }  else {
                $model->setTemplate('error/production/index');
            }

            $baseModel->addChild($model);
            $baseModel->setTerminal(true);

            $e->setViewModel($baseModel);
            $result = $e->getResult();

            $logSv = $e->getApplication()->getServiceManager()->get('SystemLogService');
            $exception = $e->getParam('exception');
            if (null != $exception) {
                $logSv->err($exception->getMessage());
                $logSv->err($exception->getTraceAsString());
            }

            if (get_class($result) == "Zend\View\Model\ViewModel") {
                $response = $e->getResponse();
                return $response;
            }
             *
             */

            $model = $e->getResult();
            if ($model instanceof ViewModel) {
                if ($statusCode == 404) {
                    $model->setTemplate('error/production/404');
                } else {
                    $model->setTemplate('error/production/index');
                }
            }
        } else {
            if ($error == Application::ERROR_CONTROLLER_NOT_FOUND) {
                //there is no controller named $e->getRouteMatch()->getParam('controller')
                $logText = 'The requested controller '
                        . $e->getRouteMatch()->getParam('controller') . '  could not be mapped to an existing controller class.';

                //you can do logging, redirect, etc here..
            }

            if ($error == Application::ERROR_CONTROLLER_INVALID) {
                //the controller doesn't extends AbstractActionController
                $logText = 'The requested controller '
                        . $e->getRouteMatch()->getParam('controller') . ' is not dispatchable';

                //you can do logging, redirect, etc here..
            }

            if ($error == Application::ERROR_ROUTER_NO_MATCH) {
                // the url doesn't match route, for example, there is no /foo literal of route
                $logText = 'The requested URL could not be matched by routing.';
                //you can do logging, redirect, etc here...
            }

            if ($error == Application::ERROR_EXCEPTION) {
                //echo $error;
            }
        }
    }
    
    
    
}
