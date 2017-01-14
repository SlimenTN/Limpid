<?php
namespace framework\core\Request;

/**
 * Class SessionHandler
 * Session handler gives a simple functions to manipulate the $_SESSION array
 * @package framework\core\Request
 * 
 * Arnaout slimen <arnaout.slimen@sbc.tn>
 */
class SessionHandler
{
    
    public static function initSession(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function push($index, $value){
        $_SESSION[$index] = $value;
    }
    
    public function get($index){
        return $_SESSION[$index];
    }

    public function remove($index){
        if($this->exist($index)) unset($_SESSION[$index]);
    }

    public function clean($index){
        if($this->exist($index)) $_SESSION[$index] = null;
    }
    
    public function destroy(){
        session_destroy();
    }
    
    public function exist($index){
        return array_key_exists($index, $_SESSION);
    }
}