<?php

class Player_Application
{

    protected $_autoloader;

    protected $_bootstrap;

    protected $_environment;
    
    protected $_session = array();
    
    protected $_access = false;
    
    protected $_install = array();
        
    protected $_options = array();
        
    protected $_config = array();
    
    protected $_timeout = 0;

    public function __construct($environment = null, $options = null)
    {
        
        require_once 'Player/Loader/Autoloader.php';
        $this->_autoloader = Player_Loader_Autoloader::getInstance();
        
        $this->setTimeOut();
        $this->_environment = (string) $environment;

        if ($options !== null) {

            if (is_string($options)) {
                
                $options = $this->setConfig($options);
                
            } elseif (is_array($options)) {
                
                $options = $this->setOptions($options);
                
            }

            $this->_config = $this->setOptions($options);
            
        }
        
    }
    
    public function run()
    {
        
        if(!$this->getAcess()){
            
           $this->setAcess(); 
            
        }

        //@TODO
        
    }

    public function setTimeOut($time = 0)
    {
        
        $this->_timeout = set_time_limit((int) $time);
        
    }

    public function setOptions($options)
    {
        
        $array = array();
        
        foreach ($array as $key => $value) {
            
            if (is_array($value)) {
                
                $array[$key] = $this->setOptions($options);
                
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
        
            $this->_config = Player_Convert::getIni($configs);
            
        }
        
    }

    public function getConfig()
    {
        
        return $this->_config;
        
    }
    
    public function setSession($session = null)
    {
        
        // @TODO
        
    }
    
    public function getSession()
    {
        
        return $this->_session;
        
    }
    
    public function setAcess()
    {
        
        $status = new Player_Validate();
        
        if($status->getValidate()){
            
            return $this->_access = true;
            
        }
        
    }
    
    public function getAcess()
    {
        
        return $this->_access;
        
    }
    
    public function getEnvironment()
    {
        
        return $this->_environment;
        
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