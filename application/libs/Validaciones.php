<?php
	/**
	 * Clase de validaciones
	 * @author Emmanuel Valverde Ramos
	 * @version 1.0 10-01-2016
	 */
	class Validaciones
	{
		/**
		 * Método empleado para devolver errores o no
		 * @param  Array $errores Array con los errores de la aplicación
		 * @return true | array  resultado()
		 */
		public static function resultado($errores){
			if (!$errores) {
				return true;
			}
			return $errores;
		}// resultado()

		/**
		 * Método de saneamiento de parametros, este método también es
		 * el causante de que no se puedan insertar tildes, puesto que si no pongo el
		 * htmlentities, no puedo escapar la inyección de código HTML
		 * @param  Strin $parm Parametro a sanear
		 * @return true | array  resultado()
		 */
		public static function saneamiento($parm){
			$parm = strip_tags($parm);
			$parm = trim($parm);
			return $parm;
		}// saneamiento()

		/**
		 * Método estatico que valida una imagen empleando la variable globarl $_FILES
		 * @return [type] [description]
		 */
		public static function validarImg($img)
		{
			$errores = [];
			if (isset($img["img_perf"])) {
		 		//Declaramos el tamaño maximo del fichero
		 		$tam_max = 500000;
		 		//Realizamos las validaciones
		 		if ($img["img_perf"]["size"] > $tam_max) {
		 			$errores["img_perf"] = "El fichero es demasiado grande";
		 		} elseif (!self::cabeceraFichero($img["img_perf"]["type"])) {
		 			// Comprobamos que el fichero tiene una extensión valida sino error
		 			$errores["img_perf"]  = "El fichero no es una imagen";
		 		} else {
		 			//Como no nos podemos fiar del usuario, vamos a comprobar el mime obteniendo todos los mimes
		 			$mime = finfo_open(FILEINFO_MIME);
		 			if(!$mime){
		 				// Si no se puede declarar $mime por la razón que sea no subir el fichero
		 				$errores["img_perf"] = "Por motivos de seguridad no se a podido analizar su archivo";
		 			} else {
		 				// si la cabecera original del fichero
		 				if (!self::tipoFichero($img["img_perf"], $mime)) {
		 					$errores["img_perf"] = "El fichero no es un una imagen";
		 				}
		 			}
		 		}
		 	}
		 	return self::resultado($errores);
		} // validarImg

		public static function subirImg($id_con, $img){
			$errores = [];
			// Si pasa la comprobaciones
			// declaramos la ruta del directorio donde almacenaremos lasimagenes
			$dir = FOTOS;
			// le daremos al fichero la ruta donde se va a almacenar, una cadena aleatoria. puesto que si alguien
			// nos sube 2 fichero con el mismo nombre el servidor sobreescribe, la primera imagen y lo concateno
			// con el nombre de la imagen, para seguir conteniendo la extensión del fichero
			$fichero = $dir  . $id_con . $img["img_perf"]["name"];
			// creamos el directorio si da error mostramos un error
			if(self::crearDir($dir)){
				if (!move_uploaded_file($img["img_perf"]["tmp_name"],$fichero)) {
					$errores["img_perf"] = "Error guardando la imagen";
				}
			} else {
				$errores["img_perf"] = "No se ha podido subir la foto al servidor";
			}
			return self::resultado($errores);
		}


 	public static function crearDir($dir){
 		if (file_exists($dir)) {
 			return true;
 		} else{
 			if (mkdir($dir, "0700")) {
 				# Si se crea la carpeta $dir correctamente
 				return true;
 			}
 			// Si el directorio no se puede crear
 			return false;
 		}
 	}

 	/**
 	 * Método que comprueba las cabeceras mime
 	 * @param  String $cabecera_mime cabecera a comprobar
 	 * @return Boolean
 	 */
 	public static function cabeceraFichero($cabecera_mime){
 		switch ($cabecera_mime) {
 			case 'image/png':
 				return true;
 			break;
 			case 'image/jpeg':
 				return true;
 			break;
 			case 'image/gif':
 				return true;
 			break;
 			case 'image/bmp':
 				return true;
 			break;
 			default:
 				return false;
 			break;
 		}
 	}
 	/**
 	 * Método que comprueba el mime original
 	 */
 	public static function tipoFichero($tipoFichero, $mimeinfo){
 		$mimereal = finfo_file($mimeinfo, $tipoFichero["tmp_name"]);
 		if(strpos($mimereal, 'image/jpeg') !== 0 && strpos($mimereal, 'image/png')  !== 0  && strpos($mimereal, 'image/gif') && strpos($mimereal, 'image/bmp') !== 0) {
 			return false;
 		} else{
 			return true;
 		}
 	} // tipoFichero();

		/**
		 * 	Método de validación de tipos
		 * @param  String $tipo Tipo de rol del usuario
		 * @return true | array  resultado()
		 */
		public static function validarTipo($tipo){
			$errores = [];
			if (!isset($tipo) || empty($tipo) || mb_strlen(trim($tipo)) === 0 ) {
				$errores[] = "El tipo está vacío";
			} elseif (mb_strlen(trim($tipo)) < 4) {
				$errores[] = "El tipo es demasiado corto";
			} elseif (mb_strlen(trim($tipo)) > 49) {
				$errores[] = "El tipo es demasiado largo";
			} elseif (!self::regexTipo($tipo, $longitud = 49)) {
				$errores[] = "El tipo no cumple el formato";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarTipo()
		/**
		 * Esta función valida el campo habilitado
		 * @param  boolean $boolean true | false
		 * @return true | arrray  resultado()  Si es valido el campo true sino un array númerico con los errores
		 */
		public static function validarHabilitado($boolean){
			$errores = [];
			// Si no existe el campo
			if (!isset($boolean)) {
				$errores[] = "El campo habilitado, está vacio";
			} elseif(!is_bool($boolean)) {
				// Si no es booleano
				$errores[] = "El campo habilitado no es un booleano";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarHabilitado()

		/**
		 * Método que valida un checkbox
		 */
		public static function validarCheckbox($check){
			$errores = [];
			//Comprobación de checkbox
			if(!isset($check)){
				$errores["terminos"] = "Debe admitir los terminos de la aplicación";
			} elseif ($check != "on") {
				$errores["terminos"] = "Debe admitir los terminos de la aplicación";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// método de validación de checkbox

		/**
		 * validarNombre Valida un nombre de persona
		 * @param  String $nombre nombre de persona, cliente...
		 * @return true | Array         Devuelve el array de errores en caso de existir o true en caso
		 * de que la validación sea correcta
		 */
		public static function validarNombre($nombre, $longitud = 25){
			$errores = [];
			$min = 3;
			if (!isset($nombre) || empty($nombre) || (mb_strlen(trim($nombre)) == 0)) {
				$errores[] = "El nombre está vacio";
			} elseif(mb_strlen(trim($nombre)) < $min ||  mb_strlen(trim($nombre)) > $longitud ){
				$errores[] = "El nombre debe tener entre $min y $longitud caracteres";
			} elseif(!self::regexNombre($nombre)){
				$errores[] = "El nombre no cumple el formato";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarNombre()
		/**
		 * validarNombre Valida un nombre de persona
		 * @param  String $nombre nombre de persona, cliente...
		 * @return true | Array         Devuelve el array de errores en caso de existir o true en caso
		 * de que la validación sea correcta
		 */
		public static function validarApellidos($apellidos, $longitud = 50){
			$errores = [];
			$min = 3;
			if (!isset($apellidos) || empty($apellidos) || mb_strlen(trim($apellidos)) === 0) {
				$errores[] = "Los apellidos está vacio";
			} elseif(mb_strlen(trim($apellidos)) < $min ||  mb_strlen(trim($apellidos)) > $longitud ){
				$errores[] = "Los apellidos deben tener entre $min y $longitud caracteres";
			} elseif(!self::regexNombre($apellidos)){
				$errores[] = "Los apellidos no cumple el formato";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarNombre()

		/**
		 * Método de validación de URL
		 * @param  String $url URL
		 * @return true | false
		 */
		public static function validarUrl($url){
			$errores = [];
			if (!isset($url) || empty($url) || mb_strlen(trim($url)) == 0) {
				$errores[] = "La URL está vacia";
			} elseif (mb_strlen(trim($url)) < 3) { // .es minimo de 3 caracteres
				// Si la longitud de la url es menor de 3 está mal
				$errores[] = "La URL es demasiado corta";
			} elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
				// Si el filtro de PHP no es cumplido da un error
				$errores[] = "La URL no es valida";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarUrl()

		/**
		 * validarFecha, valida la fecha que se le pasa como parametro con el siguiente formato
		 * FORMATO 1900/01/21
		 * @param  $string $fecha fecha separada por barras
		 * @param $String $separador Separador del formato [POR DEFECTO - ]
		 * @return true | array  resultado()  [description]
		 */
		public static function validarFecha($fecha, $separador = "-"){ // FORMATO 1900-0-/21
			$errores = [];
			// Separamos la fecha en un array de 3 posiciones
			$fechas = explode($separador,$fecha);
			// Comprobamos la fecha general
			if (!isset($fecha) || empty($fecha) || mb_strlen(trim($fecha)) == 0) {
				$errores[] = "La fecha está vacia";
			} elseif (mb_strlen(trim($fecha)) > 10) {
				$errores[] = "La fecha no cumple el formato";
			} // validación general
			// Si no hay errores validamos los campos uno a uno, en caso de existir error
			// no se comprueban.
			if (!$errores) {
				// Comprobamos el año
				if (!isset($fechas[0]) || empty($fechas[0])) {
					$errores[] = "El año está vacio";
				} elseif (mb_strlen(trim($fechas[0])) > 4){
					$errores[] = "El año no cumple el formato";
				} else {
					$anio = $fechas[0];
				} // validación del año
				// Comprobamos el mes
				if (!isset($fechas[1]) || empty($fechas[1])) {
					$errores[] = "El mes está vacio";
				} elseif (mb_strlen(trim($fechas[1])) != 2) {
					$errores[] = "El mes no cumple el formato";
				} else {
					$mes = $fechas[1];
				} // Validación del mes
				// Comprobamos el día
	 			if (!isset($fechas[2]) || empty($fechas[2])) {
	 				$errores[] = "El día está vacio";
	 			} elseif (mb_strlen(trim($fechas[2])) != 2) {
					$errores[] = "El día no cumple el formato";
				} else{
					$dia = $fechas[2];
				} // Validación del día
				// Comprobamos la validez del día si no existen erroes antes
				if (!$errores) {
					if (!checkdate($mes, $dia, $anio)) {
						$errores[] = "La fecha no es valida";
					}
				} // validación del día
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarFecha()

		/**
		 * Este método valida el código generado aleatoreamente
		 * @param  String $codigo El código de la promoción
		 * @return true | array  resultao()
		 */
		public static function validarCodigo($codigo){
			$errores = [];
			// Dicionario de palabras validas
			$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_1234567890";
			// Longitud maxima de'los códigos generados puede pasarse
			// a variable de clase para que las dos la obtengan de la clase
			$max_longitud = 15;
			if (!isset($codigo) || empty($codigo) || mb_strlen(trim($codigo)) == 0) {
				$errores[] = "El código está vacio";
			} elseif (mb_strlen(trim($codigo)) > $max_longitud) {
				$errores[] = "El código es demasiado largo";
			} else {
				if(self::comparar($str, $codigo) === true){
					return true;
				} else {
					$errores[] = "El código no es valido";
				}
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarCodigo()

		/**
		 * Método que comprueba si existen una cadena esta compuesta a partir de otra
		 * @param  String $array  String en el que queremos buscar un texto
		 * @param  String $codigo String texto que buscamos
		 * @return Boolean true | false
		 */
		private function comparar($array,$codigo){
			// contador de aciertos
			$cont = 0;
			// longitud de los campos
			$longitud_array  = mb_strlen($array);
			$longitud_codigo  = mb_strlen($codigo);
			// compobamos que existen las 2 variables
			if (isset($array) && isset($codigo)) {
				// recorrecom el dicionario
				for ($i = 0; $i < $longitud_array; $i++) {
					// obtenemos la priemra letra del diccionario
					$letra  = mb_substr($array,$i,1);
					for ($j = 0; $j < $longitud_codigo ; $j++) {
						// obtenemos la primera letra del código
						$cod = mb_substr($codigo,$j,1);
						// comparamos las letras del código con las letras del diccionario
						if (strcmp($cod, $letra) === 0) {
							// si son iguales incrementamos erl contador
							$cont++;
						}
					}
				}
				// Si el contador es igual a la longitud del código devolvemos true
				if ($cont === $longitud_codigo) {
					return true;
				}
				// sino devolvemos false
				return false;
			}
		} // comparar()

		/**
		 * Método que valida un telefono
		 * @param  String $telefono Telefono a validar
		 * @return [type]           [description]
		 */
		public static function validarTelefono($telefono, $longitud = 9){
			$errores = [];
			$longitudMinima = 9;
			if (!isset($telefono) || empty($telefono) || mb_strlen(trim($telefono)) == 0) {
				$errores[] = "El teléfono está vacio";
			} elseif (mb_strlen(trim($telefono)) < $longitudMinima) {
				$errores[] = "El teléfono es demasiado corto";
			} elseif (mb_strlen(trim($telefono)) > $longitud) {
				$errores[] = "El teléfono es demasiado largo";
			} elseif (!self::regexTelefono($telefono)) {
				$errores[] = "El teléfono no cumple el formato, debe empezar por 9, 6, 7 o 8";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarTelefono()

		/**
		 * Método que genera un código aleatorio
		 * Por medidas de seguridad se admite que tengan "-"" y "_"
		 * De forma que no sea simplemente alfanumerico
		 * @return String Cadena aleatoria
		 */
		public static function generarCodigo(){
			$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_1234567890" . date("Yhis");
			$cad = "";
			//										  AÑO	HORA MIN  SEG
			// Montamos una cadena aleatoria con 63 + 0000 + 00 + 00 + 00
			for($i=0;$i<15;$i++) {
				$cad .= mb_substr($str,rand(0,73),1);
			}
			return $cad;
		} // generarCodigo()

		/**
		 * Método que valida un nick
		 * Tiene dependencia del método regexNick que actualmente se encuentra
		 * en la misma clase, pero esto podría cambiar
		 * @param  String $nick Nick que deseamos validar
		 * @return Boolean true | false
		 */
		public static function validarNick($nick){
			$errores = [];
			if(!isset($nick) || empty($nick) || mb_strlen(trim($nick)) == 0){
				$errores[] = "El usuario está vacio";
			} elseif (mb_strlen(trim($nick)) < 4){
				$errores[] = "El usuario es demasiado corto";
			} elseif (mb_strlen(trim($nick)) > 25) {
				$errores[] = "El usuario es demasiado largo";
			}elseif (!self::regexNick($nick)){
				$errores[] = "El usuario no cumple el formato";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarNick()

		public static function validarSalario($salario, $longitud = 50){
			$errores = [];
			if(!isset($salario) || empty($salario) || mb_strlen(trim($salario)) == 0){
				$errores[] = "El salario está vacio";
			} elseif (mb_strlen(trim($salario)) < 4){
				$errores[] = "El salario es demasiado corto";
			} elseif (mb_strlen(trim($salario)) > $longitud) {
				$errores[] = "El salario es demasiado largo";
			}elseif (!self::regexSalario($salario, $longitud)){
				$errores[] = "El salario no cumple el formato";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarSalario()

		/**
		 * Método de validar email
		 * @param  String $email Email a validar
		 * @return Boolean  true | false
		 */
		public static function validarEmail($email){
			$errores = [];
			if (!isset($email) || empty($email)) {
				$errores[] = "El email está vacio";
			} elseif (mb_strlen(trim($email)) == 0){
				$errores[] = "El email está vacio";
			} elseif (mb_strlen(trim($email)) < 7) {
				$errores[] = "El email es demasiado corto";
			} elseif (mb_strlen(trim($email)) > 50) {
				$email = trim($email);
				$errores[] = "El email es demasiado largo";
			} elseif (!self::regexEmail($email)) {
				$errores[] = "El email no es valido";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarEmail()
		/**
		 * Método que valida y compara las contraseñas a la hora de dar de alta
		 * @param  String $pass1 Contraseña 1 para comprobar
		 * @param  String $pass2 Contraseña 2 para comparar
		 * @return Boolean  true | false
		 */
		public static function validarPassAlta($pass1, $pass2)
		{
			$errores = [];
			// PASSWORD 1
			// =======================================================================
			if (!isset($pass1) || empty($pass1) || mb_strlen(trim($pass1)) == 0) {
				$errores["pass"] = "La contraseña está vacía";
			} elseif(mb_strlen(trim($pass1)) < 6) {
				$errores ["pass"] = "La contraseña es demasiado corta";
			} elseif (mb_strlen(trim($pass1)) > 25) {
				$errores ["pass"] = "La contraseña es demasiado larga";
			} elseif(!self::regexMayusculas($pass1) || !self::regexMinusculas($pass1) || !self::regexNumeros($pass1)) {
				$errores["pass"] = "La contraseña no cumple con el formato";
			}

			// PASSWORD 2
			// =======================================================================
			if (!isset($pass2) || empty($pass2)) {
				$errores["pass2"] = "La contraseña repetida está vacía";
			} elseif (mb_strlen(trim($pass2)) == 0) {
				$errores["pass2"] = "La contraseña repetida está vacío";
			} elseif(mb_strlen(trim($pass2)) < 6) {
				$errores ["pass2"] = "La contraseña repetida es demasiado corta";
			} elseif (mb_strlen(trim($pass2)) > 25) {
				$errores ["pass2"] = "La contraseña repetida es demasiado larga";
			} elseif(!self::regexMayusculas($pass2) || !self::regexMinusculas($pass2) || !self::regexNumeros($pass2)) {
				$errores["pass2"] = "La contraseña repetida no cumple con el formato";
			}

			// Comprobación de igualdad
			// =======================================================================
			if (isset($pass1) && isset($pass2)) {
				if ($pass1 != $pass2) {
					$errores["distintas"] = "Las contraseñas son distintas";
				}
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarPassAlta()
		/**
		 * Método de validación de contraseña durante el login
		 * @param  String $pass Contraseña sin cifrar
		 * @return Array | true   Devuelve true si no hay errores y un Array si los hay
		 */
		public static function validarPassLogin($pass, $longitud = 25){
			$errores = [];
			if (!isset($pass) || empty($pass)) {
				$errores[] = "La contraseña está vacía";
			} elseif (mb_strlen(trim($pass)) == 0) {
				$errores[] = "La contraseña está vacía";
			} elseif(mb_strlen(trim($pass)) < 6) {
				$errores [] = "La contraseña es demasiado corta";
			} elseif (mb_strlen(trim($pass)) > $longitud) {
				$errores [] = "La contraseña es demasiado larga";
			} elseif(!self::regexMayusculas($pass) || !self::regexMinusculas($pass) || !self::regexNumeros($pass)) {
				$errores[] = "La contraseña no cumple con el formato";
			}
			// Utilizamos la función que comprueba si el array existe o no
			// Y devuelve true si la validación es correcta y el array de errores
			// en caso de existir
			return self::resultado($errores);
		}// validarPassLogin()

		// =====================================================================================
 		// Funciones de Regex
 		// ====================================================================================
 		/**
 		 * Método que comprueba una expresión regular
 		 * @param  String $cadena nick a comprobar
 		 * @return boolean true | false
 		 */
	 	public static function regexNick($cadena, $longitud = 25){
	 		$pattern = "/^[a-zA-ZáéíóúñÑÁÉÍÓÚàèìòùÀÈÌÒÙÄËÏÖÜäëïöü.\d_-]{4," . $longitud . "}$/i";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexNick()

	 	/**
 		 * Método que comprueba una expresión regular
 		 * @param  String $cadena salario a comprobar
 		 * @return boolean true | false
 		 */
	 	public static function regexSalario($cadena, $longitud = 50){
	 		$pattern = "/^[a-zA-ZáéíóúñÑÁÉÍÓÚàèìòùÀÈÌÒÙÄËÏÖÜäëïöü€$, .\\d_-]{4," . $longitud . "}$/i";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexNick()

	 	/**
 		 * Método que comprueba una expresión regular
 		 * @param  String $cadena Cualquier cadena a comprobar
 		 * @return boolean true | false
 		 */
	 	public static function regexTipo($cadena, $longitud = 25){
	 		$pattern = "/^[a-zA-ZáéíóúñÑÁÉÍÓÚàèìòùÀÈÌÒÙÄËÏÖÜäëïöü .\d_-]{4," . $longitud . "}$/i";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexTipo()

	 	/**
	 	 * Método Creado para la comprobación de si una contraseña contiene un minusculas
	 	 * @param  String $cadena Cadena de texto que queremos comprobar
	 	 * @return Boolean true | false
	 	 */
	 	public static function regexMinusculas($cadena){
	 		$pattern = "`[a-zñáéíóúàèìòùäëïöüñ]`";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexMinuscualas()
	 	/**
	 	 * Método Creado para la comprobación de si una contraseña contiene caracteres especiales
	 	 * @param  String $cadena Cadena a comprobar
	 	 * @return Boolean  true | false
	 	 */
	 	public static function regexEspaciales($cadena){
	 		$pattern = "`[-_!¡@]`";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexEspeciales()
	 	/**
	 	 * MMétodo Creado para la comprobación de si una contraseña contiene un Mayusculas
	 	 * @param  String $cadena Cadena que se desea comprobar
	 	 * @return Boolean  true | false
	 	 */
	 	public static function regexMayusculas($cadena){
	 		$pattern = "`[A-ZÁÉÍÓÚÀÈÌÒÙÄËÏÖÜÑ]`";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// RegexMayusculas
	 	/**
	 	 * Método Creado para la comprobación de si una contraseña contiene un número
	 	 * @param  String $cadena variable a comprobar
	 	 * @return Boolean  true | false
	 	 */
	 	public static function regexNumeros($cadena){
	 		$pattern = "`[0-9]`";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexNumeros()
	 	/**
	 	 * Método que comprueba números enteros
	 	 * @param  String  $cadena   Cadena (porque tiene que venir con "") que deseamos comprobar
	 	 * @param  integer $longitud longitud de la cadena a validar como maximo
	 	 * @return Boolean true | false
	 	 */
	 	public static function regexEnteros($cadena, $longitud = 11){
	 		// EL DOLAR ES MUY IMPORTANTE, sin el dolar no funciona
	 		$pattern = "/^[0-9]{1," . $longitud . "}$/";
	 		if (preg_match($pattern, $cadena)){
	 			return true;
	 		}
	 		return false;
	 	}// regexEnteros()

	 	/**
	 	 * Método que valide una cadena contra una expresión regular
	 	 * @param  String $cadena Variable a comprobar
	 	 * @return Boolean  true | false
	 	 */
	 	public static function regexNombre($cadena){
	 		$pattern = "/^[A-Za-zñÑáéíóúÁÉÍÓÚÄËÏÖÜäëïöüàèìòùÀÈÌÔÙ  -_]{3,}$/";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexNombre()
	 	/**
	 	 * Método que valida el email empleando un filter_var
	 	 * @param  String $email Email que se desea validar
	 	 * @return boolean true | false
	 	 */
	 	public static function regexEmail($email){
	 		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexNombre()

	 	/**
	 	 * Método que valide una cadena contra una expresión regular
	 	 * @param  String $cadena Variable a comprobar
	 	 * @return Boolean  true | false
	 	 */
	 	public static function regexDireccion($cadena, $longitud = 100){
	 		$pattern = "/^[A-Za-zñÑáéíóúÁÉÍÓÚàèìòùÀÈÌÔÙçÇ 0-9,.:;ºª -_ #\\/]{3," . $longitud . "}$/";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}// regexDireccion()

	 	/**
	 	 * Método que valida un telefono
	 	 * @param  String $cadena Cadena a validar
	 	 * @return Boolean
	 	 */
	 	public static function regexTelefono($cadena){
	 		$pattern = "/^((\\+?34([ \\t|\\-])?)?[9|6|7|8]((\\d{1}([ \\t|\\-])?[0-9]{3})|(\\d{2}([ \\t|\\-])?[0-9]{2}))([ \\t|\\-])?[0-9]{2}([ \\t|\\-])?[0-9]{2})$/";
	 		if (preg_match($pattern, $cadena)) {
	 			return true;
	 		}
	 		return false;
	 	}
	 /**
	 * Funcion que valida cualquier identificador.
	 * @param  int 			 $id 			Identificador.
	 * @return boolean/array true/$errores 	Devuelve que la validacion ha sido correcta/Errores producidos durante la validacion.
	 */
	public static function validarId($id = null) {
	// Valor por defecto null, por si olvidamos pasar el parámetro, se muestre un error.
		$errores=[]; // Array para almacenar los errores.
		// Comprobamos que el id existe y no es nulo.
		if(!isset($id) || empty($id) || mb_strlen(trim($id)) === 0){
			$errores[] = "El campo id está vacío";
		}
		// Comprobamos si el id es un numero y ademas es entero.
		elseif(!self::regexEnteros($id)){
			$errores[] = "El campo id debe ser un número entero.";
		}
		// Comprobamos si el id tiene la longitud correcta.
		elseif(mb_strlen(trim($id)) > 11 || mb_strlen(trim($id)) < 1){
			$errores[]="El campo id no tiene la longitud permitida.";
		}
		return self::resultado($errores);
	}// validarId()

	/**
	 * Funcion que valida cualquier numero decimal que se le pase.
	 * @param  int/float $numero      	Numero a comprobar.
	 * @param  int 		 $parte_entera  Longitud de la parte entera del numero. Por defecto es 4.
	 * @return boolean               	Devuelve si es correcto(true)/Incorrecto(false).
	 */
	public static function regexDecimales($numero, $parte_entera=4) {
		$pattern = "/^[0-9]{1," . $parte_entera . "}+(\.[0-9]{1,2})?$/";
		if(preg_match($pattern, $numero)){
			return true;
		}
		return false;
	}// regexDecimales()

	/**
	 * Funcion que valida un precio por hora de la categoria de usuario, el precio de un servicio y el precio del presupuesto.
	 * @param  int/float 	 $decimal 		Precio total de un trabajo, presupuesto o servicio.
	 * @param  int 		 	 $parte_entera  Longitud de la parte entera que debe tener el numero decimal.
	 * @return boolean/array true/$errores  Devuelve que la validacion ha sido correcta/Errores producidos durante la validacion.
	 */
	public static function validarDecimales($decimal, $parte_entera=4){
		$errores=[]; // Array para almacenar los errores.
		// Comprobamos si existe el campo pasado como parámetro.
		if(!isset($decimal) || empty($decimal) || mb_strlen(trim($decimal)) === 0){
			$errores[] = "El campo está vacío.";
		}
		// Comprobamos si el decimal es un numero y ademas es decimal o entero.
		if(!is_numeric($decimal) || (!is_float($decimal) && !is_int($decimal))){
			$errores[]="Este campo debe ser un número entero o decimal.";
		}
		// Comprobamos si el decimal es menor o igual que cero.
		if($decimal <= 0){
			$errores[]="Este campo no puede ser menor o igual a cero.";
		}
		// Comprobamos si el decimal tiene la longitud correcta y se adapta al patron.
		if(mb_strlen($decimal) > ($parte_entera+3) || mb_strlen($decimal) < 1 || !regexDecimales($decimal, $parte_entera)){
			$errores[]="Este campo debe tener una longitud máxima de " . $parte_entera . " cifras enteras y 2 decimales separadas por un punto.";
		}
		return self::resultado($errores);
	}// validarDecimales()

	//Sanea la entrada
	/**
	 * Método que sanea las entradas de un array
	 * @param  Array $array Array de datos a ser saneados
	 * @return Array        Datos saneados
	 */
	public static function sanearEntrada($array){
		foreach ($array as $clave => $valor) {
			$array[$clave] = self::saneamiento($valor);
		}
		return $array;
	}

	public static function validarNif($nif){
		$cadena = strtoupper($cadena);
		//$letra = substr($cadena, -1, 1); donde empieza el substring y cuanto a de coger
		$letra = substr($cadena, -1, 1);
		//numero de digitos que tendrá el DNI o NIE
		$numero = substr($cadena, 0, 8);

		// Si es un NIE hay que cambiar la primera letra por 0, 1 ó 2 dependiendo de si es X, Y o Z.
		$numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);

	 	//calculo de la letra mediante la formula del modulo 23
		$modulo = $numero % 23;
		$letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
		$letra_correcta = substr($letras_validas, $modulo, 1);

	 	//Si la letra no es correcta da un error
		if($letra_correcta != $letra) {
			return false;
		}
		return true;
	}// validarNif()

	/**
	 * Método que valida las direcciónes
	 * @param  String  $direccion Dirección a comporbar
	 * @param  integer $longitud  Longitud maxima
	 * @return Array | true    true = Si hay no hay errores Array si lo que devuelve son errores
	 */
	public static function validarDireccion($direccion, $longitud = 100){
		$errores = [];
		if (!isset($direccion) || empty($direccion) || mb_strlen(trim($direccion)) === 0) {
			$errores[] = "La direccion está vacia";
		} elseif (mb_strlen(trim($direccion)) <= 4) {
			$errores[] = "La direccion es demasiado corta";
		} elseif (mb_strlen(trim($direccion)) >= $longitud) {
			$errores[] = "La direccion es demasiado larga";
		} elseif (!self::regexDireccion($direccion, $longitud)) {
			$errores[] = "La dirección no cumple el formato";
		}
		// Utilizamos la función que comprueba si el array existe o no
		// Y devuelve true si la validación es correcta y el array de errores
		// en caso de existir
		return self::resultado($errores);
	}// validarDireccion()

	/**
	 * Método que valida las direcciónes
	 * @param  String  $descripcion Descripción a comporbar
	 * @param  integer $longitud  Longitud maxima de la dirección
	 * @return Array | true    true = Si hay no hay errores Array si lo que devuelve son errores
	 */
	public static function validarDescripcion($descripcion, $longitud = 100){
		$errores = [];
		if (!isset($descripcion) || empty($descripcion) || mb_strlen(trim($descripcion)) === 0) {
			$errores[] = "La descripcion está vacia";
		} elseif (mb_strlen(trim($descripcion)) <= 4) {
			$errores[] = "La descripcion es demasiado corta";
		} elseif (mb_strlen(trim($descripcion)) >= $longitud) {
			$errores[] = "La descripcion es demasiado larga";
		} elseif (!self::regexDireccion($descripcion, $longitud)) {
			$errores[] = "La descripcion no cumple el formato";
		}
		// Utilizamos la función que comprueba si el array existe o no
		// Y devuelve true si la validación es correcta y el array de errores
		// en caso de existir
		return self::resultado($errores);
	}// validarDescripcion()

	/**
	 * Método que valida las direcciónes
	 * @param  String  $requisitos Requisitos a comporbar
	 * @param  integer $longitud  Longitud maxima
	 * @return Array | true    true = Si hay no hay errores Array si lo que devuelve son errores
	 */
	public static function validarRequisitos($requisitos, $longitud = 100){
		$errores = [];
		if (!isset($requisitos) || empty($requisitos) || mb_strlen(trim($requisitos)) === 0) {
			$errores[] = "Los requisitos están vacios";
		} elseif (mb_strlen(trim($requisitos)) <= 4) {
			$errores[] = "Los requisitos son demasiado cortos";
		} elseif (mb_strlen(trim($requisitos)) >= $longitud) {
			$errores[] = "Los requisitos son demasiado largos";
		} elseif (!self::regexDireccion($requisitos, $longitud)) {
			$errores[] = "Los requisitos no cumplen el formato";
		}
		// Utilizamos la función que comprueba si el array existe o no
		// Y devuelve true si la validación es correcta y el array de errores
		// en caso de existir
		return self::resultado($errores);
	}// validarRequisitos()

	/**
	 * Método que comprueba la ordenación de la busqueda
	 * @param  String $tipo Campo seleccionado
	 * @return Boolean       [description]
	 */
	public static function compruebaOrdenBusqueda($tipo){
		switch ($tipo) {
			case 'nombre':
				return 'order by nombre asc';
				break;
			case 'nuevo':
				return 'order by fech_al desc';
				break;
			case 'viejo':
				return 'order by fech_al asc';
				break;
			default:
				return false;
			break;
		}
	}// compruebaOrdenBusqueda()

	/**
	 * Método de que comprueba el tipo de Busqueda
	 * @param  String $tipo Tipo de busqueda
	 * @return Boolean   true = si es valido, false = sino es valido
	 */
	public static function compruebaTipoBusqueda($tipo){
		switch ($tipo) {
			case 'nombre':
				return "contacto.nombre";
				break;
			case 'categoria':
				return "categoria.nombre_cat";
				break;
			case 'direccion':
				return "direccion";
				break;
			default:
				return false;
			break;
		}
	}// compruebaTipoBusqueda()

	/**
	 * Método que valida el tipo de busqueda que se hace en el buscador
	 * @param  String $busqueda Tipo de busqueda a realizar
	 * @return Array | true    true = Si hay no hay errores Array si lo que devuelve son errores
	 */
	public static function validarTipoBusqueda($busqueda)
	{
		$errores = [];
		// tipo de busqueda
		if (!isset($busqueda) || empty($busqueda)) {
			$errores["busqueda"] = "Selecione un campo";
		} elseif (!self::compruebaTipoBusqueda($busqueda)) {
			$errores["busqueda"] = "Selecione un campo";
		}
		return self::resultado($errores);
	}// validarTipoBusqueda()

	/**
	 * Método de validar el tipo de ordenación
	 * @param  String $ordenacion Tipo de ordenación
	 * @return Array | true    true = Si hay no hay errores Array si lo que devuelve son errores
	 */
	public static function validarTipoOrdenacion($ordenacion)
	{
		$errores = [];
		//Orden de la busqueda
		if (!isset($ordenacion) || empty($ordenacion)) {
			$errores["orden"] = "Selecione un campo";
		} elseif (!self::compruebaOrdenBusqueda($ordenacion)) {
			$errores["orden"] = "Selecione un campo";
		}
		return self::resultado($errores);
	}// validar TipoOrdenacion()

	/**
	 * Método de validación del buscador
	 * @param  String $campo Cadena de texto a buscar
	 * @return Array | true    true = Si hay no hay errores Array si lo que devuelve son errores
	 */
	public static function validarCampoBusqueda($campo)
	{
		$errores = [];
		// Validar buscador
		if (!isset($campo) || empty($campo)) {
			$errores["cont-busqueda"] = "El campo de busqueda está vacio";
		}
		return self::resultado($errores);
	}//valiarCampoBusqueda()

	/**
	 * Método utilizado para sanear campos como el buscador
	 * @param  String $texto Cadena a sanear
	 * @return String  Cadena ya saneada
	 */
	public static function limpiarString($texto)
	{
      $textoLimpio = preg_replace('([^A-Za-zñÑáéíóúÁÉÍÓÚÄËÏÖÜäëïöüàèìòùÀÈÌÔÙ 0-9])', '', $texto);
      return $textoLimpio;
	}
	public static function limpiarTextarea($texto)
	{
		$textoLimpio = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $texto);
		return $textoLimpio;
	}


}// Fin de clase
?>