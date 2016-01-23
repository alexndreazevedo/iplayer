<?php


class Player_Encrypt
{

    /**
     * Hash type.
     *
     * @var array
     */
    static protected $_hash = 'sha1';

    /**
     * Gets hash.
     *
     * @return string
     */
    static public function getHash()
    {
        
        return self::$_hash;

    }

    /**
     * Sets hash.
     *
     * @return boolean
     */
    static public function setHash($params = null)
    {
        
        if(hash(self::getHash(), $params)){
            
            return true;
            
        }
        
        return false;
        
    }

    /**
     * Sets file hash.
     *
     * @return boolean
     */
    static public function setHashFile($filename = null)
    {
        
        if(file_exists($filename)){
        
            $return = hash_file(self::getHash(), $filename);
            
            if($return){
                
                return $return;
                
            }
            
        }
        
        return false;

    }

    /**
     * Sets file contents hash.
     *
     * @return boolean
     */
    static public function setHashFileContents($filename = null)
    {
        
        if(file_exists($filename)){
            
            $contents = file_get_contents($filename);
            
            if($contents){
                
                $return = hash(self::getHash(), $contents);

                if($return){

                    return $return;

                }
                
            }
            
        }
        
        return false;

    }
    
}