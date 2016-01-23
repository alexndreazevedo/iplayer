<?php

class Player_Validate {

    protected $_activation;
    protected $_validate;
    protected $_filename;
    protected $_options;

    public function __construct($environment = null, $options = null) {

        $this->_filename = APPLICATION_PATH . '/.ac';

        $this->_validate = false;
        $this->setValidate();

        //$this->view->activation_code = $senha;

        if ($options != null) {

            //@TODO
        }


        if ($this->isPost()) {

            $data = $this->getPost();

            $return = $this->accessURL('login', array('login' => $data['login'], 'senha' => $data['senha'], 'senhaAc' => $this->updateAc(), 'configurado' => $data['configurado']));

            $return = array('dev' => $return['dev']);

            $this->write_ini_file($return, $_SERVER['DOCUMENT_ROOT'] . 'public/config.ini', true);

            $this->mensagens($return['dev']);
            
        } else {

            if (($ini['inicializa'] == 1) && ($this->verifyconnection() == 'online')) {

                $ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . 'public/config.ini');

                $return = $this->accessURL('config', array('idplayer' => $ini['idplayer'], 'login' => $ini['login']));

                $return = array('dev' => $return['dev']);

                $this->accessURL('ultimoacesso', array('idplayer' => $ini['idplayer']));

                $this->write_ini_file($return, $_SERVER['DOCUMENT_ROOT'] . 'public/config.ini', true);

                $this->redirect('/playlist/index/');
                
            }
            
        }
        
    }

    public function getValidate() {

        return $this->_validate;
        
    }

    public function setValidate() {

        if ($this->_activation == null || !file_exists($this->_filename)) {

            $this->setActivation();
            
        } else {

            $this->getActivation();
            
        }

        return $this->_validate = true;
        
    }

    public static function getActivation() {

        $fopen = fopen($this->_filename, 'r');

        if ($fopen) {

            while (!feof($fopen)) {

                $fgets = fgets($fopen);
                $pass = $fgets;
            }

            fclose($fopen);
        }

        return $this->_activation = $pass;
        
    }

    public function setActivation() {

        $pass = $this->_setCode();

        $fopen = fopen($this->_filename, 'w+');

        fwrite($fopen, $pass);
        fclose($fopen);

        return $this->_activation = $pass;
        
    }

    protected function _setCode() {

        $return = null;

        $char = 'abcdxywzABCDZYWZ0123456789';
        $limit = strlen($char) - 1;
        $pass = '';

        for ($i = 1; $i < 5; $i++) {

            $pass .= $char{mt_rand(0, $limit)};
        }

        $return = srand(time()) . SHA1($pass) . rand();

        return $return;
        
    }

}