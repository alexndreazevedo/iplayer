<?php

class Player_Layout
{
    
    protected $_file;
    
    protected $_path;
    
    public $base;
    
    public $root;

    public function __construct($path = null, $base = null, $root = null) {
        
        $this->setLayout();
        $this->_path = $path;
        
        if($base == null){
            
            $this->base = 'http://' . $_SERVER['SERVER_NAME'] . '/layout/';
            
        }
        
        if($root == null){
            
            $this->root = 'http://' . $_SERVER['SERVER_NAME'] . '/files/medias/';
            
        }
        
        
    }

    public function getLayout($params = null){

        if($params == null) {

            return $this->_file;

        } else {

            if(isset($this->_file[$params])){
                
                return $this->_path . $this->_file[$params];
            }

        }
            
        return false;
            
    }
    
    public function setLayout(){
        
        if($this->getLayout('html') == null){
            
            $this->_file = array(
                
                'login'     => 'login.phtml',
                'download'  => 'download.phtml',
                'play'      => 'play.phtml',
                
            );
            
        }
        
    }

    public function customLayout($params = array()) {
        
        array_push($this->_file, $params);
        
    }

    public function getMedia($params = null, $print = true) {
        
        $return = $this->root . $params;
        
        if($print){
            
            print $return;
            
        } else {
            
            return $return;
            
        }
        
    }

    public function getBase($params = null, $print = true) {
        
        $return = $this->base . $params;
        
        if($print){
            
            print $return;
            
        } else {
            
            return $return;
            
        }
        
    }

    public function setTitle($params = null, $print = true) {
        
        $return = '<title>' . $params . '</title>' . PHP_EOL;
        
        if($print){
            
            print $return;
            
        } else {
            
            return $return;
            
        }
        
    }

    public function setScript($params = null, $options = null, $print = true) {
        
        $return = '<script src="' . $this->getBase($params, false) . '" language="' . $options . '"></script>' . PHP_EOL;
        
        if($print){
            
            print $return;
            
        } else {
            
            return $return;
            
        }
        
    }

    public function setLink($params = null, $media = 'screen', $rel = 'stylesheet', $type = 'text/css', $print = true) {
        
        $return = '<link href="' . $this->getBase($params, false) . '" media="' . $media . '" rel="' . $rel . '" type="' . $type . '" />' . PHP_EOL;
        
        if($print){
            
            print $return;
            
        } else {
            
            return $return;
            
        }
        
    }

    public function setMeta($params = null, $options = null, $print = true) {
        
        $return = '<meta http-equiv="' . $params . '" content="' . $options . '" />' . PHP_EOL;
        
        if($print){
            
            print $return;
            
        } else {
            
            return $return;
            
        }
        
    }

    public function setPrint($params = null, $break = true) {
        
        if($break){
        
            print $params . PHP_EOL;
            
        } else {
        
            print $params;
            
        }
        
    }

    public function showLayout($layout = null, $options = null){
        
        if($options != null) {
            
            $this->customLayout($options);
            
        }
        
        $filename = $this->getLayout($layout);
        
        $file = Player_File::getFile($filename);
        
        ob_start();
        
        include($filename);
        
        $return = ob_get_contents();
        
        ob_end_clean();

        print $return;
        
    }
    
}