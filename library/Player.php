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
        
        ob_start();
                
        print 'begin' . "<br />";

        $connection = new Player_Connect();
        
        if($this->getInstall()){
                
            print 'active' . "<br />";
            
            if($this->getDownload()){
                
                print 'play' . "<br />";
                
                //@TODO


            } else {
                
                print 'download' . "<br />";

                if($connection->checkConnection($this->getEnvironment())){
                
                    print 'connected' . "<br />";
                
                    if($this->setDownload()) {

                        //@TODO

                        print 'download' . "<br />";
                        
                    }
                    
                }
                
            }
            
        } else {
                
            print 'inativo' . "<br />";
        
            $validate = new Player_Validate();
            
            if($validate->getActivation()){
                
                print 'code' . "<br />";

                if($connection->checkConnection($this->getEnvironment())){
                
                    print 'connected' . "<br />";
                    
                    if($this->setInstall($connection, $validate)) {
                        
                        print 'active' . "<br />";
                        
                        //@TODO
                        
                    }

                }

            }
            
        }
                
        print 'end' . "<br />";
        
        ob_end_flush();
        
    }

}