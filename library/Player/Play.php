<?php

class Player_Play
{
    
    private $_session;
    
    private $_playlist;
    
    protected $_loop;
    
    protected $_campaign;
    
    protected $_media;
    
    protected $_play;
    
    protected $_next = array();
    
    public function __construct($session = null) {

        $this->setPlaylist();
        $this->session($session);
        
    }

	public function run(){
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label', 'playlist');
        $path   = Player_Flags::getFlag('path');
		
        $campaigns  = $this->session()->getSession($media['campaign']);
        $medias     = $this->session()->getSession($media['media']);
        
        $this->checkMedia($campaigns, $medias);
        
        $this->setNewMedia($this->getCampaign(), $this->getMedia());
        
        $next = $this->getNextMedia();
        
        foreach ($next as $key => $value){
        
            $this->session()->setSession($key, $value);
            
        }
        
        return $this->getPlayMedia();
        
    }
    
    public function session($params = null){
        
        if($params != null){
            
            return $this->_session = $params;
            
        }
        
        return $this->_session;
        
    }
    
    public function getPlaylist(){
        
        if($this->_playlist == null){
            
            $this->setPlaylist();
            
        }
        
        return $this->_playlist;
        
    }
    
    public function setPlaylist(){
        
        $files  = Player_Flags::getFlag('files', 'playlist');
        $label  = Player_Flags::getFlag('label', 'playlist');
        $path   = Player_Flags::getFlag('path');
        
        $file = Player_File::getFile($path['config'] . $files['file']);
        
        $playlist = Player_Convert::getXML($file, $label);
        
        $this->_playlist = $playlist;
        
    }
    
    public function getLoop(){
        
        $media  = Player_Flags::getFlag('playlist', 'media');

        if($this->_loop == null) {
        
            $files  = Player_Flags::getFlag('files', 'loop');
            $label  = Player_Flags::getFlag('label', 'loop');
            $path   = Player_Flags::getFlag('path');

            $file = Player_File::getFile($path['config'] . $files['file']);

            $loop = Player_Convert::getXML($file, $label);

            $this->_loop = $loop;
            
        }
        
        foreach($this->_loop as $key => $value){
            
            $check = 0;

            foreach($this->_loop[$key] as $k => $v){
                
                if($this->_loop[$key][$k][$media['status']] == false){
                    
                    $check++;
                    
                }
            
            }

            if($check == count($value)) {
                
                foreach($this->_loop[$key] as $k => $v){

                    $this->_loop[$key][$k][$media['status']] = true;

                }

            }
            
        }
        
        return $this->_loop;
        
    }
    
    public function setLoop($campaigns = null, $medias = null){

        $loop = $this->getLoop();
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $files  = Player_Flags::getFlag('files', 'loop');
        $label  = Player_Flags::getFlag('label', 'loop');
        $path   = Player_Flags::getFlag('path');
        
        if(isset($loop[$campaigns][$medias])){
        
            $loop[$campaigns][$medias][$media['status']] = false;
            
        }

        $file = Player_Convert::setXML($label, $loop);

        if(Player_File::setFile($path['config'] . $files['file'], $file, true)) {

            return true;
            
        }
        
        return false;
        
    }
    
    public function checkLoop($campaigns = null, $medias = null){
        
        $loop = $this->getLoop();
        
        if(isset($loop[$campaigns][$medias])){

            return $loop[$campaigns][$medias];

        }
        
        return false;

    }

    protected function getMedia() {
        
        return $this->_media;
        
    }

    protected function setMedia($params = null) {
        
        $this->_media = $params;
        
        return $this->_media;
        
    }

    protected function checkMedia($campaigns = 0, $medias = 0, $check = false) {
        
        $playlist = $this->getPlaylist();
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $path   = Player_Flags::getFlag('path');
        
        if(!array_key_exists($campaigns, $playlist)){
            
            reset($playlist);

            $campaigns = key($playlist);

        }
            
        $this->setCampaign($campaigns);
        
        if(!array_key_exists($medias, $playlist[$campaigns][$media['index']])){
            
            reset($playlist[$campaigns][$media['index']]);

            $medias = key($playlist[$campaigns][$media['index']]);

        }
            
        $this->setMedia($medias);
        
        if($check){
        
            $set = true;
			
			if(!empty($playlist[$campaigns][$media['index']][$medias])){

				if(!file_exists($path['media'] . $playlist[$campaigns][$media['index']][$medias])){

					foreach ($playlist as $key => $value){

						foreach ($value[$media['index']] as $k => $v){

							if($set){
								
								if(isset($v[$media['filename']])){

									if(file_exists($path['media'] . $v[$media['filename']])){

										$campaigns  = $this->setCampaign($key);
										$medias     = $this->setMedia($k);

										$set = false;

									}

								}

							}

						}

					}

				}

			}

        }
        
        $return = array(
            
            $media['campaign'] => $campaigns,
            $media['media'] => $medias,
            
        );
        
        return $return;
                
    }
    
    protected function getCampaign($params = null) {
        
        return $this->_campaign;
        
    }

    protected function setCampaign($params = null) {
        
        $this->_campaign = $params;
        
        return $this->_campaign;
        
    }

    protected function getNewMedia($campaigns = 0, $medias = 0) {
        
        $playlist = $this->getPlaylist();
        
        $media  = Player_Flags::getFlag('playlist', 'media');
        $path   = Player_Flags::getFlag('path');
        
        $duration   = $playlist[$campaigns][$media['duration']];
        $total      = count($playlist[$campaigns][$media['index']]);

		$select = $playlist[$campaigns][$media['index']][$medias];

        $params = array(

            $media['campaign']  => $campaigns,
            $media['media']     => $medias,
            $media['filename']  => $select[$media['filename']],
            $media['type']      => $select[$media['type']],
            $media['xml']       => ($media['library'] == $select[$media['type']]) ? $select[$media['xml']] : '',
            $media['duration']  => ceil($duration / $total),

        );

        if($select[$media['type']] == $media['file']){

            $this->setLoop($campaigns, $medias);
            
        }
        
        $this->setPlayMedia($params);

    }

    protected function setNewMedia($campaigns = 0, $medias = 0) { //define as midias
        
        $playlist = $this->getPlaylist();

        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label', 'playlist');
        $path   = Player_Flags::getFlag('path');
       
        $this->getNewMedia($campaigns, $medias);
        
        $this->checkNewMedia($campaigns, $medias);

    }

    protected function checkNewMedia($campaigns = 0, $medias = 0) {
        
        $playlist = $this->getPlaylist();

        $media  = Player_Flags::getFlag('playlist', 'media');
        $label  = Player_Flags::getFlag('label', 'playlist');
        $path   = Player_Flags::getFlag('path');
        
        $next   = false;
        $each   = false;
        $return = null;
        
        foreach ($playlist as $key => $value) {

            if($each == true){
                
                $next = true;
                $each = false;
                
            }
            
            foreach ($value[$media['index']] as $k => $v) {

                if(!in_array($campaigns, array_keys($playlist))) {

                    $next = true;

                }

                if($campaigns == $key) {

                    if(!in_array($medias, array_keys($playlist[$key][$media['index']]))) {

                        $each = true;

                    }

                }

                if($next && $return == null){
                            
                    if(file_exists($path['media'] . $v[$media['filename']])) {

                        if($v[$media['type']] == $media['file']){

                            $loop = $this->checkLoop($key, $k);

                            if($loop[$media['status']]){

                                $return = array(

                                    $media['campaign']  => $key,
                                    $media['media']     => $k,

                                );

                            }

                        } else {

                            $return = array(

                                $media['campaign']  => $key,
                                $media['media']     => $k,

                            );

                        }

                    }

                }

                if($key == $campaigns) {

                    if($k == $medias) {
                        
                        if($v[$media['type']] == $media['file']){
                            
                            $each = true;
                            
                        } else {

                            $next = true;
                            
                        }

                    }

                }

            }
            
        }
        
        if($return == null){

            $return = $this->checkMedia($campaigns, $medias, true);
            
        }
        
        $this->setNextMedia($return);
        
    }
    
    public function getPlayMedia() {
        
        return $this->_play;
        
    }
    
    public function setPlayMedia($params = array()) {
        
        $this->_play = $params;
        
    }
    
    public function getNextMedia() {
        
        return $this->_next;
        
    }
    
    public function setNextMedia($params = array()) {
        
        $this->_next = $params;
        
    }

    
}