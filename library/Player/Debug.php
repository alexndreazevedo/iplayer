<?php
require_once 'Player/File.php';
require_once 'Player/Convert.php';
require_once 'Player/Utils.php';

class Player_Debug
{
    
    protected static $_debug = true; //TRUE for default tests
    
    protected static $_log;
    
    public static function getBreakline() {
        
        return PHP_EOL;
        
    }
    
    public static function getTab() {
        
        return "\t";
        
    }
    
    public static function getLog() {
        
        return self::$_log;
        
    }
    
    public static function setLog($log) {
        
        return self::$_log .= $log . self::getBreakline();
        
    }
    
    public static function saveLog($filename) {
        
        if(Player_File::setFile($filename, self::getLog(), true, true)) {
            
            return true;
            
        }
        
        return false;
        
    }
    
    public static function switchDebug($params = null) {
            
        if(is_bool($params)) {

            self::$_debug == $params;

        } else {
            
            self::$_debug = (self::$_debug) ? false : true;
            
        }
        
        return self::$_debug;
        
    }
    
    public static function getDebug() {
        
        return self::$_debug;
        
    }
    
    public static function setDebug($log, $tab = null) {

        if(intval($tab) > 0) {
            
            $tab = Player_Utils::getSeparator(self::getTab(), $tab);
            
        }
        
        $log = $tab . $log;

        if(self::getLog() == null) {
            
            self::setLog(
                
                Player_Utils::getSeparator('=', 70)
                
            );
            
            self::setLog(
                
                'DOWNLOAD LOG - ' . 
                (Player_Utils::getInterface() ? 'PROMPT' : 'BROWSER')
                
            );
            
            self::setLog(
                
                date('d') . '/' . 
                Player_Utils::getMonth(date('m'), array('UPP','CUT')) . '/' . 
                date('Y') . ' - ' . 
                date('h:i:s')
                
            );
            
            self::setLog(
                
                Player_Utils::getSeparator('=', 70)
                
            );
            
            if(self::getDebug()){
            
                echo self::getLog() . self::getBreakline();
                
            }

        }
        
        if(self::getDebug()){
            
            echo $log . self::getBreakline();
            
        }

        self::setLog($log);
        
    }
    
    public static function getStatus($options = false){
        
        $path   = Player_Flags::getFlag('path');
        $files  = Player_Flags::getFlag('files', 'status');
		
		$filename = $path['config'] . $files['file'];
		
		if(!file_exists($filename)){

			$params = array(

				'loop' => 0,
				'msg' => '',
				'status' => '',

			);

			if(Player_Convert::setIni($params, $filename)){
			
				return $params;
				
			}

		} else {
		
			return Player_Convert::getIni($filename, $options);
			
		}
		
		return false;
        
    }
    
    public static function setStatus($msg = false, $status = false, $increment = false, $redefine = false){
        
        $path   = Player_Flags::getFlag('path');
        $files  = Player_Flags::getFlag('files', 'status');
		
		$filename	= $path['config'] . $files['file'];
		$status		= self::getStatus();
		
		if($status){

			if($redefine){

				$status['loop'] = 0;

			} else {

				if($increment) {

					$status['loop'] = intval($status['loop']) + 1;

				}

			}
			
			if($msg) {
				
				$status['msg'] = $msg;
				
			}
			
			if($status) {
				
				$status['status'] = $status;
				
			}

			if(Player_Convert::setIni($status, $filename)){

				return $status;

			}

		}
		
		return false;
        
    }
	
	public static function getClear(){
        
        $path   = Player_Flags::getFlag('path');
        $files  = Player_Flags::getFlag('files', 'refresh');
		
		$filename	= $path['config'] . $files['file'];
		
		if(file_exists($filename)){

			Player_File::unsetFile($filename);
			
			return true;
			
		}
		
		return false;
		
	}
	
	public static function setClear(){
        
        $path   = Player_Flags::getFlag('path');
        $files  = Player_Flags::getFlag('files', 'refresh');
		
		$filename	= $path['config'] . $files['file'];
		
		if(Player_File::setFile($filename)){

			return true;
			
		}
		
		return false;
		
	}

}