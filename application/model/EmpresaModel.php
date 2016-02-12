<?php
	class EmpresaModel
	{

	// =============================================================
	// Métodos de acceso a la base de datos
	// =============================================================

		/**
		 * Método hecho exclusivamente para que al editar se pueda comprobar
		 * que el nombre de la oferta que estas intentando actualizar, no
		 * pertenece ya a otra oferta
		 * @param  Integer $id ID de la empresa
		 * @return Boolean     True = existe el nombre, False = no existe el nombre
		 */
		public static function comprobarPropiedadEmpresa($id)
		{
			$ssql = 	'SELECT *
						FROM empresa, usuario
						WHERE empresa.usuario = usuario.id
						AND usuario.id = :usuario
						AND empresa.id = :id';
			$usuario = Session::get('user_id');
			$params = [	':usuario' => $usuario, ':id' => $id];
			return Database::consulta($ssql, $params, $estado = 2);
		}// comprobarPropiedadEmpresa()

		/**
		 * Método que comprueba que una empresa existe a partir de un id
		 * @param  integer $id ID a comprobar
		 * @return Array
		 */
		public static function getId($id = 0)
		{
			$ssql = 'SELECT * FROM empresa WHERE id = :id AND usuario = :usuario';
			$usuario = Session::get('user_id');
			$params = [':id' => $id, ':usuario' => $usuario];
			// 0 fetch
			return Database::consulta($ssql, $params, $estado = 0);
		}// getId()

		/**
		 * Método que devuelve todas las empresas salvo la mia
		 * @param  Integet $id 	ID a obviar
		 * @return Array     	Todas las empresas menos la que edito
		 */
		public static function getNombreNoRepetido($id)
		{
			$ssql = "SELECT empresa.nombre FROM empresa, usuario WHERE empresa.usuario = usuario.id AND usuario.id = :usuario AND empresa.nombre NOT IN (SELECT nombre FROM empresa WHERE id = :id)";
			$usuario = Session::get('user_id');
			$params = [ 'usuario' 	=> $usuario,
						'id' 		=> $id
						];
			return Database::consulta($ssql, $params, $estado = 1);
		}// getNombreNoRepetido

		/**
		 * Método utilizado para borrar las ofertas de una empresa en caso de existir
		 * @param  Integer $empresa ID de la empresa a la cual queremos borrarle las ofertas
		 * @return Boolean          True = si las ofertas han sido eliminadas
		 *                          False = si no ha sido así
		 */
		public static function deleteAllOfertasByEmpresa($empresa)
	   	{
	    	$ssql = 'DELETE FROM oferta WHERE empresa = :empresa';
	    	$params = [':empresa'=> $empresa];
	        return Database::consulta($ssql, $params, $estado = 2);
	   	}// getAllOfertaByEmpresa

	   	/**
	   	 * Método que comprueba si existen ofertas para una empresa.
	   	 * @param  Integer $empresa ID de la empresa a comprobar
	   	 * @return Boolean          True = si hay
	   	 */
	   	public static function comprobarOfertas($empresa)
	   	{
	   		$ssql = 'SELECT * FROM oferta WHERE empresa = :empresa';
	    	$params = [':empresa'=> $empresa];
	        return Database::consulta($ssql, $params, $estado = 2);
	   	}//comprobarOfertas()

		/**
		 * Método que comprueba que el nombre de una empresa es UNIQUE
		 * @return Boolean True = si existe, False = sino existe
		 */
		public static function getNombre($nombre)
		{
			$ssql = 'SELECT * FROM empresa WHERE nombre = :nombre';
			$params = [':nombre'=> $nombre];
			// 2 = Comprobación de filas afectadas
	   		return Database::consulta($ssql, $params, $estado = 2);
		}// getNombre()

		/**
		 * Método de recuperación de todas las empresas
		 * @return Array | Boolean Devuelve el array si la consulta se ejecuta, sino devuelve false
		 */
	    public static function todas()
	    {
	        $ssql = 'SELECT * FROM empresa WHERE usuario = :id';
	        $id = Session::get('user_id');
	        $params = [':id' => $id];
	        // 1 = fetchAll
	        return Database::consulta($ssql, $params, $estado = 1);
	    }// todas()

	// =========================================================================
	// Métodos encargados de gestionar la lógica pedida por el controlador
	// ==========================================================================

	    /**
	     * Método que realiza la lógica del alta de las empresas.
	     * @return Boolean 		 True = Si se ha creado la empresa correctamente
	     *                       False = Cuando ha habido un error
	     */
	    public static function alta($array)
	    {
	    	if (!$array) {
	    		Session::add('feedback_negative', 'No se han recicibido datos');
	    		return false;
	    	}
    		// hacemos las validaciones
	    	if(EmpresaModel::validar($array)){
	    		// Saneamos el array
	    		$array = Validaciones::sanearEntrada($array);
	    		// Procedemos a la inserción de la empresa en la base de datos
	    		// Para ello preestablecemos el array que queremos insertar
	    		if (!Session::get('user_id')) {
	    			Session::add('feedback_negative', 'No tiene iniciada sesión, por lo tanto no podemos crear la empresa');
	    			return false;
	    		}
	    		$id = Session::get('user_id');
	    		$datos = [	':nombre' 		=> $array['nombre'],
	    					':web' 			=> $array['web'],
	    					':descripcion'  => $array['descripcion'],
	    					':usuario'		=> $id
	    		];
	    		// devolvemos lo que la inserción nos dice
	    		return EmpresaModel::insert($datos);

	    	} else {
	    		// Como ya existen los errores en Session
	    		// simplemente los devolvemos
	    		return false;
	    	}

	    }// alta()

	    /**
	     * Método que borra una empresa
	     * @param  integer $id Empresa a borrar
	     * @return boolean     True = si se borra, False = sino se borra
	     */
	    public static function borrar($id = 0)
	    {
	    	// casteamos el id
	    	$id = (int) $id;
	    	// Comprobar el que la empresa existe y es de dicho usuario
	    	if (EmpresaModel::getId($id)) {
	    		$conn = Database::getInstance()->getDatabase();
	    		$conn->beginTransaction();
	    		$estado = true;
	    		// comprobamos si existen ofetas para esa empresa
	    		if (EmpresaModel::comprobarOfertas($id)) {
	    			// borrar ofertas de esa empresa
		    		if (!EmpresaModel::deleteAllOfertasByEmpresa($id)) {
		    			$estado = false;
		    		}else {
		    			Session::add('feedback_positive', 'Las ofertas de la empresa han sido borradas');
		    		}
	    		}

	    		// borramos la empresa
	    		if (!EmpresaModel::delete($id)) {
	    			$estado = false;
	    		}
	    		// comprobamos el estado de la transacción
	    		if ($estado) {
	    			$conn->commit();
	    			Session::add('feedback_positive', 'La empresa ha sido borrada');
	    			return $estado;
	    		}

    			$conn->rollback();
    			Session::add('feedback_negative', 'La empresa no ha sido borrada');
    			return $estado;


	    	} else {
	    		// si la empresa no existe
	    		Session::add('feedback_negative', 'La empresa no ha sido borrada');
	    		return flase;
	    	}

	    }// borrar()

	    /**
	     * Método que edita una empresas
	     * @param  Integer $id    	ID de la empresa a editar
	     * @param  Array $array 	Datos a actualizar
	     * @return Boolean        	True = cuando el registro se edita
	     *                          False = cuando el registro no se edita
	     */
	    public static function editar($id, $array)
	    {
	    	if (!$array) {
	    		Session::add('feedback_negative', 'No se han recicibido datos');
	    		return false;
	    	}
	    	// hacemos las validaciones
	    	if ($emp = EmpresaModel::getId($id)) {
	    		if ($emp) {
	    			$array['id'] = $emp['id'];
	    		}
	    		if(EmpresaModel::validar($array)){
		    		// Saneamos el array
		    		$array = Validaciones::sanearEntrada($array);
		    		// Procedemos a la inserción de la empresa en la base de datos
		    		// Para ello preestablecemos el array que queremos insertar
		    		if (!Session::get('user_id')) {
		    			Session::add('feedback_negative', 'No tiene iniciada sesión, por lo tanto no podemos crear la empresa');
		    			return false;
		    		}
		    		$usuario = Session::get('user_id');
		    		$datos = [	':nombre' 		=> $array['nombre'],
		    					':web' 			=> $array['web'],
		    					':descripcion'  => $array['descripcion'],
		    					':usuario'		=> (int) $usuario,
		    					':id'			=> (int) $id
		    		];
		    		// devolvemos lo que la inserción nos dice
		    		//d($datos);die;
		    		return EmpresaModel::edit($datos);

		    	} else {
		    		// Como ya existen los errores en Session
		    		// simplemente los devolvemos
		    		return false;
		    	}
	    	} else {
	    		Session::add('feedback_negative', 'No se ha modificado la empresa');
		    	return false;
	    	}
	    }// editar()

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
	   					$resultado =  EmpresaModel::search($params);
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
	   		$ssql = 'INSERT INTO empresa (nombre, web, descripcion, usuario) VALUES (UPPER(:nombre), LOWER(:web), :descripcion, :usuario)';
	   		$params = [	':nombre' 		=> $array[':nombre'],
	   					':web' 			=> $array[':web'],
	   					':descripcion'	=> $array[':descripcion'],
	   					':usuario'		=> $array[':usuario']
	   					];
	   		// 3 = comprobación + last_id en $_POST
	   		return Database::consulta($ssql, $params, $estado = 3);
	   	}// insert()

	   	/**
	   	 * Método de borrado de empresas
	   	 * @param  Integer $id ID a borrar
	   	 * @return Boolean     True = si se borra, False = si no se borra
	   	 */
	   	public static function delete($id)
	   	{
	   		$ssql = 'DELETE FROM empresa WHERE id = :id';
	   		$params = [':id' => $id];
	   		return Database::consulta($ssql, $params, $estado = 2);
	   	}// delete()

	   	/**
	   	 * Método que se encarga de editar en la base de datos
	   	 * @param  Array $params Datos a actualizar
	   	 * @return Boolean       True = si se edita, False = no se edita
	   	 */
	   	public static function edit($params)
	   	{
	   		$ssql = 'UPDATE empresa SET nombre = UPPER(:nombre), web = LOWER(:web), descripcion = :descripcion, usuario = :usuario WHERE id = :id';
	   		return Database::consulta($ssql, $params, $estado = 3);
	   	}// edit()

	   	/**
	   	 * Método que ejecuta la busqueda y la devuelve
	   	 * @param  Array $params  Datos que entran se estan buscando
	   	 * @return Array         [description]
	   	 */
	   	public static function search($params)
	   	{
	   		$ssql = 'SELECT empresa.id ,empresa.nombre as nombre, web, descripcion FROM empresa, usuario WHERE usuario = usuario.id AND usuario.id = :usuario AND (empresa.nombre LIKE :busqueda OR web LIKE :busqueda OR descripcion LIKE :busqueda)';
	   		//d($ssql);die();
	   		return Database::consulta($ssql, $params, $estado = 1);
	   	}// search()
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
	    		if (($erro = Validaciones::validarNombre($array["nombre"], 50)) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        } else {
		        	if (isset($array['id'])) {
		        		// obtengo todos los nombres salvo el de la empresa
		        		// que intento editar
		        		$nombres = EmpresaModel::getNombreNoRepetido($array['id']);
		        		if (!EmpresaModel::compararNombre($nombres, $array['nombre'])) {
		        			Session::add('feedback_negative', 'La empresa ya exite');
		        		}
		        	} else {
		        		if (EmpresaModel::getNombre($array["nombre"])) {
			        		Session::add('feedback_negative', 'La empresa ya exite');
			        	}
		        	}
		        }
	    	} else {
	    		Session::add('feedback_negative', 'El nombre no ha sido recicibido');
	    	}// fin de las validaciones del nombre

	    	// Validación de la web
	    	if (isset($array['web'])) {
	    		if (($erro = Validaciones::validarUrl($array["web"])) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'La web no ha sido recicibida');
	    	}// fin de las validaciones del apellido

	    	// Validación de la descripcion
	    	if (isset($array['descripcion'])) {
	    		$array['descripcion'] = Validaciones::limpiarTextarea($array['descripcion']);
	    		if (($erro = Validaciones::validarDescripcion($array["descripcion"], 1000)) !== true) {
		        	Session::addArray('feedback_negative', $erro);
		        }
	    	} else {
	    		Session::add('feedback_negative', 'La descripcion no ha sido recicibida');
	    	}// Fin de la validación de la descripcion

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