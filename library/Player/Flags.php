<?php


class Player_Flags
{

    /**
     * Root path.
     *
     * @var array
     */
    static protected $_path;

    /**
     * Flags the activation, install and playing mode.
     *
     * @var array
     */
    static protected $_flag;

    /**
     * Gets path.
     *
     * @return string
     */
    static public function getPath($params = null)
    {
        
        if(self::$_path == null) {
            
            self::setPath();
            
        }
        
        if($params == null){
            
            return self::$_path;
            
        } else {
            
            if(isset(self::$_path[$params])){

                return self::$_path[$params];

            }
            
        }
        
        return false;

    }

    
    /**
     * Sets path.
     *
     * @return void
     */
    static public function setPath()
    {
        
        self::$_path = array(

            'root'      => REAL_PATH . DIRECTORY_SEPARATOR,
            'config'    => REAL_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR,
            'file'      => REAL_PATH . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR,
            'media'     => REAL_PATH . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'medias' . DIRECTORY_SEPARATOR,
            'library'   => REAL_PATH . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR,
            'picture'       => REAL_PATH . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'pictures' . DIRECTORY_SEPARATOR,
            'temp'      => 'temp' . DIRECTORY_SEPARATOR,
            
        );
        
    }
    
    /**
     * Gets flags.
     *
     * @param  string $flag null
     * @param  string $sub null
     * @return string
     */
    static public function getFlag($flag = null, $sub = null)
    {
        
        if(self::$_flag == null) {
            
            self::setFlag();
            
            
        }

        if($flag == null) {
            
            return self::$_flag;
            
        } else {
            
            if(isset(self::$_flag[$flag])) {

                if(isset(self::$_flag[$flag][$sub])) {

                    return self::$_flag[$flag][$sub];

                } else {

                    return self::$_flag[$flag];

                }

            }
        
        }

        return false;

    }
    
    /**
     * Sets flags.
     *
     * @param  array $flags array
     * @return array
     */
    static public function setFlag($flags = array())
    {

        self::$_flag = array(


            'status'  => array(

                'active'    => 'ativo',
                'run'       => 'inicializa',
                'download'  => 'fimciclo',

            ),

            'url'  => array(

                'login'     => 'login',
                'config'    => 'config',
                'last'      => 'ultimoacesso',
                'playlist'  => 'playlist',
                'access'     => 'dadosftp',

            ),

            'player'  => array(

                'id'        => 'idplayer',
                'name'      => 'nome',
                'screen'    => 'player',
                'sector'    => 'idponto',
                'code'      => 'senhaAc',
                'status'    => 'configurado',

            ),

            'user'  => array(

                'id'        => 'cliente',
                'login'     => 'login',
                'password'  => 'senha',
                'name'      => 'nomecliente',
                'category'  => 'segmento',

            ),

            'settings'  => array(

                'start'     => 'hrentradaplayer',
                'end'       => 'hrsaidaplayer',
                'width'     => 'resolucaolar',
                'height'    => 'resolucaoalt',
                'duration'  => 'tempoloop',
                'off'       => 'deslautomaticoplayer',
                'status'    => 'inativar',

            ),

            'playlist' => array(
                
                'media'  => array(

                    'index'     => 'midias',
                    'name'      => 'nome',
                    'type'      => 'tipo',
                    'hash'      => 'hash',
                    'status'    => 'status',
                    'file'      => 'arquivo',
                    'filename'  => 'arquivo',
                    'library'   => 'biblioteca',
                    'xml'       => 'xml',
                    'campaign'  => '_campanha',
                    'media'     => '_midia',
                    'status'    => 'status',
                    'duration'  => 'duracao',

                ),

            ),

            'files' => array(

                'download'    => array(

                    'file'      => 'download.bat',

                ),

                'log'    => array(

                    'file'      => 'log.txt',

                ),

                'status'    => array(

                    'file'      => 'status.txt',

                ),

                'config'    => array(

                    'file'      => 'config.xml',

                ),

                'playlist'  => array(

                    'file'      => 'playlist.xml',
                    'temp'      => 'playlist.tmp.xml',

                ),

                'loop'      => array(

                    'file'      => 'loop.xml',
                    'temp'      => 'loop.tmp.xml',

                ),
                
            ),


            'label' => array(

                'config'    => 'config',
                'playlist'  => 'playlist',
                'loop'      => 'loop',
                'library'   => 'biblioteca',
                'ftp'       => 'ftp',
                'feed'      => 'feeds',

            ),

            'field' => array(

                'image'         => 'imagem',
                'description'   => 'descricao',

            ),

            'ftp' => array(

                'host'      => 'host',
                'file'      => 'file',
                'feed'      => 'feed',
                'user'      => 'user',
                'pass'      => 'pass',

            ),

            'layout' => array(

                'index'     => 'layout',
                'login'     => 'login',
                'download'  => 'download',
                'play'      => 'play',

            ),

        );
        
        self::$_flag = array_merge(self::$_flag, array('path' => self::getPath()));
        
    }
    
}