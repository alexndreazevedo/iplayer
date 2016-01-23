<?php

include_once 'Player/Application.php';

/**
 * Player extends Player_Application
 * 
 * Run player
 */

class Player extends Player_Application
{
    
    public function load()
    {
        
        //@TODO
        
    }
    
    public function run()
    {
        
        ob_start();

        $connection = new Player_Connect();
        
        if($this->getInstall()){
            
            //@TODO
            
            print "<br />" . 'active';
            
        } else {
        
            $validate = new Player_Validate();
            
            if($validate->getActivation()){

                if($connection->checkConnection()){
                    
                    if($this->setInstall($connection, $validate)) {
                        
                        print "<br />" . 'active';
                        
                    }

                }

            }
            
        }
        
        ob_end_flush();
        
    }

}