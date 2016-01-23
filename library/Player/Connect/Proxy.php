<?php

/**
 * Player_Connect_Proxy
 * 
 * Sets a proxy to connects the player to server
 */
class Player_Connect_Proxy
{

    /**
     * Proxy access server.
     *
     * @var mixed null
     */
    public static $server = null;

    /**
     * Proxy access port.
     *
     * @var mixed 0
     */
    public static $port = 0;

    /**
     * Proxy access user.
     *
     * @var mixed null
     */
    public static $user = null;

    /**
     * Proxy access password.
     *
     * @var mixed null
     */
    public static $pass = null;
    
    /**
     * Gets a proxy setting
     *
     * @return array
     */
    public static function getProxy() {

        return array(
            
            'server'    => self::$server,
            'port'      => self::$port,
            'user'      => self::$user,
            'pass'      => self::$pass,
            
        );
        
    }
    
    /**
     * Sets a proxy setting
     *
     * @param  mixed $params null
     * @return array
     */
    public static function setProxy($params = null) {

        if(is_array($params)) {

            if(isset($params['proxy'])) {
                
                self::$server  = (isset($params['proxy']['server']) ? $params['proxy']['server'] : null);
                self::$port    = (isset($params['proxy']['port']) ? $params['proxy']['port'] : 0);
                self::$user    = (isset($params['proxy']['user']) ? $params['proxy']['user'] : null);
                self::$pass    = (isset($params['proxy']['pass']) ? $params['proxy']['pass'] : null);
                
            } else {
            
                self::$server  = (isset($params['server']) ? $params['server'] : null);
                self::$port    = (isset($params['port']) ? $params['port'] : 0);
                self::$user    = (isset($params['user']) ? $params['user'] : null);
                self::$pass    = (isset($params['pass']) ? $params['pass'] : null);
                
            }
            
        }
        
        return self::getProxy();
        
    }

}