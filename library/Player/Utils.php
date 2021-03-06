<?php

/**
 * Player_Utils
 * 
 * Some utils functions like date
 */
class Player_Utils
{

    public function getAccented($params = null, $caps = false) {
        
        $accented = array(
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 
            'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 
            'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 
            'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 
            'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 
            'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 
            'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 
            'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 
            'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 
            'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 
            'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 
            'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 
            'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', 
            '?'
        );
        
        $normal = array(
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 
            'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 
            'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 
            'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 
            'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 
            'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 
            'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 
            'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 
            'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 
            'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 
            'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 
            'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 
            'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 
            'o'
        );
        
        $return = preg_replace(
            
            array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), 
            array('', '-', ''), 
            str_replace($accented, $normal, $params)
            
        );
        
        if($caps) {
            
            $return = strtolower($return);
            
        }
        
        return $return;
        
    }
	
	public static function setSwitch($bool = null, $true = null, $false = null){
		
        if(is_bool($bool)) {

			if($bool) {
				
				return $false;
				
			} else {
				
				return $true;
				
			}

        } else {
            
            return ($bool) ? $false : $true;
            
        }
        
        return $false;
		
	}
    
    public static function getSeparator($params = null, $options = null) {
        
        $return = $params;
        
        for($i = 1; $i < (int) $options; $i++){
            
            $return .= $params;
            
        }
        
        return $return;
        
    }
        
    public static function getMonth($params = null, $options = null) {
        
        $month = array(
            
            'janeiro',
            'fevereiro',
            'março',
            'abril',
            'maio',
            'junho',
            'julho',
            'agosto',
            'setembro',
            'outubro',
            'novembro',
            'dezembro',
            
        );
        
        if(is_array($options)) {
            
            if(in_array('ALL', $options)) {
                
                $return = $month;
                
                foreach ($return as $key => $value){

                    if(in_array('CUT', $options)) {

                        $return[$key] = substr($value, 0, 3);

                    }
                    
                    if(in_array('CAP', $options)) {

                        $return[$key] = ucfirst($value);

                    }

                    if(in_array('ACC', $options)) {

                        $return[$key] = self::getAccented($value);

                    }
                    
                    if(in_array('UPP', $options)) {

                        $return[$key] = strtoupper($value);

                    }
                    
                }
                
            } else {
                
                $return = $month[(int) $params];

                if(in_array('CUT', $options)) {

                    $return = substr($return, 0, 3);

                }
                
                if(in_array('CAP', $options)) {

                    $return = ucfirst($return);

                }

                if(in_array('ACC', $options)) {

                    $return = self::getAccented($return);

                }
                
                if(in_array('UPP', $options)) {

                    $return = strtoupper($return);

                }

            }
            
        } else {
            
            $return = $month[(int) $params];
            
        }

        return $return;
        
    }

    static public function setEncode($params = null, $encoding = 'UTF-8', $default = 'ISO-8859-1') {
        
        return html_entity_decode(htmlentities($params, ENT_COMPAT, $encoding), ENT_COMPAT, $default);
        
    }

    static public function redirect($params = ''){
        
        return header('Location: http://' . $_SERVER["HTTP_HOST"] . '/' . $params);
        
    }
    
    static public function getInterface() {
        
        if(php_sapi_name() == 'cli') {
            
            return true;
            
        }
        
        return false;
        
    }
    
}