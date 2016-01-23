<?php

require_once 'Player/Loader.php';

class Player_Loader_Autoloader
{

    protected static $_instance;

    protected $_autoloaders = array();

    protected $_defaultAutoloader = array('Player_Loader', 'loadClass');

    protected $_fallbackAutoloader = false;

    protected $_internalAutoloader;

    protected $_namespaces = array(
        
        'Player_'  => true,
        
    );

    protected $_namespaceAutoloaders = array();

    public static function getInstance()
    {
        
        if (null === self::$_instance) {
            
            self::$_instance = new self();
            
        }
        
        return self::$_instance;
        
    }

    public static function resetInstance()
    {
        
        self::$_instance = null;
        
    }

    public static function autoload($class)
    {
        
        $self = self::getInstance();

        foreach ($self->getClassAutoloaders($class) as $autoloader) {
            
            if ($autoloader instanceof Zend_Loader_Autoloader_Interface) {
                
                if ($autoloader->autoload($class)) {
                    
                    return true;
                    
                }
                
            } elseif (is_array($autoloader)) {
                
                if (call_user_func($autoloader, $class)) {
                    
                    return true;
                    
                }
                
            } elseif (is_string($autoloader) || is_callable($autoloader)) {
                
                if ($autoloader($class)) {
                    
                    return true;
                    
                }
                
            }
            
        }

        return false;
        
    }

    public function setDefaultAutoloader($callback)
    {
        
        $this->_defaultAutoloader = $callback;
        
        return $this;
        
    }

    public function getDefaultAutoloader()
    {
        
        return $this->_defaultAutoloader;
        
    }

    public function setAutoloaders(array $autoloaders)
    {
        
        $this->_autoloaders = $autoloaders;
        
        return $this;
        
    }

    public function getAutoloaders()
    {
        
        return $this->_autoloaders;
        
    }

    public function getNamespaceAutoloaders($namespace)
    {
        
        $namespace = (string) $namespace;
        
        if (!array_key_exists($namespace, $this->_namespaceAutoloaders)) {
            
            return array();
            
        }
        
        return $this->_namespaceAutoloaders[$namespace];
        
    }

    public function registerNamespace($namespace)
    {
        
        if (is_string($namespace)) {
            
            $namespace = (array) $namespace;
            
        }

        foreach ($namespace as $ns) {
            
            if (!isset($this->_namespaces[$ns])) {
                
                $this->_namespaces[$ns] = true;
                
            }
            
        }
        
        return $this;
        
    }

    public function unregisterNamespace($namespace)
    {
        
        if (is_string($namespace)) {
            
            $namespace = (array) $namespace;
            
        }

        foreach ($namespace as $ns) {
            
            if (isset($this->_namespaces[$ns])) {
                
                unset($this->_namespaces[$ns]);
                
            }
            
        }
        
        return $this;
        
    }

    public function getRegisteredNamespaces()
    {
        
        return array_keys($this->_namespaces);
        
    }

    public function setFallbackAutoloader($flag)
    {
        
        $this->_fallbackAutoloader = (bool) $flag;
        
        return $this;
        
    }

    public function isFallbackAutoloader()
    {
        
        return $this->_fallbackAutoloader;
        
    }

    public function getClassAutoloaders($class)
    {
        
        $namespace   = false;
        $autoloaders = array();

        foreach (array_keys($this->_namespaceAutoloaders) as $ns) {
            
            if ('' == $ns) {
                
                continue;
                
            }
            
            if (0 === strpos($class, $ns)) {
                
                if ((false === $namespace) || (strlen($ns) > strlen($namespace))) {
                    
                    $namespace = $ns;
                    $autoloaders = $this->getNamespaceAutoloaders($ns);
                    
                }
                
            }
            
        }

        foreach ($this->getRegisteredNamespaces() as $ns) {
            
            if (0 === strpos($class, $ns)) {
                
                $namespace     = $ns;
                $autoloaders[] = $this->_internalAutoloader;
                
                break;
                
            }
            
        }

        $autoloadersNonNamespace = $this->getNamespaceAutoloaders('');
        
        if (count($autoloadersNonNamespace)) {
            
            foreach ($autoloadersNonNamespace as $ns) {
                
                $autoloaders[] = $ns;
                
            }
            
            unset($autoloadersNonNamespace);
            
        }

        if (!$namespace && $this->isFallbackAutoloader()) {
            
            $autoloaders[] = $this->_internalAutoloader;
            
        }

        return $autoloaders;
        
    }

    public function unshiftAutoloader($callback, $namespace = '')
    {
        
        $autoloaders = $this->getAutoloaders();
        array_unshift($autoloaders, $callback);
        $this->setAutoloaders($autoloaders);
        $namespace = (array) $namespace;
        
        foreach ($namespace as $ns) {
            
            $autoloaders = $this->getNamespaceAutoloaders($ns);
            array_unshift($autoloaders, $callback);
            $this->_setNamespaceAutoloaders($autoloaders, $ns);
            
        }

        return $this;
        
    }

    public function pushAutoloader($callback, $namespace = '')
    {
        
        $autoloaders = $this->getAutoloaders();
        array_push($autoloaders, $callback);
        $this->setAutoloaders($autoloaders);
        $namespace = (array) $namespace;
        
        foreach ($namespace as $ns) {
            
            $autoloaders = $this->getNamespaceAutoloaders($ns);
            array_push($autoloaders, $callback);
            $this->_setNamespaceAutoloaders($autoloaders, $ns);
            
        }

        return $this;
        
    }

    public function removeAutoloader($callback, $namespace = null)
    {
        
        if (null === $namespace) {
            
            $autoloaders = $this->getAutoloaders();
            
            if (false !== ($index = array_search($callback, $autoloaders, true))) {
                
                unset($autoloaders[$index]);
                $this->setAutoloaders($autoloaders);
                
            }

            foreach ($this->_namespaceAutoloaders as $ns => $autoloaders) {
                
                if (false !== ($index = array_search($callback, $autoloaders, true))) {
                    
                    unset($autoloaders[$index]);
                    $this->_setNamespaceAutoloaders($autoloaders, $ns);
                    
                }
                
            }
            
        } else {
            
            $namespace = (array) $namespace;
            
            foreach ($namespace as $ns) {
                
                $autoloaders = $this->getNamespaceAutoloaders($ns);
                
                if (false !== ($index = array_search($callback, $autoloaders, true))) {
                    
                    unset($autoloaders[$index]);
                    $this->_setNamespaceAutoloaders($autoloaders, $ns);
                    
                }
                
            }
            
        }

        return $this;
        
    }

    protected function __construct()
    {
        
        spl_autoload_register(array(__CLASS__, 'autoload'));
        $this->_internalAutoloader = array($this, '_autoload');
        
    }

    protected function _autoload($class)
    {
        
        $callback = $this->getDefaultAutoloader();
        
        try {

            call_user_func($callback, $class);
            
            return $class;
            
        } catch (Exception $e) {
            
            return false;
            
        }
        
    }
    
    protected function _setNamespaceAutoloaders(array $autoloaders, $namespace = '')
    {
        
        $namespace = (string) $namespace;
        $this->_namespaceAutoloaders[$namespace] = $autoloaders;
        
        return $this;
        
    }

}