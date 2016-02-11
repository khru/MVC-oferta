<?php

class Session
{
    public static function init()
    {
        if(session_id() == ''){
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
    }

    public static function add($key,$value)
    {
        $_SESSION[$key][] = $value;
    }

    public static function addArray($key,$array)
    {
        foreach ($array as $indice => $value) {
            Session::add($key, $value);
        }
    }

    public static function delete($key)
    {
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    public static function destroy()
    {
        session_destroy();
    }

    public static function userIsLoggedIn()
    {
        return (Session::get('user_logged_in') ? true : false);
    }

    /**
     * Método que comprueba si la sesión esta debidamente creada o no
     * @return Boolean True =  si la sesión esta bien formada, false de lo contrario
     */
    public static function comprobarSession(){
        $estado = true;
        if (!Session::get('user_logged_in')) {
            $estado = false;
        }

        if (!Session::get('user_id')) {
            $estado = false;
        }

        if (!Session::get('user_name')) {
            $estado = false;
        }

        if (!Session::get('user_email')) {
            $estado = false;
        }
        return $estado;
    }
}