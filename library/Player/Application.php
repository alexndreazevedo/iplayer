<?php

/**
 * Player_Application
 * 
 * Run application
 */
class Player_Application
{

    /**
     * Storages the Player_Loader_Autoloader instance class.
     *
     * @var Player_Loader_Autoloader
     */
    protected $_autoloader;

    /**
     * Environment of running.
     *
     * @var string
     */
    protected $_environment;

    /**
     * Session for the playing mode.
     *
     * @var array
     */
    protected $_session = array();

    /**
     * Validate the first download.
     *
     * @var boolean
     */
    protected $_download = false;

    /**
     * Install player options.
     *
     * @var array
     */
    protected $_install = array();

    /**
     * Settings from .ini file.
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Sets the timeout of running.
     *
     * @var integer
     */
    protected $_timeout = 0;

    /**
     * Options of the Player_Application class.
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Player_Application class
     * 
     * Install and play the player
     *
     * @param  string $environment null
     * @param  array $options null
     * @return void
     */
    public function __construct($environment = null, $options = null)
    {
        require_once 'Player/Loader/Autoloader.php';
        $this->_autoloader = Player_Loader_Autoloader::getInstance();
        
        $this->load();
        
        $this->setConstruct($environment, $options);
        
    }

    /**
     * Loads function definition for extends classes.
     * This class is inserted before the Player_Application definitions
     *
     * @return void
     */
    public function load() {}

    /**
     * Run function definition for extends classes.
     * This class is inserted after the Player_Application definitions
     *
     * @return void
     */
    public function run() {}

    /**
     * Construct function for run between load() and run() extends classes.
     * Sets timeout, environment of running, options to settings and flags.
     *
     * @param  string $environment null
     * @param  array $options null
     * @return void
     */
    public function setConstruct($environment = null, $options = null) {
        
        $this->setTimeOut();
        $this->_environment = (string) $environment;

        if ($options !== null) {

            $this->setOptions($options);
            $this->setConfig();
            
        }
        
    }

    /**
     * Sets timeout to script.
     *
     * @param  integer $time 0
     * @return void
     */
    public function setTimeOut($time = 0)
    {
        
        $this->_timeout = set_time_limit($time);
        
    }

    /**
     * Gets the options of the Player_Application class.
     *
     * @return array
     */
    public function getOptions()
    {
        
        return $this->_options;
        
    }

    /**
     * Sets the options of the Player_Application class.
     *
     * @param  array $options null
     * @param  integer $level -1
     * @return array
     */
    public function setOptions($options = null, $level = -1)
    {
        
        $level++;
        $return = array();
        
        if(is_string($options) && $level == 0) {
            
            $return['file'] = $options;
            
        } else if(is_string($options)) {
            
            $return = $options;
            
        } else if(is_array($options)) {

            foreach ($options as $key => $value) {

                if (is_array($value)) {

                    $return[$key] = $this->setOptions($options, $level);

                } else {

                    $return[$key] = $value;

                }

            }
        
        }
        
        if($level == 0) {
            
            return $this->_options = $return;
            
        } else {

            return $return;
            
        }
        
    }

    /**
     * Gets .ini of the settings.
     *
     * @return array
     */
    public function getConfigFile()
    {
        
        $file = $this->_options;
        
        if(isset($file['file'])) {
        
            return $file['file'];
            
        } else {
            
            return false;
            
        }
        
    }

    /**
     * Gets the settings.
     *
     * @return array
     */
    public function getConfig()
    {
        
        return $this->_config;
        
    }

    /**
     * Sets the settings from .ini file.
     *
     * @param  array $configs null
     * @return array
     */
    public function setConfig()
    {
        
        $filename   = $this->getConfigFile();
        $label      = Player_Flags::getFlag('label');
        
        if($filename != null) {

            if(file_exists($filename)){
                
                $file = Player_File::getFile($filename);

                $this->_config = Player_Convert::getXML($file, $label['config']);

            }

        }
        
    }

    /**
     * Gets sessions of the playing mode.
     *
     * @return array
     */
    public function getSession()
    {
        
        return $this->_session;
        
    }

    /**
     * Sets sessions of the playing mode.
     *
     * @param  array $session null
     * @return array
     */
    public function setSession($session = null)
    {
        
        //@TODO
        
    }
    
    /**
     * Gets if the first download is done.
     *
     * @return boolean
     */
    public function getDownload()
    {
        
        $config = $this->getConfig();
        
        $flag = Player_Flags::getFlag('status', 'download');

        if(isset($config[$flag])){
            
            if($config[$flag]){
            
                return $this->_download = true;
                
            } else {
            
                return $this->_download = false;
                
            }
            
        } else {
            
            return $this->_download = false;
            
        }
        
    }
    
    /**
     * Sets the first download to done.
     *
     * @return boolean
     */
    public function setDownload()
    {

        $download = new Player_Connect_Download($this->getEnvironment(), $this->getConfigFile());
        
        if($download->run()){
        
            $this->_download = true;
            
        }
        
        return $this->_download;
        
    }
    
    /**
     * Gets the environment to playing mode.
     *
     * @return string
     */
    public function getEnvironment()
    {
        
        return $this->_environment;
        
    }
    
    /**
     * Gets values of the flags from settings.
     *
     * @return boolean
     */
    public function getInstall()
    {
        
        $config = $this->getConfig();
        
        $flag = Player_Flags::getFlag('status', 'active');

        if(isset($config[$flag])){
            
            if($config[$flag]){
            
                return $this->_install = true;
                
            } else {
            
                return $this->_install = false;
                
            }
            
        } else {
            
            return $this->_install = false;
            
        }
        
    }
    
    /**
     * Sets installation.
     *
     * @return string
     */
    public function setInstall($connection, $validate)
    {
        
        if($this->__runInstall($connection, $validate)){
            
            return true;

        }
        
        return false;
        
    }
    
    /**
     * Run installation.
     *
     * @return string
     */
    private function __runInstall($connection, $validate)
    {
        
        $url        = Player_Flags::getFlag('url');
        $status     = Player_Flags::getFlag('status');
        $player     = Player_Flags::getFlag('player');
        $user       = Player_Flags::getFlag('user');
        $label      = Player_Flags::getFlag('label');

        $params = array(

            $user['login']      => 'multclick',
            $user['password']   => 'q6k1LB',
            $player['code']     => $validate->getActivation(),
            $player['status']   => 1

        );

        $login = $connection->loadConnection($this->getEnvironment(), $url['login'], $params);

        if($login){
            
            Player_File::setFile($this->getConfigFile(), $login, true);
            
            $xml = Player_Convert::getXML($login, $label['config']);
            
            $params = array(

                $player['id']   =>$xml[$player['id']]

            );

            $connection->loadConnection($this->getEnvironment(), $url['last'], $params);

            if($xml[$status['active']]) {

                return true;

            }
            
        }
        
        return false;
        
    }
    
}