<?php

class Player_Convert
{
    
    public static function toArray($params) {
        
        $return = array();

        if(gettype($params) == 'string' || gettype($params) == 'integer' || gettype($params) == 'double') {
            
            array_push($return, $params);
            
        } else if(gettype($params) == 'boolean') {
            
            array_push($return, ($params) ? 'true' : 'false');
            
        } else if(gettype($params) == 'object') {
            
            foreach ($params as $key => $value) {

                $return[$key] = $this->toArray($value);

            }

        }
        
        return $return;
        
    }
    
    public static function toXML($array, $arquivo) {
        
        //@TODO
             
        /*
        if (is_array($array)) {

            function create_tag($XML, $array) {
                foreach ($array as $tag_name => $value) {
                    $tag_name = is_numeric($tag_name) ? "_" . $tag_name : $tag_name;

                    if (is_array($value)) {
                        $XML->startElement($tag_name);
                        create_tag($XML, $value);
                    } else {
                        $XML->writeElement($tag_name, $value);
                    }
                }
                $XML->endElement();
            }

            $XML = new XMLWriter();
//                $XML->openMemory();
            $XML->openURI($arquivo);
            $XML->startDocument('1.0', 'UTF-8');
            create_tag($XML, $array);
            $XML->endDocument();
//                $XML->flush();
            sleep(10);
            return true;
//                echo $XML->outputMemory(true);
//                return utf8_encode();
//				sleep(5);
        } else {
            return false;
        }
        */
        
    }
    
    public static function getIni($params) {
        
        return parse_ini_file($params);
        
    }
    
    public static function setIni($assoc_arr, $path, $has_sections=FALSE) {
        
        $content = "";
        
        if ($has_sections) {
            
            foreach ($assoc_arr as $key => $elem) {
                
                $content .= "[" . $key . "]\n";
                
                foreach ($elem as $key2 => $elem2) {
                    
                    if (is_array($elem2)) {
                        
                        for ($i = 0; $i < count($elem2); $i++) {
                            
                            $content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
                            
                        }
                        
                    } else if ($elem2 == "") {
                        
                        $content .= $key2 . " = \n";
                        
                    } else {
                        
                        $content .= $key2 . " = \"" . $elem2 . "\"\n";
                        
                    }
                }
                
            }
            
        } else {
            
            foreach ($assoc_arr as $key => $elem) {
                
                if (is_array($elem)) {
                    
                    for ($i = 0; $i < count($elem); $i++) {
                        
                        $content .= $key2 . "[] = \"" . $elem[$i] . "\"\n";
                        
                    }
                    
                } else if ($elem == "") {
                    
                    $content .= $key2 . " = \n";
                    
                } else {
                    
                    $content .= $key2 . " = \"" . $elem . "\"\n";
                    
                }
                
            }
            
        }

        if (!$handle = fopen($path, 'w')) {
            
            return false;
            
        }
        
        if (!fwrite($handle, $content)) {

            return false;

        }

        fclose($handle);
        return true;
        
    }
    
    public static function getJson($params) {

        return json_decode($params);
        
    }
    
    public static function setJson($params) {

        return json_encode($params);
        
    }
    
}