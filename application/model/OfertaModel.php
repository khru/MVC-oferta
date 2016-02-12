<?php
	class OfertaModel
	{
	// =============================================================
	// Métodos de acceso a la base de datos
	// =============================================================
		/**
		 * Método que obtiene un registro por ID
		 * @param  Integer $id ID de la oferta
		 * @return Boolean     true
		 */
		public static function getId($id)
		{
			$ssql = 'SELECT * FROM oferta WHERE id = :id';
			$params = [':id'=> $id];
			return Database::consulta($ssql, $params, $estado = 0);
		}// getId()

		public static function comprobarPropiedadOferta($id)
		{
			$ssql = 	'SELECT *
						FROM oferta, empresa, usuario
						WHERE oferta.empresa = empresa.id
						AND empresa.usuario = usuario.id
						AND usuario.id = :usuario
						AND oferta.id = :id';
			$usuario = Session::get('user_id');
			$params = [	':usuario' => $usuario, ':id' => $id];
			return Database::consulta($ssql, $params, $estado = 2);
		}

		/**
		 * Método de obtención de todas las ofertas
		 * @return Array | false Array = si existen datos, False = sino hay datos
		 */
	    public static function todas()
	    {
	        $ssql = 'SELECT oferta.id as id, oferta.nombre as nombre, oferta.descripcion as descripcion, requisitos, url, salario, empresa.nombre as empresa, fecha_alta as fecha FROM empresa, oferta WHERE empresa.usuario = :id AND oferta.empresa = empresa.id';
	        $id = Session::get('user_id');
	        $params = [':id' => $id];
	        return Database::consulta($ssql, $params, $estado = 1);
	    }// todas()

	    /**
	     * Método de obtención de nombre
	     * @param  String $nombre Nombre por el cual se quiere obtener la oferta
	     * @return Array          Cam
	     */
	    public static function getNombre($nombre)
	    {
	    	$ssql = 'SELECT oferta.nombre FROM oferta,empresa WHERE oferta.empresa = empresa.id AND oferta.nombre = :nombre';
        	$params = [':nombre' => $nombre];
	        return Database::consulta($ssql, $params, $estado = 1);
	    }// getNombre()

	    public static function getNombreEmpresa()
	    {
			$ssql = 'SELECT empresa.nombre as empresa FROM empresa, usuario WHERE empresa.usuario = usuario.id AND usuario.id = :id ';
			$id = Session::get('user_id');
			$params = [':id' => $id];
	   		return Database::consulta($ssql, $params, $estado = 1);
	    }// getNombreEmpresa()

	    public static function getIdEmpresaByNombre($nombre)
	    {
			$ssql = 'SELECT id FROM empresa WHERE nombre = :nombre';
			$params = [':nombre' => $nombre];
			return Database::consulta($ssql, $params, $estado = 0);
	    }// getIdEmpresaByNombre()

	    public static function getOfertaById($id)
	    {
	    	$ssql = 'SELECT oferta.nombre FROM oferta,empresa,usuario WHERE oferta.empresa = empresa.id AND usuario.id = empresa.usuario AND oferta.id = :id AND usuario.id = :usuario';
	    	$usuario = Session::get('user_id');
	    	$params = [':id' => $id, ':usuario' => $usuario];
	    	return Database::consulta($ssql, $params, $estado = 2);
	    }// getOfertaById()

	    public static function getNombreNoRepetido($id)
	    {
	    	$ssql = "SELECT oferta.nombre FROM oferta, empresa, usuario WHERE oferta.empresa = empresa.id AND empresa.usuario = usuario.id AND usuario.id = :usuario AND oferta.nombre NOT IN (SELECT nombre FROM oferta WHERE id = :id)";
			$usuario = Session::get('user_id');
			$params = [ 'usuario' 	=> $usuario,
						'id' 		=> $id
						];
			return Database::consulta($ssql, $params, $estado = 1);
	    }// getNombreNoRepetido()

	// =========================================================================
	// Métodos encargados de gestionar la lógica pedida por el controlador
	// ==========================================================================

	    /**
	     * Método de alta de ofertas
	     * @param  Array $array Datos a insertar
	     * @return Boolean      True = si se insertan, False = sino se inserta
	     */
	    public static function alta($array)
	    {
	    	if (!$array) {
	    		// generamos el error
	    		Session::add('feedback_negative', 'No se han recibido datos');
	    		return false;
	    	}
    		// hacemos las validaciones
	    	if(OfertaModel::validar($array)){
	    		// Saneamos el array
	    		$array = Validaciones::sanearEntrada($array);
	    		// Procedemos a la inserción de la empresa en la base de datos
	    		// Para ello preestablecemos el array que queremos insertar
	    		if (!Session::get('user_id')) {
	    			Session::add('feedback_negative', 'No tiene iniciada sesión, por lo tanto no podemos crear la oferta');
	    			return false;
	    		}
	    		$fecha_alta = date('Y-m-d h:i:s');
	    		$empresa = Session::get('empresa');
	    		$empresa = $empresa['id'];
	    		$datos = [	':nombre' 		=> $array['nombre'],
	    					':descripcion'  => $array['descripcion'],
	    					':requisitos'  	=> $array['requisitos'],
	    					':url' 			=> $array['url'],
	    					':salario' 		=> $array['salario'],
	    					':empresa'		=> $empresa,
	    					':fecha_alta'	=> $fecha_alta
	    		];
	    		Session::delete('empresa');
	    		// devolvemos lo que la inserción nos dice
	    		return OfertaModel::insert($datos);
	    		// procedemos a la inserción de los datos en la base de datos,
	    		// para ello tenemos un método llamado insert

	    	} else {
	    		return false;
	    	}
	    }// alta()

	    /**
	     * Método de edición de ofertas
	     * @param  Integer $id    ID de la oferta a modificar
	     * @param  Array $array   Datos a editar
	     * @return Boolean        True = si se realiza la edición, False = sino se realiza
	     */
	    public static function editar($id, $array)
	    {

	    	if (!$array) {
	    		Session::add('feedback_negative', 'No se han recicibido datos');
	    		return false;
	    	}
	    	// hacemos las validaciones
	    	if ($ofr = OfertaModel::getId($id)) {
	    		if ($ofr) {
	    			$array['id'] = $ofr['id'];
	    		}
	    		if(OfertaModel::validar($array)){
		    		// Saneamos el array
		    		$array = Validaciones::sanearEntrada($array);
		    		// Procedemos a la inserción de la empresa en la base de datos
		    		// Para ello preestablecemos el array que queremos insertar
		    		if (!Session::get('user_id')) {
		    			Session::add('feedback_negative', 'No tiene iniciada sesión, por lo tanto no podemos crear la oferta');
		    			return false;
		    		}
		    		$empresa = Session::get('empresa');
	    			$empresa = $empresa['id'];
	    			$datos = [	':nombre' 		=> $array['nombre'],
	    						':descripcion'  => $array['descripcion'],
	    						':requisitos'  	=> $array['requisitos'],
	    						':url' 			=> $array['url'],
	    						':salario' 		=> $array['salario'],
	    						':empresa'		=> $empresa,
	    						':id'			=> $id
	    		];
	    		Session::delete('empresa');
		    		// devolvemos lo que la inserción nos dice
		    		//d($datos);die;
		    		return OfertaModel::edit($datos);

		    	} else {
		    		// Como ya existen los errores en Session
		    		// simplemente los devolvemos
		    		return false;
		    	}
	    	} else {
	    		Session::add('feedback_negative', 'No se ha modificado la empresa');
		    	return false;
	    	}
	    }//editar()


	    /**
	     * Método de busqueda
	     * @param  Array $array 	Datos a buscar
	     * @return Array | false    Array con los resultados o false cuando hay errores
	     */
	   	public static function buscar($array)
	   	{
	   		// comprobamos si el array que nos e
	   		if (!$array) {
	   			Session::add('feedback_negative', 'No se han recicibido datos');
	    		return false;
	   		} else {
	   			// Existen datos hay que validarlo
	   			if (isset($array['busqueda'])) {
	   				if (empty(isset($array['busqueda'])) || mb_strlen(trim($array['busqueda'])) === 0) {
	   					Session::add('feedback_negative', 'No se han recicibido datos a buscar');
	   				} else {
	   					// saneamos la busqueda
	   					$busqueda = Validaciones::limpiarString($array['busqueda']);
	   					$busqueda = '%' . $busqueda . '%';
	   					// lanzo la consulta a la base de datos
	   					$usuario = (int) Session::get('user_id');
	   					$params = [':busqueda' => $busqueda, ':usuario' => $usuario];
	   					$resultado =  OfertaModel::search($params);
	   					if (!$resultado) {
	   						Session::add('feedback_negative', 'No se han encontrado resultados');
	   					}
	   					return $resultado;
	   				}

				return Session::comprobarErrores();

	   			} else {
	   				// No existe la busqueda
	   				Session::add('feedback_negative', 'No se han recicibido datos a buscar');
	    			return false;
	   			}
	   		}
	   	}// buscar()

	// ====================================================================
	// Métodos que ejecutan las modificaciones en la base de datos
	// ====================================================================

	    /**
	     * Método de inserción de empresas
	     * @param  Array $array Datos a insertar en la tabla
	     * @return Boolean      True = Si se insertan bien
	     *                      False = Si no se insertan bien
	     */
	    public static function insert($array)
	    {
	    	//$conn = Database::getInstance()->getDatabase();
	   		$ssql = 'INSERT INTO oferta (nombre, descripcion, requisitos, url, salario,empresa, fecha_alta) VALUES (UPPER(:nombre),  :descripcion, :requisitos, LOWER(:url), :salario, :empresa, :fecha_alta)';
			return Database::consulta($ssql, $array, $estado = 3);
	   	}// insert()

	   	/**
	   	 * Método de borrado de de ofertas de la base de datos
	   	 * @param  integer $id ID de la oferta a borrar
	   	 * @return Boolean     True = si se borra, False = sino se borra
	   	 */
	   	public static function delete($id = 0)
	   	{
	   		$id = (int) $id;
	   		$id = Validaciones::saneamiento($id);
	   		// comprobamos que existe dicha oferta y que es del usuario
	   		if (OfertaModel::getOfertaById($id)) {
	   			$ssql = 'DELETE FROM oferta WHERE id = :id';
	   			$params = [':id' => $id];
	   			if (Database::consulta($ssql, $params, $estado = 2)) {
	   				Session::add('feedback_positive', 'La oferta ha sido satisfactoriamente borrada');
	   				return true;
	   			}
	   			Session::add('feedback_negative', 'La oferta no ha sido borrada');
	   			return false;
	   		} else {
	   			// Si intenta borrar una oferta que no es suya o que no existe
	   			Session::add('feedback_negative', 'La oferta no ha sido borrada');
	   			return false;
	   		}
	   	}// delete()

	   	public static function edit($params)
	   	{
	   		$ssql = 'UPDATE oferta SET nombre = UPPER(:nombre), descripcion = LOWER(:descripcion), requisitos = :requisitos, url = :url, salario = :salario, empresa = :empresa WHERE id = :id';
	   		return Database::consulta($ssql, $params, $estado = 3);
	   	}// edit()

	   	/**
	   	 * Método que ejecuta la busqueda y la devuelve
	   	 * @param  Array $params  Datos que entran se estan buscando
	   	 * @return Array         [description]
	   	 */
	   	public static function search($params)
	   	{
	   		$ssql = 'SELECT oferta.id as id, oferta.nombre as nombre, oferta.descripcion as descripcion, oferta.requisitos as requisitos, url, salario, empresa.nombre as empresa, fecha_alta as fecha FROM oferta, empresa, usuario WHERE oferta.empresa = empresa.id AND empresa.usuario = usuario.id AND usuario.id = :usuario AND (oferta.nombre LIKE :busqueda OR oferta.descripcion LIKE :busqueda OR oferta.requisitos LIKE :busqueda OR url LIKE :busqueda OR salario LIKE :busqueda OR empresa.nombre LIKE :busqueda OR fecha_alta LIKE :busqueda)';
	   		//d($ssql);die();
	   		return Database::consulta($ssql, $params, $estado = 1);
	   	}// search()+

	// =================================================================
	// Método de Validación que realiza las validaciones pertinentes
	// =================================================================

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
	    		if (($erro = Validaciones::validarNombre($array["nombre"] , 50)) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        } else {
		        	if (isset($array['id'])) {
		        		// cuando estamos editando
		        		$nombres = OfertaModel::getNombreNoRepetido($array['id']);
		        		if (!OfertaModel::compararNombre($nombres, $array['nombre'])) {
		        			Session::add('feedback_negative', 'La oferta ya exite');
		        		}
		        	} else {
		        		// cuando estamos insertando
		        		if (OfertaModel::getNombre($array['nombre'])) {
			        		Session::add('feedback_negative', 'El nombre ya existe');
			        	}
		        	}
		        }
	    	} else {
	    		Session::add('feedback_negative', 'El nombre no ha sido recicibido');
	    	}// fin de las validaciones del nombre

			// Validación del descripcion
	    	if (isset($array['descripcion'])) {
	    		$array['descripcion'] = Validaciones::limpiarTextarea($array['descripcion']);
	    		if (($erro = Validaciones::validarDescripcion($array["descripcion"], 1000)) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'La descripcion no ha sido recicibida');
	    	}// Fin de la validación del descripcion

	    	// Validación de los requisitos
	    	if (isset($array['requisitos'])) {
	    		$array['requisitos'] = Validaciones::limpiarTextarea($array['requisitos']);
	    		if (($erro = Validaciones::validarRequisitos($array["requisitos"], 1000)) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'Los requisitos no han sido recicibidos');
	    	}// Fin de la validación de los requisitos

	    	// Validación de la url
	    	if (isset($array['url'])) {
	    		if (($erro = Validaciones::validarUrl($array["url"])) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'La url no ha sido recicibida');
	    	}// fin de las validaciones de la url

	    	// validamos el salario
	    	if (isset($array['salario'])) {
	    		if (($erro = Validaciones::validarSalario($array["salario"])) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'El salario no ha sido recibido');
	    	}

	    	// validamos la empresa
	    	if (isset($array['empresa']) || !empty($array['empresa'])) {
	    		$array['empresa'] = Validaciones::saneamiento($array['empresa']);
	    		if (!OfertaModel::getIdEmpresaByNombre($array['empresa'])) {
	    			Session::add('feedback_negative', 'La empresa no existe');
	    		} else {
	    			$empresa = OfertaModel::getIdEmpresaByNombre($array['empresa']);
	    			Session::set('empresa', $empresa);
	    			Session::set('selected', $array['empresa']);
	    		}
	    	} else {
	    		Session::add('feedback_negative', 'La empresa no ha sido seleccionada, o quizás no tenga ninguna');
	    	}

	    	// Comprobación de de que no haya habido errores
	    	return Session::comprobarErrores();

	    }//validar()

	// =========================================================
	// El método compararNombre se repite en los modelos
	// En el futuro se bebería crear la clase Model
	// La cual debería tener un minimo de 2 atributos
	// 		- validacions
	// 		- database
	// 	Y contener métodos como el de compararNombre
	// ==========================================================

	    /**
	     * Método que comprueba si el nombre de una empresa es igual que el que se intenta
	     * insertar
	     * @param  Array $array  	Empresas
	     * @param  String $nombre 	Nombre de la empresa que se intenta comprobar
	     * @return Boolean        	True = Sino existe False= si existe
	     */
	    public static function compararNombre($array, $nombre)
	    {
	    	if (is_array($array)) {
	    		foreach ($array as $key => $value) {
	    			if ($value['nombre'] == $nombre) {
	    				return  false;
	    			}
	    		}
	    		return true;
	    	}
	    }
	}// fin de la clase
?>