<?php

/**
 * Player_Convert
 * 
 * Converts values
 */
class Player_Convert
{
    
    public static function getXML($params = null, $target = null, $encoding = 'UTF-8') {
        
        if (!$params) {
            
            return false;
            
        }

        if (!function_exists('xml_parser_create')) {

            return false;
            
        }

        $xml = xml_parser_create($encoding);
        
        xml_parser_set_option($xml, XML_OPTION_TARGET_ENCODING, $encoding);
        xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($xml, $params, $values);
        xml_parser_free($xml);

        if (!$values){
            
            return;
            
        }

        $array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$array;

        $repeated_tag_index = array();
        
        foreach ($values as $data) {
            
            unset($attributes, $value);

            extract($data);

            $result = array();
            $attributes_data = array();
            
            if (isset($value)) {
                
                $value = ($value === 'true') ? true : $value;
                $value = ($value === 'false') ? false : $value;
                
                if(is_string($value)) {
                
                    $result = Player_Utils::setEncode($value);
                    
                } else {
                
                    $result = $value;
                    
                }

            }

            if (isset($attributes)) {
                
                foreach ($attributes as $attr => $val) {
                    
                    $attributes_data[$attr] = Player_Utils::setEncode($val);
                    
                }
                
            }

            if ($type == 'open') {

                if($tag == 'item'){

                    $tag = $attributes_data['id'];

                }
                
                $parent[$level - 1] = &$current;
                
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) {

                    $current[$tag] = $result;
                    
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                    
                } else {
                    
                    if (isset($current[$tag][0])) {
                        
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                        
                    } else {
                        
                        $current[$tag] = array($current[$tag], $result);
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                    }
                    
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                    
                }
                
            } elseif ($type == 'complete') {
                
                if (!isset($current[$tag])) {
                    
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    
                } else {
                    
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {

                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        $repeated_tag_index[$tag . '_' . $level]++;
                        
                    } else {
                        
                        $current[$tag] = array($current[$tag], $result);
                        
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        
                        $repeated_tag_index[$tag . '_' . $level]++;
                        
                    }
                    
                }
                
            } elseif ($type == 'close') {
                
                $current = &$parent[$level - 1];
                
            }
            
        }

        if($target != null){
            
            if(isset($array[$target])){
        
                return $array[$target];
                
            }
            
        }
        
        return $array;
        
    }
    
    public static function setXML($tag = 'xml', $params = array(), $encoding = 'UTF-8') {
        
        $xml = new DomDocument('1.0', $encoding);
        
        $xml->formatOutput = true;

        $child = self::setXMLTag($xml, $tag, $params, $encoding);

        $xml->appendChild($child);
        
        return $xml->saveXML();

    }

    public static function &setXMLTag($xml, $key = 'xml', $params = null, $encoding = 'UTF-8')
    {

        if(is_numeric($key)) {

            $tag = $xml->createElement('item');

            $tag->setAttribute('id', $key);

        } else {

            $tag = $xml->createElement($key);
            
        }

        if(is_array($params)){

            foreach($params as $key => $value) {
        
                $child = self::setXMLTag($xml, $key, $value, $encoding);
                       
                $tag->appendChild($child);

            }

        } else {
            
            $params = ($params === true) ? 'true' : $params;
            $params = ($params === false) ? 'false' : $params;
                
            $child = Player_Utils::setEncode($params);
                
            $tag->appendChild($xml->createTextNode($child));
            
        }

        return $tag;

    }
    
    /**
     * Read a .ini file.
     *
     * @param  string $params null
     * @return array
     */
    public static function getIni($params = null, $options = false)
    {
        return parse_ini_file($params, $options);
        
    }
    
    /**
     * Write a .ini file.
     *
     * @param  array $params array
     * @param  string $filename null
     * @param  string $level -1
     * @return mixed
     */
    public static function setIni($params = array(), $filename = null, $level = -1)
    {
        
        if($params && $filename) {
            
            $return = '';
            $level++;

            foreach ($params as $key => $value) {

                if (is_array($value)) {

                    if(count($value) > 1) {

                        $return .= self::setIni($value, $filename, $level);

                    } else {

                        $return .= $key . ' = "' . self::setIni($value, $filename, $level) . '"' . "\n";

                    }

                } else if ($value == '') {

                    $return .= $key;

                } else {

                    $return .= $value;

                }

            }

            if($level == 0) {

                $return = Player_File::setFile($filename, $return, true);

            }

            return $return;
            
        }
        
        return false;
        
    }
    
    /**
     * Decode a Json file to array;
     *
     * @param  mixed $params null
     * @param  boolean $options true
     * @return array
     */
    public static function getJson($params = null, $options = false) {

        return @json_decode($params, $options);
        
    }
    
    /**
     * Encode a array to Json file;
     *
     * @param  mixed $params null
     * @param  int $params [optional]
     * @return string
     */
    public static function setJson($params = null, $options = null) {

        return @json_encode($params, $options);
        
    }
    
}