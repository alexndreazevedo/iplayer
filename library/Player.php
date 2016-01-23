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
        
        if($this->getInstall()){
            
            if($this->getDownload()){
                
                $this->setPlay();

            } else {

                $this->setDownload($connection);
                
                $path   = Player_Flags::getFlag('path');
                $files  = Player_Flags::getFlag('files', 'status');
                
                $status = Player_File::getFile($path['config'] . $files['file']);
                
                if($status == null) {
                    
                    $status = 'Downloading';
                    
                }

                $this->layout->timeout  = 5;
                $this->layout->status   = $status;
                
            }
            
        } else {
        
            $validate = new Player_Validate();
            
            $activation = $validate->getActivation();
            
            $this->setInstall($connection, $validate);

            $this->layout->status   = 'Sign in for activate';
            $this->layout->code     = $activation;

        }
        
        $this->setScreen();
        
    }

}