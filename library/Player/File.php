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
     * Remove files in a directory.
     *
     * @param  string $filename null
     * @return boolean
     */
    public static function removeFiles($params = array(), $path = null)
    {

        foreach ($params as $key => $value){
        
            self::unsetFile($path . $value);

        }
        
    }
    
    /**
     * Move files to directory.
     *
     * @param  string $filename null
     * @return boolean
     */
    public static function moveFiles($params = array(), $local = null, $temp = null)
    {

        foreach ($params as $key => $value){
        
            self::moveFile($temp . $value, $local . $value, true);

        }
        
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
    public static function setFile($filename = null, $params = null, $overwrite = false, $increment = false)
    {
        
        if(file_exists($filename) && $overwrite == false && $increment == false){
                    
            return false;
            
        } else {
            
            if($increment){

                $fopen = fopen($filename, 'a+');
                
            } else {

                $fopen = fopen($filename, 'w+');
                
            }
            
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
     * Remove a file.
     *
     * @param  string $filename null
     * @return boolean
     */
    public static function unsetFile($filename = null)
    {
        
        if(file_exists($filename)){
            
            self::setPermissions($filename);
                    
            if(@unlink($filename)){
                
                return true;
                
            }
            
        }
        
        return false;
        
    }
    
    /**
     * Check a directory.
     *
     * @param  string $dirname null
     * @param  boolean $create false
     * @param  mixed $options 0777
     * @return boolean
     */
    public static function getDir($dirname = null, $create = false, $options = 0777)
    {

        if(is_dir($dirname)){
            
            if($create) {
                
                self::setDir($dirname, $options);
                
                return true;
                
            }
                
            return true;
            
        }
        
        return false;
        
    }
    
    /**
     * Makes a directory.
     *
     * @param  string $dirname null
     * @param  boolean $create false
     * @param  mixed $options 0777
     * @return boolean
     */
    public static function setDir($dirname = null, $options = 0777)
    {

        if(is_dir($dirname)){
            
            return true;
            
        } else {
            
            if(mkdir($dirname, $options)) {
                
                return true;
                
            }
                
        }
        
        return false;
        
    }
    
    /**
     * Move a file.
     *
     * @param  string $filename null
     * @param  string $params null
     * @param  string $overwrite false
     * @return boolean
     */
    public static function moveFile($filename = null, $params = null, $overwrite = false)
    {
        
        if(file_exists($params) && $overwrite == false){
            
            return false;
            
        } else {
            
            if(!file_exists($filename)) {

                return false;

            } else {

                self::setPermissions($params);

                if(copy($filename, $params)){

                    self::setPermissions($filename);
                    
                    if(self::unsetFile($filename)){
                    
                        return true;
                        
                    }

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