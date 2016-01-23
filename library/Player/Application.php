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
     * Options of the Player_Application class.
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Instance of Player_Layout class.
     *
     * @var null
     */
    public $layout;

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
        
        $this->_environment = (string) $environment;

        if ($options !== null) {

            $this->setOptions($options);
            $this->setConfig();
            
        }
        
        $this->layout = new Player_Layout($this->getOptions('layout'));
        
    }

    /**
     * Gets the options of the Player_Application class.
     *
     * @return array
     */
    public function getOptions($params = null)
    {
        
        if($params != null) {
        
            if(isset($this->_options[$params])){

                return $this->_options[$params];
                
            }
            
        }
        
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

                if (is_array($value) && $level <= 4) {

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
     * Gets if the first download is done.
     *
     * @return boolean
     */
    public function getDownload()
    {
        
        $config = $this->getConfig();
        
        $flag = Player_Flags::getFlag('status', 'download');

        if(isset($config[$flag])){
            
            if(intval($config[$flag]) == 1){
            
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
    public function setDownload($connection)
    {
        
        if($connection->checkConnection($this->getEnvironment())){
            
            $session = new Player_Session('download');
            
            if(isset($_GET['layout'])){
                
                if($_GET['layout'] == 'download') {

                    if($session->getSession('download') == 0){
                        
                        $files  = Player_Flags::getFlag('files', 'download');
                        $path   = Player_Flags::getFlag('path');

                        $filename = $path['config'] . $files['file'];

                        $download = '"' . REAL_PATH . DIRECTORY_SEPARATOR . 'download.php"';

                        $params =
<<<HEREDOC
START /MIN /HIGH php -f $download EXIT
HEREDOC;

                        Player_File::setFile($filename, $params, true);

                        $shell = new COM('WScript.Shell');

                        $shell->Run($filename, 0, false);
						
						Player_Debug::setStatus('Starting download...', false, false, true);
                        
                        $session->setSession('download', 1);

                        Player_Utils::redirect('download');

                    }

                } else {

                    Player_Utils::redirect('download');
                    
                }
            
            } else {

                Player_Utils::redirect('download');

            }

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
     * Gets values of the flags from settings.
     *
     * @return boolean
     */
    public function setScreen()
    {

        if(isset($_GET['layout'])) {
            
            $this->layout->showLayout($_GET['layout']);
            
        }
        
    }
    
    /**
     * Gets values of the flags from settings.
     *
     * @return boolean
     */
    public function setPlay()
    {

        if(isset($_GET['layout'])) {

            if($_GET['layout'] == 'play'){
                
                $session    = new Player_Session('play');
        
                $play       = new Player_Play($session);
                
                $flags  = Player_Flags::getFlag();
                $media  = Player_Flags::getFlag('playlist', 'media');
                $path   = Player_Flags::getFlag('path', 'rel');
                $return = $play->run();
				
				if(Player_Debug::getClear()) {

					$session->clearSession();
					Player_Utils::redirect();

				} else {

					$server = 'http://' . $_SERVER["HTTP_HOST"];
					$params = '';

					if($return[$media['type']] == $media['library']) {

						$params =   '?library=' . $server . $path['library'] . $return[$media['xml']] . 
									'&picture=' . $server . $path['picture'];

					}

					$this->layout->media    = $server . $path['media'] . $return[$media['filename']] . $params;
					$this->layout->flags    = $flags;
					$this->layout->refresh  = $return[$media['duration']] . '; URL=' . $server . '/play';
					
				}

            } else {

                Player_Utils::redirect('play');

            }

        } else  {

            Player_Utils::redirect('play');

        }
            
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
            
            if(intval($config[$flag]) == 1){
            
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

        $player     = Player_Flags::getFlag('player');
        $user       = Player_Flags::getFlag('user');

        $send = true;

        if($connection->checkConnection($this->getEnvironment())){

            if(isset($_GET['layout'])) {

                if($_GET['layout'] == 'login'){

                    if(isset($_POST)) {
                        
                        $post = array(
                            
                            'login',
                            'password',
                            
                        );
                        
                        foreach ($post as $key => $value) {
                            
                            if(!isset($_POST[$value])) {
                                
                                $send = false;
                                
                            }
                            
                        }
                        
                        if($send){

                            $params = array(

                                $user['login']      => $_POST['login'],
                                $user['password']   => $_POST['password'],
                                $player['code']     => $validate->getActivation(),
                                $player['status']   => 1

                            );
                            
                            if($this->__runInstall($connection, $validate, $params)){

                                Player_Utils::redirect('download');

                            } else {
                                
                                $this->layout->status = 'Connection error. Try again.';
                                
                            }

                        }

                    }

                } else {

                    Player_Utils::redirect('login');

                }

            } else  {

                Player_Utils::redirect('login');

            }

        }

    }
    
    /**
     * Run installation.
     *
     * @return string
     */
    private function __runInstall($connection, $validate, $params = null)
    {
        
        $url        = Player_Flags::getFlag('url');
        $status     = Player_Flags::getFlag('status');
        $player     = Player_Flags::getFlag('player');
        $label      = Player_Flags::getFlag('label');

        $login = $connection->loadConnection($this->getEnvironment(), $url['login'], $params);

        if($login){
            
            $xml = Player_Convert::getXML($login, $label['config']);

            if(isset($xml[$status['active']])){

                if(intval($xml[$status['active']]) == 1){
                    
                    Player_File::setFile($this->getConfigFile(), $login, true);
                    return true;

                }

            }
            
        }
        
        return false;
        
    }
    
}