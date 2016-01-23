<?php

class Player_Application
{

    protected $_autoloader;

    protected $_bootstrap;

    protected $_environment;
    
    protected $_session = array();
    
    protected $_access = array();
    
    protected $_install = array();
        
    protected $_options = array();
        
    protected $_config = array();
    
    protected $_timeout = 0;

    public function __construct($environment = null, $options = null)
    {
        
        $this->setTimeOut();
        $this->_environment = (string) $environment;

        require_once 'Player/Loader/Autoloader.php';
        $this->_autoloader = Player_Loader_Autoloader::getInstance();

        if (null !== $options) {

            if (is_string($options)) {
                
                $options = $this->setConfig($options);
                
            } elseif (is_array($options)) {
                
                $options = $this->setOptions($options);
                print 'string';
                
            }

            $this->setOptions($options);
            
        }
        
    }
    
    public function run()
    {
        
        //@TODO
        
    }

    public function setTimeOut($time = 0)
    {
        
        $this->_timeout = set_time_limit($time);
        
    }

    public function setOptions()
    {
        
        $array = array();
        
        foreach ($array as $key => $value) {
            
            if (is_array($value)) {
                
                $array[$key] = $value->setOptions();
                
            } else {
                
                $array[$key] = $value;
                
            }
            
        }
        
        return $array;
        
    }

    public function getOptions()
    {
        
        return $this->_options;
        
    }

    public function setConfig($configs)
    {
        
        if(file_exists($configs)){
        
            $this->_config = parse_ini_file($configs);
            
        }
        
    }

    public function getConfig()
    {
        
        return $this->_config;
        
    }
    
    public function setSession($session = null)
    {
        
        // @TODO
        
        session_start();

        if(isset($_SESSION['views'])) {
            
            $_SESSION['views'] = $_SESSION['views']+1;
        } else {
            
            $_SESSION['views'] = 1;
            echo "Views=". $_SESSION['views'];
        }
        
    }
    
    public function getSession()
    {
        
        return $this->_session;
        
    }
    
    public function setAcess()
    {
        
        return $this->_access;
        
    }
    
    public function getAcess()
    {
        
        // @TODO
        
    }
    
    public function setInstall()
    {
        
        return $this->_install;
        
    }
    
    private function __runInstall()
    {
        
        //@TODO
        
    }

    
}