<?php

class Player_Loader
{

    public static function loadClass($class, $dirs = null)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            
            return;
            
        }

        $className = ltrim($class, '\\');
        $file      = '';
        $namespace = '';
        $lastNsPos = strripos($className, '\\');
        
        if ($lastNsPos != false) {
            
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $file      = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            
        }
        
        $file .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (!empty($dirs)) {

            $dirPath = dirname($file);
            
            if (is_string($dirs)) {
                
                $dirs = explode(PATH_SEPARATOR, $dirs);
                
            }
            
            foreach ($dirs as $key => $dir) {
                
                if ($dir == '.') {
                    
                    $dirs[$key] = $dirPath;
                    
                } else {
                    
                    $dir = rtrim($dir, '\\/');
                    $dirs[$key] = $dir . DIRECTORY_SEPARATOR . $dirPath;
                    
                }
                
            }
            
            $file = basename($file);
            
            self::loadFile($file, $dirs, true);
            
        } else {
            
            self::loadFile($file, null, true);
            
        }

    }

    public static function loadFile($filename, $dirs = null, $once = false)
    {

        $incPath = false;
        
        if (!empty($dirs) && (is_array($dirs) || is_string($dirs))) {
            
            if (is_array($dirs)) {
                
                $dirs = implode(PATH_SEPARATOR, $dirs);
                
            }
            
            $incPath = get_include_path();
            set_include_path($dirs . PATH_SEPARATOR . $incPath);
            
        }

        if ($once) {
            
            include_once $filename;
            
        } else {
            
            include $filename;
            
        }

        if ($incPath) {
            
            set_include_path($incPath);
            
        }

        return true;
        
    }

    public static function isReadable($filename)
    {
        
        if (is_readable($filename)) {
            
            return true;
            
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && preg_match('/^[a-z]:/i', $filename)) {

            return false;
            
        }

        foreach (self::explodeIncludePath() as $path) {
            
            if ($path == '.') {
                
                if (is_readable($filename)) {
                    
                    return true;
                    
                }
                
                continue;
                
            }
            
            $file = $path . '/' . $filename;
            
            if (is_readable($file)) {
                
                return true;
                
            }
            
        }
        
        return false;
        
    }

    public static function explodeIncludePath($path = null){
        
        if (null === $path) {
            
            $path = get_include_path();
            
        }

        if (PATH_SEPARATOR == ':') {
            
            $paths = preg_split('#:(?!//)#', $path);
            
        } else {
            
            $paths = explode(PATH_SEPARATOR, $path);
            
        }
        
        return $paths;
        
    }

    public static function autoload($class){
        
        try {
            
            @self::loadClass($class);
            
            return $class;
            
        } catch (Exception $e) {
            
            return false;
            
        }
        
    }

    public static function registerAutoload($class = 'Player_Loader', $enabled = true){
        
        require_once 'Player/Loader/Autoloader.php';
        $autoloader = Player_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        if ('Player_Loader' != $class) {
            
            self::loadClass($class);
            $methods = get_class_methods($class);
            
            $callback = array($class, 'autoload');

            if ($enabled) {
                
                $autoloader->pushAutoloader($callback);
                
            } else {
                
                $autoloader->removeAutoloader($callback);
                
            }
            
        }
        
    }

    protected static function _includeFile($filespec, $once = false){
        
        if ($once) {
            
            return include_once $filespec;
            
        } else {
            
            return include $filespec ;
            
        }
        
    }
    
}