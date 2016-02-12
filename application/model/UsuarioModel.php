<?php
	/**
	 * UsuarioModel
	 */
	class UsuarioModel
	{
		/**
		 * Método que recibe un email y comprueba que dicho email no exite en la DB
		 * @param  String $email Email a comprobar, ha de ser saneado antes de usarlo en este metodo
		 * @return Boolean       True si hay más de una fila afectada false si no hay filas afectadas
		 */
	    public static function getEmail($email)
	    {
	   		$ssql = 'SELECT * FROM usuario WHERE email = :email';
	   		$params = [':email' => $email];
	   		return Database::consulta($ssql, $params, $estado = 2);
	    }// getEmail()

	    /**
	     * Método que gestiona la lógica del alta de un nuevo usuario
	     * @param  Array $array Array de datos a validar, sanear e insertar
	     * @return Boolean
	     *         			True = si se ha dado de alta.
	     *            		False = si ha habido algún error en el proceso
	     */
	    public static function alta($array)
	    {
	    	if (!$array) {
	    		// generamos el error
	    		Session::add('feedback_negative', 'No se han recibido datos');
	    		return false;
	    	}
    		// hacemos las validaciones
	    	if(UsuarioModel::validar($array)){
	    		// Saneamos el array
	    		$array = Validaciones::sanearEntrada($array);
	    		// Procedemos a la inserción del usuario en la base de datos
	    		// Para ello preestablecemos el array que queremos insertar
	    		$datos = [	':nombre' => $array['nombre'],
	    					':apellido' => $array['apellido'],
	    					':email'  => $array['email'],
	    					':pass'  => sha1($array['clave'])
	    		];
	    		// devolvemos lo que la inserción nos dice
	    		return UsuarioModel::insert($datos);
	    		// procedemos a la inserción de los datos en la base de datos,
	    		// para ello tenemos un método llamado insert

	    	} else {
	    		return false;
	    	}

	    }// Método de alta()

	    /**
	     * Método de inserción del usuario en la DB
	     * @param  Array $array Datos a insertar
	     * @return Boolean      true = si se inserta, false = sino se inserta
	     */
	    public static function insert($array)
	    {
	    	// creamos la consulta
	   		$ssql = 'INSERT INTO usuario (nombre, apellido, email, pass) VALUES (:nombre, :apellido, :email, :pass)';
	   		// las insertamos y se inserta generamos la sesión
	   		if (Database::consulta($ssql, $array, $estado = 3)){
	   			$id = $_POST['last_id'];
	   			Session::set('user_id', $id);
		        Session::set('user_name', $array[':nombre']);
		        Session::set('user_email', $array[':email']);
		        Session::set('user_logged_in', true);
		        // comprobamos que la sesión se ha iniciado correctamente
		        if (Session::comprobarSession()) {
		        	Session::add('feedback_positive', 'Cuenta creada correctamente');
		        	return true;
		        }
		        Session::add('feedback_negative', 'Ha ocurrido un error al iniciar sesión, intentelo de nuevo más tarde');
		        return false;

	   		} else {
	   			Session::add('feedback_negative', 'La cuenta no ha sido creada correctamente');
		        return false;
	   		}
	    }// insert()

	    /**
	     * Método que valida los datos a insertar en la base de datos
	     * @param  Array $array Datos a validar
	     * @return Boolean    True = si los datos son validos, False = sino lo son
	     */
	    public static function validar($array)
	    {
	    	// Si exite el campo lo validamos

	    	// Validación del nombre
	    	if (isset($array['nombre'])) {
	    		if (($erro = Validaciones::validarNombre($array["nombre"])) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'El nombre no ha sido recicibido');
	    	}// fin de las validaciones del nombre

	    	// Validación del apellido
	    	if (isset($array['apellido'])) {
	    		if (($erro = Validaciones::validarApellidos($array["apellido"])) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'Los apellidos no han sido recicibido');
	    	}// fin de las validaciones del apellido

	    	// Validación del email
	    	if (isset($array['email'])) {
	    		if (($erro = Validaciones::validarEmail($array["email"])) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        } else {
		        	// comprobamos que el email no existe en la base de datos
		        	if (UsuarioModel::getEmail($array["email"])) {
		        		Session::add('feedback_negative', 'El email ya exite');
		        	}
		        }
	    	} else {
	    		Session::add('feedback_negative', 'El email no ha sido recicibido');
	    	}// Fin de la validación del email

	    	//validación de las contraseñas
	    	if (isset($array['clave'])) {
	    		if (isset($array['claveRe'])) {
	    			// lógica de las validaciones
	    			if (($erro = Validaciones::validarPassAlta($array["clave"], $array['claveRe'])) !== true) {
			        	Session::addArray('feedback_negative', $erro);
			        }
	    		} else {
	    			Session::add('feedback_negative', 'La clave repetida no se se ha recibido');
	    		}
	    	} else {
	    		Session::add('feedback_negative', 'La clave no se se ha recibido');
	    	}// fin de la validación de las contraseñas

	    	// Comprobación de de que no haya habido errores
	    	return Session::comprobarErrores();

	    }//validar()

	}// fin de la clase
?>