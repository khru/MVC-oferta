<?php

class LoginModel
{

    /**
     * Método que realiza la lógica del login
     * @param  Array $datos Datos necesarios para poder realizar el login
     * @return Bollean   True = cuando se realiza el login sin problemas, False = cuando hay errores
     */
    public static function dologin($datos)
    {
        /**
         * Comprobamos que hay datos
         */
        if(!$datos){
            // Sino hay datos damos un error
            Session::add('feedback_negative', 'No tengo los datos de Login');
            return false;
        } elseif (LoginModel::validar($datos)) {
            // hacemos la logica del login
            // primero saneamos el array con los datos
            $datos = validaciones::sanearEntrada($datos);
            $conn = Database::getInstance()->getDatabase();
            $ssql = "SELECT  id, nombre, email, pass FROM usuario WHERE email=:email";
            $query = $conn->prepare($ssql);
            $query->bindValue(':email', $datos['email'], PDO::PARAM_STR);
            $query->execute();
            $count = $query->rowCount();
            if (!Database::comprobarConsulta($count)) {
                Session::add('feedback_negative', 'No estás registrado');
                return false;
            }

            $usuario = $query->fetch();
            if($usuario['pass'] != sha1($datos['clave'])){
                Session::add('feedback_negative', 'La clave no coincide');
                return false;
            }

            // Iniciamos la sesión
            Session::set('user_id', $usuario['id']);
            Session::set('user_name', $usuario['nombre']);
            Session::set('user_email', $datos['email']);
            Session::set('user_logged_in', true);
            Session::add('feedback_positive', 'Sesión iniciada');
            // comprobamos que la sesión se esta formando adecuadamente
            if (Session::comprobarSession()) {
                return true;
            } else {
                Session::add('feedback_negative', 'Error iniciando sesión, intentelo más tarde.');
            }
        } else {
            // sin no se validan los campos correctamente devolvemos un false
            // y el reportamos los errores
            return false;
        }
    }// dologin()

    /**
     * Método encargado de validar datos
     * @param  Array $array Datos a validar
     * @return Boolean      true = si los datos son validos, false = si son invalidos
     */
    public static function validar($array)
    {
        // Validación de la clave
        if(isset($array['clave'])){
            if (($erro = Validaciones::validarPassLogin($array["clave"])) !== true) {
                    Session::addArray('feedback_negative', $erro);
            }
        } else {
            Session::add('feedback_negative', 'No se ha indicado la clave');
        }

        // Validación del email
        if(isset($array['email'])){
            if (($erro = Validaciones::validarEmail($array["email"])) !== true) {
                    Session::addArray('feedback_negative', $erro);
            }
        } else {
            Session::add('feedback_negative', 'No se ha indicado el email');
        }

        // Si hay errores devolvemos false
        if(Session::get('feedback_negative')){
            return false;
        }
        // Si no hay errores devolvemos true
        return true;
    }

}// fin de la clase