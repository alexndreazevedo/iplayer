<?php

require_once 'Player/Connect.php';
require_once 'Player/Connect/Proxy.php';
require_once 'Player/Connect/FTP.php';
require_once 'Player/Convert.php';
require_once 'Player/File.php';
require_once 'Player/Encrypt.php';
require_once 'Player/Flags.php';
require_once 'Player/Debug.php';
require_once 'Player/Utils.php';

class Player_Connect_Download extends Player_Connect
{

    protected $_environment;
    
    protected $_config;
    
    protected $_options;
    
    protected $_playlist;
    
    protected $_loop;
    
    protected $_files = array();
    
    protected $_libraries = array();
    
    protected $_pictures = array();

    protected $_refresh = false;

    /**
     * Sets the timeout of running.
     *
     * @var integer
     */
    protected $_timeout = 0;

    public function __construct($environment = null, $options = null) {
        
        $this->_environment = (string) $environment;
        
        if($options != null) {
            
            $this->setConfigFile($options);
            
        }
        
    }
    
    public function run() {
        
        $path   = Player_Flags::getFlag('path');
        $files  = Player_Flags::getFlag('files');
        
        $this->setTimeOut();
        
        if(Player_Utils::getInterface()) {
            
            if($this->getConfig()){

                Player_Debug::setStatus('Downloading...');
                $this->clearUpdate($this->setConfig(), 'download settings');
                $this->clearUpdate($this->setPlaylist(), 'update playlists');
                $this->clearUpdate($this->setLoop(), 'update loops');
                $this->clearUpdate($this->setMedias(), 'download media');
                $this->clearUpdate($this->setLibrary(), 'download libraries');
                $this->clearUpdate($this->setPictures(), 'download images');
                $this->clearUpdate($this->setUpdate(), 'finish downloads');
                $this->clearUpdate($this->setDownload(), 'update log');
                Player_Debug::setStatus('Download finished.');
                
            }
            
        }
        
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

    public function getConfigFile() {
        
        return $this->_config;
        
    }

    public function setConfigFile($params = null) {
        
        $this->_config = $params;
        
    }
    
    public function getConfig() {
        
        $status = Player_Flags::getFlag('status');
        $player = Player_Flags::getFlag('player');
        $label  = Player_Flags::getFlag('label');
        $files  = Player_Flags::getFlag('files');
        $path   = Player_Flags::getFlag('path');
        $url	= Player_Flags::getFlag('url');
        
        $file   = Player_File::getFile($this->getConfigFile());
        $config = Player_Convert::getXML($file, $label['config']);
		$loop	= Player_Debug::getStatus();
        
        if(isset($config[$status['active']])){
            
            if(intval($config[$status['active']]) == 1){
				
				$params = array(

                    $player['id']   => $config[$player['id']]

                );

                $this->loadConnection($this->getEnvironment(), $url['last'], $params);
				
				if(!isset($config[$status['download']])){
            
		            $config[$status['download']] = 0;

				}
				
				if($config[$status['download']] == 0 and $loop['loop'] > 0){
					
		            Player_Debug::setDebug('Download stopped. Already downloading via browser.');
					Player_Debug::saveLog($path['config'] . $files['log']['file']);
					
					return false;
					
				}

                return $this->_options = $config;
                
            } else {
				
                Player_Debug::setStatus('Download not finished. Please active the player.');
				Player_Debug::setDebug('Download stopped. The player must be activated.');
                Player_Debug::saveLog($path['config'] . $files['log']['file']);
				
			}
            
        }
        
        return false;
        
    }

    public function setConfig() {
        
        $environment = $this->getEnvironment();
        
        $url    = Player_Flags::getFlag('url');
        $player = Player_Flags::getFlag('player');
        $user   = Player_Flags::getFlag('user');
        $status = Player_Flags::getFlag('status');
        $label  = Player_Flags::getFlag('label');
        $files  = Player_Flags::getFlag('files');
        $path   = Player_Flags::getFlag('path');
        
        $xml    = $this->getConfig();
        
        if($xml){
        
            Player_Debug::setStatus('Downloading settings...', false, true);

            $params = array(

                $player['id']   => $xml[$player['id']],
                $user['login']  => $xml[$user['login']],

            );

            Player_Debug::setDebug('Verifying updates...');

            $config = $this->loadConnection($environment, $url['config'], $params);

            if($config){

                Player_File::copyFile($path['config'] . $files['playlist']['file'], $path['config'] . $files['playlist']['temp'], true);
                Player_File::copyFile($path['config'] . $files['loop']['file'], $path['config'] . $files['loop']['temp'], true);

                Player_Debug::setDebug('Saving updates...');
				
				$array = Player_Convert::getXML($config, $label['config']);
				
				$array[$status['download']] = $this->_options[$status['download']];
				
				$config = Player_Convert::setXML($label['config'], $array);

                if(Player_File::setFile($this->getConfigFile(), $config, true)){

                    return $this->_options = $array;

                }

            }
            
        }

        return false;
        
    }
    
    public function getPlaylist() {
        
        return $this->_playlist;
        
    }
    
    public function setPlaylist() {
        
        Player_Debug::setStatus('Downloading playlist...');
        
        $environment = $this->getEnvironment();
        
        $url    = Player_Flags::getFlag('url');
        $link   = Player_Flags::getFlag('link');
        $player = Player_Flags::getFlag('player');
        $files  = Player_Flags::getFlag('files', 'playlist');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        
        $file   = Player_File::getFile($this->getConfigFile());
        $xml    = Player_Convert::getXML($file, $label['config']);
        
        $params = array(

            $player['sector']   => $xml[$player['sector']]

        );
        
        $playlist = $this->loadConnection($environment, $url['playlist'], $params);
        
        if ($playlist){
            
            Player_Debug::setDebug('Creating playlist backup...');
            
            if(Player_File::setFile($path['config'] . $files['temp'], $playlist, true)){

                Player_Debug::setDebug('Verifying playlist updates...');

                $xml = Player_Encrypt::setHashFileContents($path['config'] . $files['file']);
                $tmp = Player_Encrypt::setHashFileContents($path['config'] . $files['temp']);

                if($xml !== $tmp){

                    Player_Debug::setDebug('Updates:', 1);

                    Player_Debug::setDebug('"' . $xml . '"', 2);
                    Player_Debug::setDebug('"' . $tmp . '"', 2);

                    $this->setRefresh(true);

                } else {

                    Player_Debug::setDebug('No playlist updates.', 1);
                    
                    $this->setRefresh(false);
                    
                }

                Player_Debug::setDebug('Caching playlist...', 1);
                
                $playlist = Player_Convert::getXML($playlist, $label['playlist']);

                $this->_playlist = $playlist;
                
            }
        
	        return $this->_playlist;
            
        }
		
		return false;
        
    }

    public function getLoop() {
        
        return $this->_loop;
        
    }

    public function setLoop(){
        
        Player_Debug::setStatus('Creating loop...');
        
        $playlist = $this->getPlaylist();
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $files  = Player_Flags::getFlag('files', 'loop');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
            
        Player_Debug::setDebug('Processing loop...');

        $loop = array();
        
        foreach ($playlist as $key => $value) {
            
            if(isset($value[$media['index']])){

                foreach ($value[$media['index']] as $k => $v) {

                    if($v[$media['type']] == $media['file']) {

                        $loop[$key][$k] = array(

                            $media['status'] => true,

                        );

                    }

                }

            }

        }

        Player_Debug::setDebug('Saving loop...', 1);
        
        $xml = Player_Convert::setXML($label['loop'], $loop);
        
        if(Player_File::setFile($path['config'] . $files['temp'], $xml, true)){
        
            return $this->_loop = $loop;
            
        }
        
        return false;
        
    }
    
    public function setMedias() {
        
        Player_Debug::setStatus('Downloading media...');
                
        $environment = $this->getEnvironment();

        $url    = Player_Flags::getFlag('url');
        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        $ftp    = Player_Flags::getFlag('ftp');
        
        $connect    = $this->loadConnection($environment, $url['access']);
        $access     = Player_Convert::getXML($connect, $label['ftp']);
        
        $local  = $path['media'];
        $temp   = $path['media'] . $path['temp'];
        
        $playlist = $this->getPlaylist();
        
        Player_Debug::setDebug('Listing files to download...');

        Player_File::setPermissions($local, 0777);
        
        $download = array();
        
        foreach ($playlist as $key => $value) {
            
            if(isset($value[$media['index']])){

                foreach ($value[$media['index']] as $k => $v) {
					
					if(!empty($v[$media['filename']])){

						if (!in_array($v[$media['filename']], $this->getFiles())) {

							if (file_exists($local . $v[$media['filename']])) {

								$hash = Player_Encrypt::setHashFile($local . $v[$media['filename']]);

								if ($hash !== $v[$media['hash']]) {

									Player_Debug::setDebug('Corrupted files:"' . Player_Debug::getTab() . '"' . $v[$media['filename']] . '"', 1);

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

										Player_Debug::setDebug('Corrupted files:' . Player_Debug::getTab() . '"' . $v[$media['filename']] . '"', 1);

										Player_File::setPermissions($temp . $v[$media['filename']]);

										array_push($download,

											array(

												$media['file']  => $v[$media['filename']],
												$media['hash']  => $v[$media['hash']]

											)

										);

									}

								} else {

									Player_Debug::setDebug('Download:' . Player_Debug::getTab() . '"' . $v[$media['filename']] . '"', 1);

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

        }
        
        $refresh = (count($download) > 0) ? true : false;
        
        $this->setRefresh($refresh);
        
		if(!$refresh){

	        Player_Debug::setDebug('No files for download.', 1);
			
			return true;
			
		}

        Player_Debug::setDebug('Downloading files...');

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
        
        Player_Debug::setStatus('Downloading libraries...');
        
        $environment = $this->getEnvironment();
        
        $playlist = $this->getPlaylist();
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        $url    = Player_Flags::getFlag('url');
        $ftp    = Player_Flags::getFlag('ftp');
       
        $connect    = $this->loadConnection($environment, $url['access']);
        $access     = Player_Convert::getXML($connect, $label['ftp']);
        
        Player_Debug::setDebug('Listing libraries to downloading...');
        
        $local  = $path['library'];
        $temp   = $path['library'] . $path['temp'];
        
        $download = array();

        foreach ($playlist as $key => $value) {
            
            if(isset($value[$media['index']])){
				
                foreach ($value[$media['index']] as $k => $v) {
				
					if(!empty($v[$media['xml']])){

						if($v[$media['type']] == $media['library']) {

							array_push($download, 

								array(

									$media['file'] => $v[$media['xml']]

								)

							);

							Player_Debug::setDebug('File:' . Player_Debug::getTab() . '"' . $v[$media['xml']] . '"', 1);
							$this->setLibraries($v[$media['xml']]);

						}

					}

                }

            }

        }
        
        $refresh = (count($download) > 0) ? true : false;
        
		if(!$refresh){

	        Player_Debug::setDebug('No libraries for download.', 1);
			
			return true;
			
		}

        Player_Debug::setDebug('Downloading libraries...');
        
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
        
        Player_Debug::setStatus('Downloading images...');

        $library = $this->getLibraries();
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label');
        $path   = Player_Flags::getFlag('path');
        $field  = Player_Flags::getFlag('field');
       
        Player_Debug::setDebug('Listing images to download:');
		
        foreach ($library as $key => $value){
            
            $lib = $path['library'] . $path['temp'];
            
            $file = Player_File::getFile($lib . $value);
            
            $feed = Player_Convert::getXML($file, $label['library']);
			
			if(isset($feed[$label['feed']])) {

				foreach ($feed[$label['feed']] as $k => $v){

					if(isset($v[$field['image']])){

						if(!empty($v[$field['image']])){

							$local  = $path['picture'];
							$temp   = $path['picture'] . $path['temp'];

							$filename = substr($v[$field['image']], strrpos($v[$field['image']], '/') + 1);

							if(!file_exists($local . $filename)) {

								Player_File::setDir($temp);
								
								$i = 1;

								do {
									
									$success = $this->loadFile($temp . $filename, $v[$field['image']], true);
									
									if($success){

										Player_Debug::setDebug('Download:' . Player_Debug::getTab() .  '"' . $filename . '"', 1);
										
									} else {

										Player_Debug::setDebug('Trying download ' . $i++ . Player_Debug::getTab() .  '"' . $filename . '"', 1);

										if($i > 5){

											Player_Debug::setDebug('Canceling download after ' . $i++ . ' failed tries.' .Player_Debug::getTab() .  '"' . $filename . '"', 1);

											return false;

										}
										
									}
									
									$success = Player_Utils::setSwitch($success, true, false);
									
								} while($success);

							}

							$this->setPics($filename);

						}

					}

				}

			}
            
        }
		
		return true;
        
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
    
    public function clearUpdate($params = null, $options = null){
		
        $path   = Player_Flags::getFlag('path');
        $files  = Player_Flags::getFlag('files');
		
		if(!$params){
       
	        Player_Debug::setDebug('Download could not be performed.');
	        Player_Debug::setDebug('Error processing ' . $options . '.');
        
	        Player_Debug::saveLog($path['config'] . $files['log']['file']);
			
			Exit;
			
		}
		
	}
    
    public function setUpdate(){
       
        Player_Debug::setDebug('Moving temporary files...');
        
        Player_Debug::setStatus('Updating files...');
        
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

        foreach ($step as $index){
            
            Player_File::moveFiles($index['files'], $index['path'], $index['temp']);

            $local  = Player_File::listFiles($index['path']);
            
            $diff   = array_diff($index['files'], $local);
            
            Player_File::removeFiles($diff, $index['temp']);
            
        }

        $refresh = array(

            'playlist',
            'loop',

        );
       
        Player_Debug::setDebug('Moving temporary playlist...');
        
        if($this->getRefresh()){
            
            foreach ($refresh as $index) {
                
                Player_File::moveFile($path['config'] . $files[$index]['temp'], $path['config'] . $files[$index]['file'], true);
                
            }
			
			Player_Debug::setClear();
            
        } else {
            
            foreach ($refresh as $index) {
                
                Player_File::unsetFile($path['config'] . $files[$index]['temp']);
                
            }
            
        }
       
        Player_Debug::setDebug('Deleting old files...');
            
        foreach ($step as $index){

            $temp   = Player_File::listFiles($index['temp']);
            
            Player_File::removeFiles($temp, $index['temp']);
            
        }
        
        Player_Debug::saveLog($path['config'] . $files['log']['file']);
		
		return true;
        
    }
    
    public function setDownload(){
       
        Player_Debug::setDebug('Saving settings...');
        
        Player_Debug::setStatus('Finishing download...');
        
        $path   = Player_Flags::getFlag('path');
        $files  = Player_Flags::getFlag('files');
        $label  = Player_Flags::getFlag('label');
        $status = Player_Flags::getFlag('status');
        
        $file   = Player_File::getFile($path['config'] . $files['config']['file']);
        
        $xml    = Player_Convert::getXML($file, $label['config']);
        
        $xml[$status['download']] = 1;
            
        $file   = Player_Convert::setXML($label['config'], $xml);
        
        if(Player_File::setFile($path['config'] . $files['config']['file'], $file, true)){

			if(file_exists($path['config'] . $files['download']['file'])){

				if(Player_File::unsetFile($path['config'] . $files['download']['file'])){
					
					return true;
					
				} else {
					
					return false;
					
				}

			}
		
			return true;

		}
		
		return false;
        
    }
        
}