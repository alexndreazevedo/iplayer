<?php

class Player_File
{
    
    /**
     * List files in a directory.
     *
     * @param  string $filename null
     * @return boolean
     */
    public static function listFiles($params = null)
    {
        
        $array = array();

        if(file_exists($params)){

            $opendir = opendir($params);

            while ($filename = readdir($opendir)) {
                
                if(is_file($params . $filename)){

                    $array[] = $filename;
                    
                }

            }
            
            return $array;
        
        }
        
        return false;
        
    }
    
    /**
     * Read a file.
     *
     * @param  string $filename null
     * @return boolean
     */
    public static function getFile($filename = null)
    {

        if(file_exists($filename)){
            
            $contents = @file_get_contents($filename);
            
            if($contents){
                
                return $contents;
                    
            }
            
        }
        
        return false;
        
    }
    
    /**
     * Write a file.
     *
     * @param  string $filename null
     * @param  string $params null
     * @param  string $overwrite false
     * @return boolean
     */
    public static function setFile($filename = null, $params = null, $overwrite = false)
    {
        
        if(file_exists($filename) && $overwrite == false){
                    
            return false;
            
        } else {

            $fopen = fopen($filename, 'w+');
            
            if($fopen){

                $write = fwrite($fopen, $params);

                fclose($fopen);

                if($write){

                    return true;

                }
                
            }
                
        }
        
        return false;
        
    }
    
    /**
     * Copy a file.
     *
     * @param  string $filename null
     * @param  string $params null
     * @param  string $overwrite false
     * @return boolean
     */
    public static function copyFile($filename = null, $params = null, $overwrite = false)
    {
        
        if(file_exists($params) && $overwrite == false){
            
            return false;
            
        } else {
            
            if(!file_exists($filename)) {

                self::setFile($filename, null, $overwrite);

            }
            
            if(copy($filename, $params)){

                return true;

            }
            
        }

        return false;
        
    }
    
    /**
     * Reads a permission of a file.
     *
     * @param  mixed $params null
     * @return string
     */
    public static function getPermissions($params = null) {

        return fileperms($params);
        
    }
    
    /**
     * Changes a permission of a file.
     *
     * @param  mixed $params null
     * @param  midex $options
     * @return boolean
     */
    public static function setPermissions($params = null, $options = 0777) {

        return @chmod($params, $options);
        
    }

}