<?php

class Player_Layout
{
    
    static protected $_defs;
    
    static protected $_html;

    static function getLayout($type = null, $params = null){
        
        if($type == null || $type == 'html'){
            
            if($params == null) {

                return self::$_html;

            } else {

                return (isset(self::$_html[$params]) ? self::$_html[$params] : false);

            }
            
        } else {

            if($params == null) {

                return self::$_defs;

            } else {

                return (isset(self::$_defs[$params]) ? self::$_defs[$params] : false);

            }
            
        }
        
    }
    
    static function setLayout(){
        
        if(self::getLayout('html') == null){
            
            self::$_html = array(
                
                'login',
                'play'
                
            );
            
        }
        
        if(self::getLayout('defs') == null){
            
            self::$_defs = array(
                
                'login' => array(
                    
                    'title' => 'Login',
                    
                ),
                'play'  => array(
                    
                    'title' => 'Login'
                    
                ),
                
            );
            
        }
        
    }

    static function setHTML($html) {
        
        array_push(self::$_html, $html);
        
    }

    static function setDefs($defs) {
        
        array_push(self::$_defs, $defs);
        
    }
    
    static function showLayout($layout = null, $html = null, $defs = null){
        
        self::setLayout();
        
        if($html != null) {
            
            self::setHTML($html);
            
        }
        
        if($defs != null) {
            
            self::setDefs($defs);
            
            
        }
        
        $filename       = self::getLayout('html', $layout);
        $definitions    = self::getLayout('defs', $layout);
        
        //@TODO
        
        
    }
    
}