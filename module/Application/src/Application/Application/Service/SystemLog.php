<?php

namespace Application\Application\Service;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * システムログのクラス
 */
class SystemLog
{
    /**
     *
     * @var type 
     */
    protected $svLocator;
    
    /**
     *
     * @var type 
     */
    protected $logger;
    
    /**
     *
     * @var type 
     */
    protected $filename = 'system.log';
    
    /**
     * 
     */
    const LOG_DIR = 'var/log';
    
    
    /**
     * 
     * @param ServiceLocatorInterface $svLocator
     * @param type $filename
     */
    public function __construct(ServiceLocatorInterface $svLocator, $filename = null) {
        $this->svLocator = $svLocator;
        
        if (null !=  $filename) {
            $this->filename = $filename;
        }        
        
        $writer = new Stream(HOME_DIR . '/' . self::LOG_DIR . '/' . $this->filename );        
        $this->logger = new  Logger();        
        $this->logger->addWriter($writer);        
    }
    
    /**
     * ローラ名の取得
     * @param type $name
     * @return string
     * @throws \Exception
     */
    public function __get($name) {
        if ($name == 'logger') {
            return $this->$name;
        }
        
        throw new \Exception('プロパティーが定義されていません');
    }
    
    /**
     * 
     * @param type $priority
     * @param type $message
     * @param type $extra
     */
    public function log($priority, $message, $extra = array())
    {        
        if (is_array($message) || is_object($message)) {
            ob_start();
            $message = print_r($message, true);
            ob_get_clean();
        }
        $this->logger->log($priority, $message, $extra);
    }
    
    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function emerg($message, $extra = array())
    {
        return $this->log($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function alert($message, $extra = array())
    {
        return $this->log(Logger::ALERT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function crit($message, $extra = array())
    {
        return $this->log(Logger::CRIT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function err($message, $extra = array())
    {
        return $this->log(Logger::ERR, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function warn($message, $extra = array())
    {
        return $this->log(Logger::WARN, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function notice($message, $extra = array())
    {
        return $this->log(Logger::NOTICE, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function info($message, $extra = array())
    {
        return $this->log(Logger::INFO, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function debug($message, $extra = array())
    {
        return $this->log(Logger::DEBUG, $message, $extra);
    }
}