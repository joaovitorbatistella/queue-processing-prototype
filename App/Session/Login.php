<?php
namespace Session;

class Login{
    private static function init(){
        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    public static function getUsuarioLogado(){
        self::init(); 
        return self::isLogged() ? $_SESSION['user'] : null;
    }

    public static function isLogged(){
        self::init();
        return isset($_SESSION['user']['id']);
    }

    public static function requiredLogin(){
        if(!self::isLogged()){
            header('location: '.APP_URL.'/user/login');
            exit;
        }
    }
    
    public static function requiredLogout(){
        if(self::isLogged()){
            header('location: '.APP_URL.'/user/login');
            exit;
        }
    }
    
    public static function login($user){
        self::init();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email']
        ];
    }

    public static function logout(){
        self::init();
        setcookie("logged",  "", time() - 3600);
        unset($_SESSION['user']);
        header('location: '.APP_URL.'/user/login');
        exit;
    }
}    
?>