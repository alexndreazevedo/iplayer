<?php

class Player_Connect
{
    
    protected $_protocol = 'http://';
    
    protected $_subdomain = 'api';
    
    protected $_domain = 'appserver.com';
    
    protected $_port = 80;
    
    protected $_path = '/';
    

    public function check() {
        
        if (!@fsockopen($this->_domain, $this->_port, $errno, $errstr, 5)) {
            
            $return = false;
            
        } else {
            
            $return = true;
            
        }
        
        return $return;
        
    }
    
    public function load($path, array $params = null) {
        
        if($this->check()) {

            $return = array();
            $result = '';

            $url = $this->_protocol . $this->_subdomain . $this->_domain . $this->_path . $path;

            $curl = curl_init();

            curl_setopt_array($curl,

                array(

                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $params,
                    CURLOPT_RETURNTRANSFER => true,

                )

            );

            $result = curl_exec($curl);

            curl_close($curl);

            $json = new Convert;
            $result = $json
                ->getJson($result)
                ->toArray($result);

            if (is_array($return)) {

                foreach ($return as $key => $value) {

                    $i = ($path == 'login') ? 'dev' : $key;

                    $return[$i] = $value;

                }

                return $return;

            }
            
        }

    }
    
}