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
        
        print 'executou';
        
        $validate   = new Player_Validate();
        $settings   = array();
        $url        = array();
        
        $settings['env']    = $this->getEnvironment();
        $settings['file']   = $this->getConfigFile();
        
        $url['login']       = $this->getFlag('url', 'login');
        $url['config']      = $this->getFlag('url', 'config');
        $url['access']      = $this->getFlag('url', 'access');
        
        var_dump($settings);
        var_dump($url);
        
        if($this->getInstall()){
            
            //@TODO
            
            print "<br />" . 'active';
            
        } else {
            
            print "<br />" . 'inactive';
            
            $activation = $validate->getActivation();
            
            if($activation){
                
                print "<br />" . $activation;

                $connection = new Player_Connect();

                if($connection->checkConnection()){
            
                    //@TODO
                    
                    $params = array(
                        
                        'login'         => '',
                        'senha'         => '',
                        'senhaAc'       => $activation,
                        'configurado'   => 1
                        
                    );
                    
                    $login = $connection->loadConnection($settings['env'], $url['login'], $params);
                        
                    if($login) {
                       
                        $ini = Player_Convert::setIni($login, $settings['file'], true);
                        
                        var_dump($ini);
                        
                        $params = array(
                            
                            'idplayer'  => $ini['idplayer'],
                            'login'     => $ini['login']
                            
                        );
                    
                        $config = $connection->loadConnection($settings['env'], $url['config'], $params);
                        
                        $ini = Player_Convert::setIni($config, $settings['file'], true);
                        
                        var_dump($ini);
                        
                        $params = array(
                            
                            'idplayer'  =>$ini['idplayer']
                            
                        );
                        
                        $access = $connection->loadConnection($settings['env'], $url['access'], $params);
                       
                    } else {
                        
                        //@TODO
                        
                    }

                }

            }
            
        }
        
        ob_end_flush();
        
    }

}