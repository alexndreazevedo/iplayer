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
     * Validate the access to server.
     *
     * @var boolean
     */
    protected $_access = false;

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
     * Flags the activation, install and playing mode.
     *
     * @var array
     */
    protected $_flag = array();

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
        
        $this->setFlag(
            
            array(
                
                'status'  => array(
                    
                    'active'    => 'ativo',
                    'run'       => 'inicializa',
                    'download'  => 'fimciclo',
                    
                ),
                
                'url'  => array(
                    
                    'login'     => 'login',
                    'config'    => 'config',
                    'access'    => 'ultimoacesso',
                    
                ),
                
                'player'  => array(
                    
                    'id'        => 'idplayer',
                    'name'      => 'nome',
                    'screen'    => 'player',
                    'sector'    => 'idponto',
                    'code'      => 'senhaAc',
                    'status'    => 'configurado',
                    
                ),
                
                'user'  => array(
                    
                    'id'        => 'cliente',
                    'login'     => 'login',
                    'password'  => 'senha',
                    'name'      => 'nomecliente',
                    'category'  => 'segmento',
                    'sector'    => 'idponto',
                    
                ),
                
                'settings'  => array(
                    
                    'start'     => 'hrentradaplayer',
                    'end'       => 'hrsaidaplayer',
                    'width'     => 'resolucaolar',
                    'height'    => 'resolucaoalt',
                    'duration'  => 'tempoloop',
                    'off'       => 'deslautomaticoplayer',
                    'status'    => 'inativar',
                    
                ),
                
            )
            
        );
        
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
        
        if(isset($this->_options['file'])) {
        
            return $this->_options['file'];
            
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
        
        $filename = $this->getConfigFile();
        
        if($filename != null) {

            if(file_exists($filename)){

                $this->_config = Player_Convert::getIni($filename);

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
     * Gets if the access to server is allow.
     *
     * @return boolean
     */
    public function getAcess()
    {
        
        return $this->_access;
        
    }
    
    /**
     * Checks and set the access to server.
     *
     * @return boolean
     */
    public function setAcess()
    {
        
        $status = new Player_Validate();
        
        if($status->getValidate()){
            
            return $this->_access = true;
            
        }
        
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
     * Gets flags.
     *
     * @param  string $flag null
     * @param  string $sub null
     * @return string
     */
    public function getFlag($flag = null, $sub = null)
    {
        
        if(isset($this->_flag[$flag])) {
        
            if(isset($this->_flag[$flag][$sub])) {
            
                return $this->_flag[$flag][$sub];
                
            } else {
            
                return $this->_flag[$flag];
                
            }
            
            
        } else {
        
            return false;
            
        }
        
    }
    
    /**
     * Sets flags.
     *
     * @param  array $flags array
     * @return array
     */
    public function setFlag($flags = array())
    {
        
        return $this->_flag = $flags;
        
    }
    
    /**
     * Gets values of the flags from settings.
     *
     * @return boolean
     */
    public function getInstall()
    {
        
        $config = $this->getConfig();
        
        $flag = $this->getFlag('status', 'active');

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
        
        $url        = $this->getFlag('url');
        $status     = $this->getFlag('status');
        $player     = $this->getFlag('player');
        $user       = $this->getFlag('user');
        
        if($this->__runInstall($connection, $validate, $url, $status, $player, $user)){
            
            return true;

        } else {
            
            return false;
            
        }
        
    }
    
    /**
     * Run installation.
     *
     * @return string
     */
    private function __runInstall($connection, $validate, $url, $status, $player, $user)
    {

        $params = array(

            $user['login']      => '',
            $user['password']   => '',
            $player['code']     => $validate->getActivation(),
            $player['status']   => 1

        );

        $login = $connection->loadConnection($this->getEnvironment(), $url['login'], $params);

        $ini = Player_Convert::setIni($login, $this->getConfigFile(), true);

        $params = array(

            $player['id']   =>$ini[$player['id']]

        );

        $access = $connection->loadConnection($this->getEnvironment(), $url['access'], $params);
        
        if($ini[$status['active']]) {
            
            return true;

        } else {
            
            return false;
            
        }
        
    }

    
}