<?php

	class Empresa extends Controller
	{
		/**
		 * Método constructor del controlador Empresa
		 * @param View $view [description]
		 */
	    public function __construct(View $view)
	    {
	        parent::__construct($view);
	        Auth::checkAutentication();
	    }// __construct()

	    /**
	     * Método index del controlador
	     */
	    public function index()
	    {
	    	// Creamos un bloque try catch, para atrapar posible excepciones
	    	try{
	    		$empresas = EmpresaModel::todas();
	    		$datos = ['empresas' => $empresas];
	        	echo $this->view->render("empresa/index", $datos);
	    	} catch (PDOException $e) {
	    		// llamamos a la vista de error 500
	    		$array = ['msg' => 'Error del servidor, disculpe las molestias.'];
	    		echo $this->view->render('error/error500',$array);
	    		// modo debbug ON
	    		/*echo '<pre>';
	    		echo $e->getMessage();*/
	    	}

	    }// index()

	    /**
	     * Método que llama a la vista del formulario de alta de Empresas
	     * y que realiza la lógica de la inserción de empresas
	     */
	    public function crear()
	    {
	    	if (!$_POST) {
	    		$datos = ['titulo' => 'Creación de empresa'];
	    		echo $this->view->render("empresa/formularioEmpresa",$datos);
	    	} else {
	    		// creamos un bloque TRY - CATCH para preveer errores SQL
	    		try {
	    			// Comprobamos $_POST

			    	if (EmpresaModel::alta($_POST)) {
			    		// Generamos el mensaje de que se ha creado apropiadamente
			    		Session::add('feedback_positive', 'La empresa se a creado satisfactoriamente');
			    		// comprobamos si tiene un origen, de ser asi lo enviamos al origen
			    		// en caso contrario lo redreccionamos a /
			    		if($origen = Session::get('origen')){
			                Session::set('origen', null);
			                header ('Location:' . $origen);
			                exit();
			            }else{
			                //Existe un fedback_positive desde el modelo
			                //para mostrar en home
			                header('Location: /Empresa#' . $_POST['last_id']);
			                exit();
			            }
			    	} else {
			    		// existen errores, llamamos a la vista para mostrar los errores
			    		// Saneamos $_POST
			    		$array = Validaciones::sanearEntrada($_POST);
			    		$datos = ['datos' => $array];
			    		// mostramos la vista con los datos saneados,
			    		// para evitar la inyeción de código
			    		echo $this->view->render('empresa/formularioEmpresa', $datos);
			    	}
	    		} catch (PDOException $e) {
	    			// llamamos a la vista de error 500
	    			$array = ['msg' => 'Error del servidor, disculpe las molestias.'];
	    			echo $this->view->render('error/error500',$array);
	    			// modo debbug ON
		    		/*echo '<pre>';
		    		echo $e->getMessage();*/
	    		}
	    	}

	    }// crear()


	    /**
	     * Método de borrado de empresas
	     * @param  integer $id ID del registro a borrar
	     */
	    public function borrar($id = 0)
	    {
	    	try {
	    		EmpresaModel::borrar($id);
	    		header('Location: /Empresa');
	    		exit();
	    	} catch (PDOException $e) {
	    		// llamamos a la vista de error 500
	    		$array = ['msg' => 'Error del servidor, disculpe las molestias.'];
	    		echo $this->view->render('error/error500',$array);
	    		// modo debbug ON
	    		/*echo '<pre>';
	    		echo $e->getMessage();*/
	    	}

	    }// borrar()

	    /**
	     * Método de edición de Empresas
	     * @param  integer $id ID de la empresa a editar
	     */
	    public function editar($id = 0)
	    {
	    	// validamos y saneamos aqui porque vamos a utilizarlo en caso
	    	// de que no haya $_POST
	    	$id = (int) $id;
	    	$id = Validaciones::saneamiento($id);
	    	// Bloque try catch para preveer posibles excepciones
	    	try {
	    		if (!$_POST) {
	    			// comprobamos si la empresa que se intenta editar es del usuario
	    			// que realiza la petición
		    		if (!EmpresaModel::comprobarPropiedadEmpresa($id)) {
		    			Session::add('feedback_negative', 'No puedes editar una empresa que no es tuya');
		    			header('Location: /Empresa');
		    			exit();
		    		}
		    		// mostrar el formulario para poder editar
		    		// para ello deberemos recuperar los BD
		    		$empresa = EmpresaModel::getId($id);
		    		$datos = ['titulo' => 'Edición de empresa', 'datos' => $empresa];
		    		echo $this->view->render("empresa/formularioEmpresa",$datos);
		    	} else {
		    		// tratamos los datos en el modelo
		    		if (EmpresaModel::editar($id ,$_POST)) {
		    			Session::add('feedback_positive', 'La empresa ha sido editada');
		    			$last_id = $_POST["last_id"];
		    			header('Location: /Empresa');
		    			exit();
		    		} else {
		    			// existen errores, llamamos a la vista para mostrar los errores
			    		// Saneamos $_POST
			    		Session::add('feedback_negative', 'La empresa no se ha podido modificar o no se ha modificado nada');
			    		$array = Validaciones::sanearEntrada($_POST);
			    		$datos = ['datos' => $array];
			    		// mostramos la vista con los datos saneados,
			    		// para evitar la inyeción de código
			    		echo $this->view->render('empresa/formularioEmpresa', $datos);
		    		}

		    	}
	    	} catch (PDOException $e) {
	    		// llamamos a la vista de error 500
	    		$array = ['msg' => 'Error del servidor, disculpe las molestias.'];
	    		echo $this->view->render('error/error500',$array);
	    		// modo debbug ON
	    		/*echo '<pre>';
	    		echo $e->getMessage();*/
	    	}

	    }// editar()

	}// clase EMpresa
?>