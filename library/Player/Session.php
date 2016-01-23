<?php

class Player_Session
{
    
    public function __construct($params = null) {
        
        session_start($params);
    
    }
    
    public function getSession($params = null){
        
        if(!isset($_SESSION[$params])){
            
            $_SESSION[$params] = 0;
            
        }
            
        return $_SESSION[$params];
            
    }
    
    public function setSession($params = null, $options = null){
            
        $_SESSION[$params] = $options;
            
        return $_SESSION[$params];
            
    }
    
}