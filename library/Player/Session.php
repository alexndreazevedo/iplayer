<?php

class Player_Session
{
    
    protected $session;
    protected $sid;
    protected $name;


    public function __construct($params = null) {
        
        session_start();
		
        $this->name	= session_name($params);
        $this->sid	= session_id();
    
    }
    
    public function getSID(){
        
        return $this->sid;
            
    }
    
    public function getName(){
        
        return $this->name;
            
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
    
    public function clearSession(){
            
        session_destroy();
            
    }
    
}