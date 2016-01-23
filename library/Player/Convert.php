<?php

/**
 * Player_Convert
 * 
 * Converts values
 */
class Player_Convert
{
    
    /**
     * Convert a values to array
     *
     * @param  array $params null
     * @return array
     */
    public static function toArray($params) {
        
        $return = array();

        if(gettype($params) == 'string' || gettype($params) == 'integer' || gettype($params) == 'double') {
            
            array_push($return, $params);
            
        } else if(gettype($params) == 'boolean') {
            
            array_push($return, ($params) ? 'true' : 'false');
            
        } else if(gettype($params) == 'object') {
            
            foreach ($params as $key => $value) {

                $return[$key] = self::toArray($value);

            }

        }
        
        return $return;
        
    }
    
    /**
     * Convert a values to XML
     *
     * @param  array $params array
     * @param  string $filename null
     * @return boolean
     */
    public static function toXML($params = array(), $filename = null) {
             
        if (is_array($params)) {

            function tag($XML, $params) {
                
                foreach ($params as $key => $value) {
                    
                    $key = (is_numeric($key)) ? '_' . $key : $key;

                    if (is_array($value)) {
                        
                        $XML->startElement($key);
                        tag($XML, $value);
                        
                    } else {
                        
                        $XML->writeElement($key, $value);
                        
                    }
                    
                }
                
                $XML->endElement();
                
            }

            $XML = new XMLWriter();
            
            $XML->openURI($filename);
            $XML->startDocument('1.0', 'UTF-8');
            
            tag($XML, $params);
            
            $XML->endDocument();
            
            sleep(10);
            
            return true;
            
        } else {
            
            return false;
            
        }
        
    }
    
    /**
     * Read a .ini file.
     *
     * @param  string $params null
     * @return array
     */
    public static function getIni($params = null) {
        
        return parse_ini_file($params);
        
    }
    
    /**
     * Write a .ini file.
     *
     * @param  array $params array
     * @param  string $path null
     * @param  string $level -1
     * @param  string $result false
     * @return mixed
     */
    public static function setIni($params = array(), $path = null, $level = -1, $result = false) {
        
        //@TODO
        
        $return = '';
        $level++;

        foreach ($params as $key => $value) {
            
            if (is_array($value)) {

                if($level == 0) {

                    $return .= '[' . $key . ']' . "\n";

                }

                if(count($value) > 1) {

                    $return .= self::setIni($value, $path, $level);
                    
                } else {
                    
                    $return .= $key . ' = "' . self::setIni($value, $path, $level) . '"' . "\n";
                    
                }

            } else if ($value == '') {

                $return .= $key;

            } else {

                $return .= $value;

            }

        }

        if($level == 0) {

            $handle = fopen($path, 'w');

            if (!$handle) {

                return false;

            }

            if (!fwrite($handle, $return)) {

                return false;

            }

            fclose($handle);
            
            if($result) {
        
                return $return;
                
            } else {
            
                return true;
                
            }
            
        } else {
            
            return $return;
            
        }
        
    }
    
    /**
     * Decode a Json file to array;
     *
     * @param  mixed $params null
     * @param  boolean $options true
     * @return array
     */
    public static function getJson($params = null, $options = true) {

        return json_decode($params);
        
    }
    
    /**
     * Encode a array to Json file;
     *
     * @param  mixed $params null
     * @param  int $params [optional]
     * @return string
     */
    public static function setJson($params = null, $options = null) {

        return json_encode($params, $options);
        
    }
    
}