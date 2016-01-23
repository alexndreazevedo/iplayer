<?php

require_once 'Player/Connect.php';
require_once 'Player/Connect/FTP.php';
require_once 'Player/Convert.php';
require_once 'Player/Encrypt.php';
require_once 'Player/Flags.php';
require_once 'Player/Utils.php';

class Player_Connect_Download extends Player_Connect
{

    protected $_environment;
    
    protected $_config;
    
    protected $_options;
    
    protected $_log;
    
    protected $_playlist;
    
    protected $_loop;
    
    protected $_files = array();
    
    protected $_debug = false;

    protected $_refresh = false;

    public function __construct($environment = null, $options = null) {
        
        $this->_environment = (string) $environment;

        if($environment == 'development') {
            
            $this->_debug = true;
            
        }
        
        if($options != null) {
            
            $this->setConfigFile($options);
            
        }
        
    }
    
    public function run() {
        
        $this->setConfig();
        $this->setPlaylist();
        $this->setLoop();
        $this->setMedias();
        
        $this->setLibrary();
        $this->setPictures();
        
        $this->setFiles(
            
            array(
                
                'fotos',
                'rssurl',
                'arquivos'
                
            )

        );

        $this->setUpdate();
        
    }
    
    public function getEnvironment() {
        
        return $this->_environment;
        
    }
    
    public function getBreakline() {
        
        if(php_sapi_name() == 'cli'){

            return "\n";
            
        } else {

            return '<br />';
            
        }
        
    }
    
    public function getTab() {
        
        if(php_sapi_name() == 'CLI'){

            return "\t";
            
        } else {

            return '&nbsp;&nbsp;&nbsp;&nbsp;';
            
        }
        
    }
    
    public function getLog() {
        
        return $this->_log;
        
    }
    
    public function setLog($log) {
        
        return $this->_log .= $log . $this->getBreakline();
        
    }
    
    public function saveLog() {
        
        //@TODO
        
    }
    
    public function getDebug() {
        
        return $this->_debug;
        
    }
    
    public function setDebug($log, $tab = null) {

        if(intval($tab) > 0) {
            
            $tab = Player_Utils::getSeparator($this->getTab(), $tab);
            
        }
        
        $log = $tab . $log;

        if($this->getLog() == null) {
            
            $this->setLog(
                
                Player_Utils::getSeparator('=', 70)
                
            );
            
            $this->setLog(
                
                'DOWNLOAD LOG - ' . 
                ((php_sapi_name() == 'cli') ? 'PROMPT' : 'BROWSER')
                
            );
            
            $this->setLog(
                
                date('d') . '/' . 
                Player_Utils::getMonth(date('m'), array('UPP','CUT')) . '/' . 
                date('Y') . ' - ' . 
                date('h:i:s')
                
            );
            
            $this->setLog(
                
                Player_Utils::getSeparator('=', 70)
                
            );
            
            if($this->getDebug()){
            
                print $this->getLog() . $this->getBreakline();
                
            }

        }
        
        if($this->getDebug()){
            
            print $log . $this->getBreakline();
            
        }

        $this->setLog($log);
        
    }

    public function setConfigFile($params = null) {
        
        $this->_config = $params;
        
    }

    public function getConfigFile() {
        
        return $this->_config;
        
    }

    public function setConfig() {
        
        $environment = $this->getEnvironment();
        
        $url    = Player_Flags::getFlag('url');
        $player = Player_Flags::getFlag('player');
        $user   = Player_Flags::getFlag('user');
        $label  = Player_Flags::getFlag('label');
        
        $this->setDebug('Loading settings...');
        
        $file   = Player_File::getFile($this->getConfigFile());
        $xml    = Player_Convert::getXML($file, $label['config']);
        
        $params = array(

            $player['id']   => $xml[$player['id']],
            $user['login']  => $xml[$user['login']],

        );

        $this->setDebug('Verifying updates...');
        
        $config = $this->loadConnection($environment, $url['config'], $params);
        
        if($config){

            $this->setDebug('Saving updates...');
            
            if(Player_File::setFile($this->getConfigFile(), $config, true)){
            
                $config = Player_Convert::setXML($label['config'], $config);
                return $this->_options = $config;
                
            }
            
        }
        
        return false;
        
    }
    
    public function getPlaylist() {
        
        return $this->_playlist;
        
    }
    
    public function setPlaylist() {
        
        $environment = $this->getEnvironment();
        
        $url    = Player_Flags::getFlag('url');
        $link   = Player_Flags::getFlag('link');
        $player = Player_Flags::getFlag('player');
        $files  = Player_Flags::getFlag('files', 'playlist');
        $label  = Player_Flags::getFlag('label');
        
        $file   = Player_File::getFile($this->getConfigFile());
        $xml    = Player_Convert::getXML($file, $label['config']);
        
        $this->setDebug('Downloading playlist...');
        
        $params = array(

            $player['sector']   => $xml[$player['sector']]

        );
        
        $playlist = $this->loadConnection($environment, $url['playlist'], $params);
        
        if ($playlist){
            
            Player_File::copyFile($files['file'], $files['temp'], true);
            
            $this->setDebug('Creating playlist backup...');
            
            if(Player_File::setFile($files['temp'], $playlist, true)){

                $this->setDebug('Verifying playlist\'s updates...');

                $xml = Player_Encrypt::setHashFileContents($files['file']);
                $tmp = Player_Encrypt::setHashFileContents($files['temp']);

                if($xml !== $tmp){

                    $this->setDebug('Updates:', 2);

                    $this->setDebug('"' . $xml . '"', 4);
                    $this->setDebug('"' . $tmp . '"', 4);

                    $this->_refresh = true;

                }

                $this->setDebug('Caching playlist...');
                
                $playlist = Player_Convert::getXML($playlist, $label['playlist']);

                $this->_playlist = $playlist;
                
            }
            
        }
        
        return $this->_playlist;
        
    }

    public function getLoop() {
        
        return $this->_loop;
        
    }

    public function setLoop()
    {
        
        $playlist = $this->getPlaylist();
        
        $media = Player_Flags::getFlag('playlist', 'media');
        $files = Player_Flags::getFlag('files', 'loop');
        $label = Player_Flags::getFlag('label');
            
        $this->setDebug('Creating loop backup...');

        Player_File::copyFile($files['file'], $files['temp'], true);
            
        $this->setDebug('Processing loop...');

        $loop = array();
        
        foreach ($playlist as $key => $value) {
            
            if(isset($value[$media['index']])){

                foreach ($value[$media['index']] as $k => $v) {

                    if($v[$media['type']] == $media['file']) {

                        $loop[$key][$media['index']][$k] = array(

                            $media['status'] => 0,

                        );

                    }

                }

            }

        }

        $this->setDebug('Saving loop...');
        
        $xml = Player_Convert::setXML($label['loop'], $loop);
        
        if(Player_File::setFile($files['temp'], $xml, true)){
        
            return $this->_loop = $loop;
            
        }
        
        return false;
        
    }
    
    public function setMedias() {
                
        $environment = $this->getEnvironment();

        $url    = Player_Flags::getFlag('url');
        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        $ftp    = Player_Flags::getFlag('ftp');
        
        $this->setDebug('Accessing remote server...');
        
        $connect    = $this->loadConnection($environment, $url['access']);
        $access     = Player_Convert::getXML($connect, $label['ftp']);
        
        $local  = $path['media'];
        $temp   = $path['media'] . $path['temp'];
        
        $playlist = $this->getPlaylist();
        
        $this->setDebug('Listing files to download:');

        Player_File::setPermissions($local, 0777);
        
        $download = array();
        
        foreach ($playlist as $key => $value) {
            
            if(isset($value[$media['index']])){

                foreach ($value[$media['index']] as $k => $v) {
                    
                    //@TODO EXCEPTION TO REMOVE

                    if($v[$media['type']] == $media['library']) {

                        $v[$media['filename']]  = 'tpl_noticias-terra.swf';
                        $v[$media['hash']]      = '12338691ee42757942f38aeefdaa7cd5ac95926b';

                    }

                    //@TODO WILL STARTS FOREACH HERE

                    if (!in_array($v[$media['filename']], $this->getFiles())) {

                        if (file_exists($local . $v[$media['filename']])) {

                            $hash = Player_Encrypt::setHashFile($local . $v[$media['filename']]);

                            if ($hash != $v[$media['hash']]) {

                                $this->setDebug('Currupter: "' . $v[$media['filename']] . '"', 2);
                                
                                $this->setDebug('"' . $hash . '"', 4);
                                $this->setDebug('"' . $v[$media['hash']] . '"', 4);

                                Player_File::setPermissions($local . $v[$media['filename']]);

                                array_push($download,
                                    
                                    array(

                                        $media['file']  => $v[$media['filename']],
                                        $media['hash']  => $v[$media['hash']]
                                        
                                    )
                                    
                                );

                            }

                        } else {

                            $this->setDebug('New: "' . $v[$media['filename']] . '"', 2);

                            array_push($download,

                                array(

                                    $media['file']  => $v[$media['filename']],
                                    $media['hash']  => $v[$media['hash']]

                                )

                            );

                        }

                        $this->setFiles($v[$media['filename']]);

                    }

                }

            }

        }
        
        $stored = Player_File::listFiles($local);
        
        $this->setDebug('Downloading files...');
        
        Player_Connect_FTP::getFileFTP(
            
            $access[$ftp['host']], 
            $access[$ftp['user']], 
            $access[$ftp['pass']], 
            $access[$ftp['path']], 
            $local, 
            $download
            
        );
        
        $return['alteracoes'] = (count($arquivos_locais) == 0) ? false : true;
        
        return $return;
        
    }
    
    public function setLibrary() {
        
        //@TODO
        
    }
    
    public function setPictures() {

        //@TODO
        
    }
    
    public function getFiles() {
        
        return $this->_files;
        
    }
    
    public function setFiles($params = null) {
        
        $this->_files[] = $params;
        
    }
        
    public function setUpdate(){

        if($this->_refresh){
         
            //@TODO
            
        }
        
        return false;
        
    }
    
}