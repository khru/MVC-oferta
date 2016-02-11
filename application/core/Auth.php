<?php

class Auth
{
    public static function checkAutentication()
    {
        // No es necesario la inicialización de la sesión pero es una comprobación más
        Session::init();
        if(!Session::comprobarSession()){
            Session::destroy();
            Session::init();
    		Session::set('origen', $_SERVER['REQUEST_URI']);
            header('location: /Login');
            exit();
        }
    }

    public static function checkNoAutentication()
    {
        // No es necesario la inicialización de la sesión pero es una comprobación más
        Session::init();
        if(Session::userIsLoggedIn()){
            header('location: /');
            exit();
        }
    }
}