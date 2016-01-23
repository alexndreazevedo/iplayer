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
    protected $_activation;

    /**
     * Check the validation of the player.
     *
     * @var boolean
     */
    protected $_validate = false;

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

            $this->_filename = APPLICATION_PATH . '/.activation';
            
        }

        $this->setValidate();
        
    }

    /**
     * Gets the player validation status.
     *
     * @return boolean
     */
    public function getValidate() {
        
        return $this->_validate;
        
    }

    /**
     * Sets the player validation to activation file.
     *
     * @return boolean
     */
    public function setValidate() {

        return $this->_validate = true;
        
    }

    /**
     * Gets the player activation code from the .activation filename.
     *
     * @return string
     */
    public function getActivation() {
        
        if(!$this->_activation) {
            
            if (!file_exists($this->_filename)) {

                $this->setActivation();

            } else {

                $fopen = fopen($this->_filename, 'r');

                if ($fopen) {

                    while (!feof($fopen)) {

                        $fgets = fgets($fopen);
                        $pass = $fgets;
                    }

                    fclose($fopen);
                }

                $this->_activation = $pass;

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

        $pass = $this->_setCode();

        $fopen = fopen($this->_filename, 'w+');

        fwrite($fopen, $pass);
        fclose($fopen);

        return $this->_activation = $pass;
        
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