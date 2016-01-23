<?php

/**
 * Player_Connect
 * 
 * Connects the player to server
 */
class Player_Connect
{

    /**
     * Protocol access server.
     *
     * @var string 'http'
     */
    protected $_protocol = 'http';

    /**
     * Subdomain of server name.
     *
     * @var string 'test'
     */
    protected $_developmentserver = 'test';

    /**
     * Subdomain of server name.
     *
     * @var string 'api'
     */
    protected $_subdomain = 'api';

    /**
     * Server name.
     *
     * @var string 'appserver.com'
     */
    protected $_domain = 'appserver.com';

    /**
     * Port access server.
     *
     * @var integer 80
     */
    protected $_port = 80;

    /**
     * Path server name.
     *
     * @var string ''
     */
    protected $_path = '';
    
    /**
     * Check the connection to server
     * 
     * @return boolean
     */
    public function checkConnection($environment = null) {
        
        if($environment == 'development') {
            
            $this->_subdomain = $this->_developmentserver;
            
        }
        
        if (@fsockopen($this->_subdomain . '.' . $this->_domain, $this->_port, $errno, $errstr, 5)) {
            
            return true;
            
        }
        
        return false;
        
    }
    
    /**
     * Load a URL from server
     * 
     * Sets and gets the validation of the player
     *
     * @param  string $environment null
     * @param  string $path null
     * @param  array $params null
     * @return array
     */
    public function loadConnection($environment = null, $path = null, $params = null) {
        
        if($environment == 'development') {
            
            $this->_subdomain = $this->_developmentserver;
            
        }
        
        if($this->checkConnection()) {
            
            $url = $this->_protocol . '://' . 
                    $this->_subdomain . '.' . 
                    $this->_domain . '/' . 
                    $this->_path . '/' . 
                    $path;
            
            $curl = curl_init();

            curl_setopt_array($curl,

                array(

                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $params,
                    CURLOPT_URL => $url,

                )

            );

            $return = curl_exec($curl);

            curl_close($curl);
            
            return utf8_encode($return);
            
        } else {
            
            return false;
            
        }

    }
    
    /**
     * Load a File from any server
     * 
     * Sets and gets the validation of the player
     *
     * @param  string $path null
     * @param  array $params null
     * @return array
     */
    public function loadFile($filename = null, $path = null, $overwrite = false) {
        
        if($this->checkConnection()) {
            
            $url = $path;
            
            $curl = curl_init();

            curl_setopt_array($curl,

                array(

                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_URL => $url,

                )

            );

            $return = curl_exec($curl);

            curl_close($curl);
            
            if(Player_File::setFile($filename, $return, $overwrite)) {
            
                return true;
                
            }
            
        } else {
            
            return false;
            
        }

    }
    
}