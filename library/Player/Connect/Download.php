<?php

require_once 'Player/Connect.php';
require_once 'Player/Connect/FTP.php';
require_once 'Player/Convert.php';
require_once 'Player/File.php';
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
    
    protected $_libraries = array();
    
    protected $_pictures = array();
    
    protected $_debug = false;

    protected $_refresh = false;

    /**
     * Sets the timeout of running.
     *
     * @var integer
     */
    protected $_timeout = 0;

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
        
        $this->setTimeOut();
        $this->setConfig();
        $this->setPlaylist();
        $this->setLoop();
        $this->setMedias();
        $this->setLibrary();
        $this->setPictures();
        $this->setUpdate();
        
    }

    /**
     * Sets timeout to script.
     *
     * @param  integer $time 0
     * @return void
     */
    public function setTimeOut($time = 0)
    {
        
        $this->_timeout = set_time_limit($time);
        
    }
    
    public function getRefresh()
    {
        
        return $this->_refresh;
        
    }
    
    public function setRefresh($params = false)
    {
        
        if($this->_refresh or $params){
            
            $this->_refresh = true;
            
        } else {
            
            $this->_refresh = false;
            
        }
        
        return $this->_refresh;
        
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
        
        if(php_sapi_name() == 'cli'){

            return "\t";
            
        } else {

            return '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            
        }
        
    }
    
    public function getLog() {
        
        return $this->_log;
        
    }
    
    public function setLog($log) {
        
        return $this->_log .= $log . $this->getBreakline();
        
    }
    
    public function saveLog($filename) {
        
        if(Player_File::setFile($filename, $this->getLog(), true, true)) {
            
            return true;
            
        }
        
        return false;
        
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
            
                echo $this->getLog() . $this->getBreakline();
                
            }

        }
        
        if($this->getDebug()){
            
            echo $log . $this->getBreakline();
            
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
        $path   = Player_Flags::getFlag('path');
        
        $file   = Player_File::getFile($this->getConfigFile());
        $xml    = Player_Convert::getXML($file, $label['config']);
        
        $this->setDebug('Downloading playlist...');
        
        $params = array(

            $player['sector']   => $xml[$player['sector']]

        );
        
        $playlist = $this->loadConnection($environment, $url['playlist'], $params);
        
        if ($playlist){
            
            Player_File::copyFile($path['config'] . $files['file'], $path['config'] . $files['temp'], true);
            
            $this->setDebug('Creating playlist backup...');
            
            if(Player_File::setFile($path['config'] . $files['temp'], $playlist, true)){

                $this->setDebug('Verifying playlist\' updates...');

                $xml = Player_Encrypt::setHashFileContents($path['config'] . $files['file']);
                $tmp = Player_Encrypt::setHashFileContents($path['config'] . $files['temp']);

                if($xml !== $tmp){

                    $this->setDebug('Updates:', 1);

                    $this->setDebug('"' . $xml . '"', 2);
                    $this->setDebug('"' . $tmp . '"', 2);

                    $this->setRefresh(true);

                } else {

                    $this->setDebug('No playlist updates.');
                    
                    $this->setRefresh(false);
                    
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
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $files  = Player_Flags::getFlag('files', 'loop');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
            
        $this->setDebug('Creating loop backup...');

        Player_File::copyFile($path['config'] . $files['file'], $path['config'] . $files['temp'], true);
            
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
        
        if(Player_File::setFile($path['config'] . $files['temp'], $xml, true)){
        
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
                    
                    if (!in_array($v[$media['filename']], $this->getFiles())) {
                        
                        if (file_exists($local . $v[$media['filename']])) {

                            $hash = Player_Encrypt::setHashFile($local . $v[$media['filename']]);

                            if ($hash !== $v[$media['hash']]) {

                                $this->setDebug('Corrompido: "' . $v[$media['filename']] . '"', 1);
                                
                                $this->setDebug('"' . $hash . '"', 2);
                                $this->setDebug('"' . $v[$media['hash']] . '"', 2);

                                Player_File::setPermissions($local . $v[$media['filename']]);

                                array_push($download,
                                    
                                    array(

                                        $media['file']  => $v[$media['filename']],
                                        $media['hash']  => $v[$media['hash']]
                                        
                                    )
                                    
                                );

                            }

                        } else {
                            
                            if (file_exists($temp . $v[$media['filename']])) {

                                $hash = Player_Encrypt::setHashFile($temp . $v[$media['filename']]);

                                if ($hash !== $v[$media['hash']]) {

                                    $this->setDebug('Download corrompido: "' . $v[$media['filename']] . '"', 1);

                                    $this->setDebug('"' . $hash . '"', 2);
                                    $this->setDebug('"' . $v[$media['hash']] . '"', 2);

                                    Player_File::setPermissions($temp . $v[$media['filename']]);

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

                        }

                        $this->setFiles($v[$media['filename']]);

                    }

                }

            }

        }
        
        $refresh = (count($this->getFiles()) > 0) ? true : false;
        
        $this->setRefresh($refresh);
        
        $this->setDebug('Downloading files...');
        
        if(Player_Connect_FTP::getMediaFTP(
            
            $access[$ftp['host']], 
            $access[$ftp['user']], 
            $access[$ftp['pass']], 
            $access[$ftp['file']], 
            $temp, 
            $download,
            true
            
        )){

            return true;
        
        }
        
        return false;

    }
    
    public function setLibrary() {
        
        $environment = $this->getEnvironment();
        
        $playlist = $this->getPlaylist();
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        $url    = Player_Flags::getFlag('url');
        $ftp    = Player_Flags::getFlag('ftp');
       
        $this->setDebug('Accessing remote server...');
        
        $connect    = $this->loadConnection($environment, $url['access']);
        $access     = Player_Convert::getXML($connect, $label['ftp']);
        
        $this->setDebug('Retrieving library files...');
        
        $local  = $path['library'];
        $temp   = $path['library'] . $path['temp'];
        
        $download = array();

        foreach ($playlist as $key => $value) {
            
            if(isset($value[$media['index']])){

                foreach ($value[$media['index']] as $k => $v) {

                    if($v[$media['type']] == $media['library']) {

                        array_push($download, 
                            
                            array(
                            
                                $media['file'] => $v[$media['xml']]
                            
                            )
                            
                        );

                        $this->setLibraries($v[$media['xml']]);

                    }

                }

            }

        }
        
        if(Player_Connect_FTP::getMediaFTP(
            
            $access[$ftp['host']], 
            $access[$ftp['user']], 
            $access[$ftp['pass']], 
            $access[$ftp['feed']], 
            $temp, 
            $download
            
        )){
            
            return true;
            
        }

        return false;
        
    }
    
    public function setPictures() {

        $library = $this->getLibraries();
        
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        $field  = Player_Flags::getFlag('field');
        
        foreach ($library as $key => $value){
            
            $lib = $path['library'] . $path['temp'];
            
            $file = Player_File::getFile($lib . $value);
            
            $feed = Player_Convert::getXML($file, $label['library']);
            
            foreach ($feed as $index => $image){
                
                if(is_array($image)) {
                
                    foreach ($image as $k => $v){

                        if(isset($v[$field['image']])){
            
                            $local  = $path['picture'];
                            $temp   = $path['picture'] . $path['temp'];
                            
                            $filename = substr($v[$field['image']], strrpos($v[$field['image']], '/') + 1);
                            
                            if(!file_exists($local . $filename)) {
                            
                                Player_File::setDir($temp);

                                $this->loadFile($temp . $filename, $v[$field['image']], true);
                                
                            }

                            $this->setPics($filename);

                        }

                    }
                    
                }
                
            }
            
        }
        
    }
    
    public function getFiles() {
        
        return $this->_files;
        
    }
    
    public function setFiles($params = null) {
        
        $this->_files[] = $params;
        
    }
    
    public function getLibraries() {
        
        return $this->_libraries;
        
    }
    
    public function setLibraries($params = null) {
        
        $this->_libraries[] = $params;
        
    }
    
    public function getPics() {
        
        return $this->_pictures;
        
    }
    
    public function setPics($params = null) {
        
        $this->_pictures[] = $params;
        
    }
    
    public function setUpdate(){
        
        $file   = Player_Flags::getFlag('files', 'log');
        
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        $field  = Player_Flags::getFlag('field');
        $files  = Player_Flags::getFlag('files');
        
        $step = array(
            
            'media' => array(
                    
                'path'      => $path['media'],
                'temp'      => $path['media'] . $path['temp'],
                'files'     => $this->getFiles(),
                
            ),
            
            'library' => array(
                
                'path'      => $path['library'],
                'temp'      => $path['library'] . $path['temp'],
                'files'     => $this->getLibraries(),
                
            ),
            
            'picture' => array(
                
                'path'      => $path['picture'],
                'temp'      => $path['picture'] . $path['temp'],
                'files'     => $this->getpics(),
                
            ),
            
        );

        foreach ($step as $key => $value){

            $local = Player_File::listFiles($value['path']);
            
            $return = array_diff($local, $value['files']);
            
            Player_File::removeFiles($return, $value['path']);
            
            Player_File::moveFiles($value['files'], $value['path'], $value['temp']);

            $temp = Player_File::listFiles($value['temp']);
            
            Player_File::removeFiles($temp, $value['temp']);
            
        }
        
        if($this->getRefresh()){
            
            $refresh = array(
                
                'playlist',
                'loop',
                
            );
            
            foreach ($refresh as $key => $value) {
                
                Player_File::moveFile($path['config'] . $value[$key]['file'], $path['config'] . $value[$key]['temp'], true);
                
            }
            
        }
        
        $this->saveLog($path['config'] . $file['file']);
        
        return false;
        
    }
    
}