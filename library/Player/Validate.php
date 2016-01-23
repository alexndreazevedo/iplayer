<?php

/**
 * Player_Validate
 * 
 * Validates values in the player
 */
class Player_Validate
{

    /**
     * Activation code from .activation file.
     *
     * @var string
     */
    protected $_activation = false;

    /**
     * Location of the .activation file.
     *
     * @var string
     */
    protected $_filename;

    /**
     * Options of the Player_Validate class.
     *
     * @var array
     */
    protected $_options;

    /**
     * Player_Validate class
     * 
     * Sets and gets the validation of the player
     *
     * @param  string $environment null
     * @param  array $options null
     * @return void
     */
    public function __construct($environment = null, $options = null) {

        if ($options !== null) {
            
            //@TODO
            
        } else {

            $this->_filename = CONFIG_PATH . DIRECTORY_SEPARATOR . '.activation';
            
        }

        $this->getActivation();
        
    }

    /**
     * Gets the player activation code from the .activation filename.
     *
     * @return string
     */
    public function getActivation() {
        
        if(!$this->_activation) {
            
            $activation = Player_File::getFile($this->_filename);
            
            if($activation) {

                $this->_activation = $activation;

            } else {
                
                $this->setActivation();

            }
            
        }

        return $this->_activation;

    }

    /**
     * Sets the player activation code and writes in the .activation filename.
     *
     * @return string
     */
    public function setActivation() {

        $code = $this->_setCode();
        
        $activation = Player_Convert::setFile($this->_filename, $code);

        $this->_activation = $activation;
            
        return $this->_activation;
        
    }

    /**
     * Generate the activation code.
     *
     * @return string
     */
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