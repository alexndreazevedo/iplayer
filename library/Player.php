<?php

include_once 'Player/Application.php';

/**
 * Player extends Player_Application
 * 
 * Run player
 */

class Player extends Player_Application
{
    
    public function load(){}  //@TODO
    
    public function run()
    {
    
        $connection = new Player_Connect();
        $validate = new Player_Validate();
            
        $activation = $validate->getActivation();
        
        if($this->getInstall()){
            
            if($this->getDownload()){
                
                $this->setPlay();

            } else {

                $this->setDownload($connection);
				
                $status = Player_Debug::getStatus();
                
                $this->layout->refresh  = 5;
                $this->layout->msg		= $status['msg'];
                $this->layout->status   = $status['status'];
                $this->layout->code     = $activation;
                
            }
            
        } else {

            $this->layout->status   = 'Sign in for activate';
            $this->layout->code     = $activation;
            
            $this->setInstall($connection, $validate);

        }
        
        $this->setScreen();
        
    }

}