<?php

require_once 'Player/Flags.php';
require_once 'Player/File.php';

class Player_Connect_FTP
{
    const FTP_TIMEOUT                   = 9;
    const FTP_COMMAND_OK                = 200;
    const FTP_FILE_ACTION_OK            = 250;
    const FTP_FILE_TRANSFER_OK          = 226;
    const FTP_COMMAND_NOT_IMPLEMENTED   = 502;
    const FTP_FILE_STATUS               = 213;
    const FTP_NAME_SYSTEM_TYPE          = 215;
    const FTP_PASSIVE_MODE              = 227;
    const FTP_PATHNAME                  = 257;
    const FTP_SERVICE_READY             = 220;
    const FTP_USER_LOGGED_IN            = 230;
    const FTP_PASSWORD_NEEDED           = 331;
    const FTP_USER_NOT_LOGGED_IN        = 530;
    const FTP_ASCII                     = 0;
    const FTP_BINARY                    = 1;

    protected static $passiveMode      = true;
    protected static $lastLines        = array();
    protected static $lastLine         = '';
    protected static $controlSocket    = null;
    protected static $newResult        = false;
    protected static $lastResult       = -1;
    protected static $pasvAddr         = null;

    protected static $error_no         = null;
    protected static $error_msg        = null;
    
    protected static function FTP() {}

    protected static function connect($host, $port = 21, $timeout = self::FTP_TIMEOUT) {
        
        self::_resetError();

        $err_no = 0;
        
        $err_msg = '';
        
        self::$controlSocket = @fsockopen($host, $port, $err_no, $err_msg, $timeout) or self::_setError(-1, 'fsockopen failed');
        
        if ($err_no <> 0) {
            
            self::setError($err_no, $err_msg);
            
        }

        if (self::_isError()) {
            
            return false;
            
        }

        @socket_set_timeout(self::$controlSocket, $timeout) or self::_setError(-1, 'socket_set_timeout failed');
        
        if (self::_isError()) {
            
            return false;
            
        }

        self::_waitForResult();
        
        if (self::_isError()) {
            
            return false;
            
        }

        return self::getLastResult() == self::FTP_SERVICE_READY;
        
    }

    protected static function isConnected() {
        
        return self::$controlSocket != null;
        
    }

    protected static function disconnect() {
        
        if (!self::isConnected()) {
            
            return;
            
        }
        
        @fclose(self::$controlSocket);
        
    }

    protected static function close() {
        
        self::disconnect();
        
    }

    protected static function login($user, $pass) {
        
        self::_resetError();

        self::_printCommand("USER $user");
        
        if (self::_isError()) {
            
            return false;
            
        }

        self::_waitForResult();
        
        if (self::_isError()) {
            
            return false;
            
        }

        if (self::getLastResult() == self::FTP_PASSWORD_NEEDED){
            
            self::_printCommand("PASS $pass");
            
            if (self::_isError()) {
                
                return false;
                
            }

            self::_waitForResult();
            
            if (self::_isError()) {
                
                return false;
                
            }
            
        }

        $result = self::getLastResult() == self::FTP_USER_LOGGED_IN;
        
        return $result;
        
    }

    protected static function cdup() {
        
        self::_resetError();

        self::_printCommand("CDUP");
        
        self::_waitForResult();
        
        $lr = self::getLastResult();
        
        if (self::_isError()) {
            
            return false;
            
        }
        
        return ($lr == self::FTP_FILE_ACTION_OK || $lr == self::FTP_COMMAND_OK);
        
    }

    protected static function cwd($path) {
        
        self::_resetError();

        self::_printCommand("CWD $path");
        
        self::_waitForResult();
        
        $lr = self::getLastResult();
        
        if (self::_isError()) {
            
            return false;
            
        }
        
        return ($lr == self::FTP_FILE_ACTION_OK || $lr == self::FTP_COMMAND_OK);
        
    }

    protected static function chdir($path) {
        
        return self::cwd($path);
        
    }

    protected static function fget($fp, $remote, $mode = self::FTP_BINARY, $resumepos = 0) {
        
        self::_resetError();

        $type = "I";
        
        if ($mode == self::FTP_ASCII) {
            
            $type = "A";
            
        }

        self::_printCommand("TYPE $type");
        
        self::_waitForResult();
        
        $lr = self::getLastResult();
        
        if (self::_isError()) {
            
            return false;
            
        }

        $result = self::_download("RETR $remote");
        
        if ($result) {
            
            fwrite($fp, $result);
            
        }
        
        return $result;
        
    }

    protected static function get_option($option) {
        
        self::_resetError();
        
        switch ($option) {
            
            case 'self::FTP_TIMEOUT_SEC' :
                return self::FTP_TIMEOUT;
                
            case 'PHP_self::FTP_OPT_AUTOSEEK' :
                return false;
                
        }
        
        setError(-1, 'unknown option: ' . $option);
        
        return false;
        
    }

    protected static function get($locale, $remote, $mode = self::FTP_BINARY, $resumepos = 0) {
        
        if (!($fp = @fopen($locale, 'wb'))) {
            
            return false;
            
        }
        
        $result = self::fget($fp, $remote, $mode, $resumepos);
        
        @fclose($fp);
        
        if (!$result) {
            
            @unlink($locale);
            
        }
        
        return $result;
        
    }

    protected static function quit() {
        
        self::close();
        
    }

    protected static function raw($cmd) {
        
        self::_resetError();

        self::_printCommand($cmd);
        
        self::_waitForResult();
        
        self::getLastResult();
        
        return array(self::$lastLine);
        
    }

    protected static function rawlist($remote_filespec = '') {
        
        self::_resetError();
        
        $result = self::_download(trim("LIST $remote_filespec"));
        
        return ($result !== false) ? explode("\n", str_replace("\r", "", trim($result))) : $result;
        
    }

    protected static function getLastResult() {
        
        self::$newResult = false;
        
        return self::$lastResult;
        
    }

    private static function _hasNewResult() {
        
        return self::$newResult;
        
    }

    private static function _waitForResult() {
        
        while(!self::_hasNewResult() && self::_readln() !== false && !self::_isError()) {
            
            /* noop  */
            
        }
        
    }

    private static function _readln() {
        
        $line = fgets(self::$controlSocket);
        
        if ($line === false) {
            
            self::_setError(-1, 'fgets failed in _readln');
            
            return false;
            
        }
        
        if (strlen($line) == 0) {
            
            return $line;
            
        }

        $patch = array();
        
        if (preg_match("/^[0-9][0-9][0-9] /", $line, $patch)) {
            
            self::$lastResult = intval($patch[0]);
            
            self::$newResult = true;
            
            if (substr($patch[0], 0, 1) == '5') {
                
                self::_setError(self::$lastResult, trim(substr($line, 4)));
                
            }
            
        }

        self::$lastLine = trim($line);

        return $line;
        
    }

    private static function _printCommand($line) {

        fwrite(self::$controlSocket, $line . "\r\n");
        
        fflush(self::$controlSocket);
        
    }

    private static function _pasv() {
        
        self::_resetError();
        
        self::_printCommand("PASV");
        
        self::_waitForResult();
        
        $lr = self::getLastResult();
        
        if (self::_isError()) {
            
            return false;
            
        }
        
        if ($lr!=self::FTP_PASSIVE_MODE) {
            
            return false;
            
        }
        
        $subject = trim(substr(self::$lastLine, 4));
        
        $patch = array();
        
        if (preg_match("/\\((\d{1,3}),(\d{1,3}),(\d{1,3}),(\d{1,3}),(\d{1,3}),(\d{1,3})\\)/", $subject, $patch)) {
            
            self::$pasvAddr = $patch;

            $host = sprintf("%d.%d.%d.%d", $patch[1], $patch[2], $patch[3], $patch[4]);
            
            $port = $patch[5] * 256 + $patch[6];

            $err_no = 0;
            
            $err_msg = '';
            
            $passiveConnection = fsockopen($host, $port, $err_no, $err_msg, self::FTP_TIMEOUT);
            
            if ($err_no != 0) {
                
                self::_setError($err_no, $err_msg);
                
                return false;
                
            }

            return $passiveConnection;
            
        }
        
        return false;
        
    }

    private static function _download($cmd) {
        
        if (!($passiveConnection = self::_pasv())) {
            
            return false;
            
        }
        
        self::_printCommand($cmd);
        
        self::_waitForResult();
        
        $lr = self::getLastResult();
        
        if (!self::_isError()) {
            
            $result = '';
            
            while (!feof($passiveConnection)) {
                
                $result .= fgets($passiveConnection);
                
            }
            
            fclose($passiveConnection);
            
            self::_waitForResult();
            
            $lr = self::getLastResult();
            
            return ($lr == self::FTP_FILE_TRANSFER_OK) || ($lr == self::FTP_FILE_ACTION_OK) || ($lr == self::FTP_COMMAND_OK) ? $result : false;
            
        } else {
            
            fclose($passiveConnection);
            
            return false;
            
        }
        
    }

    private static function _resetError() {
        
        self::$error_no = null;
        
        self::$error_msg = null;
        
    }

    private static function _setError($no, $msg) {
        
        if (is_array(self::$error_no)) {
            
            self::$error_no[] = $no;
            
            self::$error_msg[] = $msg;
            
        } else if (self::$error_no != null) {
            
            self::$error_no = array(self::$error_no, $no);
            
            self::$error_msg = array(self::$error_msg, $msg);
            
        } else {
            
            self::$error_no = $no;
            
            self::$error_msg = $msg;
            
        }
        
    }

    private static function _isError() {
        
        return (self::$error_no != null) && (self::$error_no !== 0);
        
    }

    public static function getMediaFTP($host, $user, $pass, $server, $local, $file, $hashing = false) {
        
        $label = Player_Flags::getFlag('playlist','media');
        
        Player_File::setDir($local);
        
        if (self::connect($host)) {
            
            if (self::login($user, $pass)) {
                
                $hostpath = explode('/', $server);
                
                foreach ($hostpath as $value) {
                    
                    self::chdir($value);
                    
                }
                
                $done = array();
                
                foreach ($file as $key => $value) {
                    
                    if(!in_array($value[$label['file']], $done)) {
                            
                        print $local . $value[$label['file']] . "\n";

                        do {

                            $result = true;
                            
                            self::get($local . $value[$label['file']], $value[$label['file']]);

                            $log = self::getLastResult();

                            if(($log == 226) || ($log == 250) || ($log == 200)){

                                if($hashing){
                                    
                                    $hash = Player_Encrypt::setHashFile($local . $value[$label['file']]);

                                    if($value[$label['hash']] === $hash) {

                                        $result = false;

                                        array_push($done, $value[$label['file']]);

                                    }
                                    
                                } else {

                                    $result = false;
                                    
                                    array_push($done, $value[$label['file']]);
                                    
                                }

                            }
                            
                            print $log . "\n";

                        } while($result);
                        
                    }
                    
                }
                
            } else {
                
                print 'Login error.' . "\n";
                
            }
                
            self::disconnect();
            
        } else {
            
            print 'Unable to connect to the server.' . "\n";
            
        }
        
    }
    
}