<?php

class LBase {

    static function passEnc($pass) {
        return password_hash(substr_replace(md5($pass), "!%@!((&", 15, 0), PASSWORD_BCRYPT);
    }

    static function passCheck($password, $encPass) {

        return password_verify(substr_replace(md5($password), "!%@!((&", 15, 0), $encPass);
    }

    static function AuthToken($idPlayer) {
        return md5((isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']) ) . (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'no ua') . $idPlayer);
    }

    static function userLogedIn($idPlayer) {
       IF(!$idPlayer)
            return ;
       
        $_SESSION["idPlayer"]  = $idPlayer;
        $_SESSION["idUser"]    = $idPlayer;
        $_SESSION["AuthToken"] = static::AuthToken($idPlayer);
    }

    static function getIpAddress() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }
    
    static function SessionError()
    {
        
    }

    static function newPlayerToken($idPlayer)
    {
        return static::passEnc(md5(static::getIpAddress(). time().$idPlayer. random_int(0, 5e9)."TokenForKey"));
    }
    
    static function  alphaID($in)
    {
        return base_convert($in, 10, 36);
    }
}


