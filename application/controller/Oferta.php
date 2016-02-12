<?php

	class Oferta extends Controller
	{
		/**
		 * Método del constructor del controlador de ofertas
		 * @param View $view Objeto de tipo vista utilizado por Dice, para la inyección de dependencias
		 */
	    public function __construct(View $view)
	    {
	        parent::__construct($view);
	        Auth::checkAutentication();
	    }// __construct()

	    /**
	     * Método index del controlador de Oferta
	     */
	    public function index()
	    {
	    	// bloque try - catch para parar posibles excepciones de PDO
	    	try {
	    		// Obtener del modelo todas las ofertas
		    	$ofertas = OfertaModel::todas();
		    	$datos = ['ofertas' => $ofertas];
		        echo $this->view->render("ofertas/index", $datos);
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
	     * Método que llama a la vista del formulario
	     */
	    public function crear()
	    {
	    	// bloque try - catch en caso de posibles excepciones de PDO
	    	try {
	    		if (!$_POST) {
		    		$empresa = OfertaModel::getNombreEmpresa();
		    		$datos = ['titulo' => 'Creación de oferta', 'empresas' => $empresa];
		    		echo $this->view->render('ofertas/formularioOferta', $datos);
		    	} else {
		    		// Comprobamos $_POST
			    	if (OfertaModel::alta($_POST)) {
			    		// Generamos el mensaje de que se ha creado apropiadamente
			    		Session::add('feedback_positive', 'La oferta se a creado satisfactoriamente');
			    		// comprobamos si tiene un origen, de ser asi lo enviamos al origen
			    		// en caso contrario lo redreccionamos a /
			    		if($origen = Session::get('origen')){
			                Session::set('origen', null);
			                header ('Location:' . $origen);
			                exit();
			            }else{
			                //Existe un fedback_positive desde el modelo
			                //para mostrar en home o en el controlador
			                $last_id = $_POST['last_id'];
			                header('Location: /Oferta#' . $last_id);
			                exit();
			            }
			    	} else {
			    		// existen errores, llamamos a la vista para mostrar los errores
			    		// Saneamos $_POST
			    		$array = Validaciones::sanearEntrada($_POST);
			    		$empresa = OfertaModel::getNombreEmpresa();
			    		$datos = ['datos' => $array, 'empresas' => $empresa];
			    		// mostramos la vista con los datos saneados, para evitar la inyeción de código
			    		echo $this->view->render('ofertas/formularioOferta', $datos);
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
	    }// crear()

	    /**
	     * Método de borrado de una oferta
	     * @param  integer $id Id de la oferta
	     */
	    public function borrar($id = 0)
	    {
	    	// Bloque TRY - CATCH para parar posibles excepciones
	    	try {
	    		OfertaModel::delete($id);
	    		header('Location: /Oferta');
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
	     * Método del controlador el cual se encarga de la edición de las ofertas
	     * @param  integer $id ID de la oferta a editar
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
	    			// comprobamos si la oferta que se intenta editar es del usuario
	    			// que realiza la petición
		    		if (!OfertaModel::comprobarPropiedadOferta($id)) {
		    			Session::add('feedback_negative', 'No puedes editar una oferta que no es tuya');
		    			header('Location: /Oferta');
		    			exit();
		    		}
		    		// mostrar el formulario para poder editar
		    		// para ello deberemos recuperar los BD
		    		$info = OfertaModel::getId($id);
		    		$empresa = OfertaModel::getNombreEmpresa();
		    		$datos = ['titulo' => 'Edición de oferta', 'empresas' => $empresa, 'datos' => $info];
		    		echo $this->view->render("ofertas/formularioOferta",$datos);
		    	} else {
		    		// tratamos los datos en el modelo
		    		if (OfertaModel::editar($id,$_POST)) {
		    			Session::add('feedback_positive', 'La oferta ha sido editada');
		    			header('Location: /Oferta');
		    			exit();
		    		} else {
		    			// existen errores, llamamos a la vista para mostrar los errores
			    		// Saneamos $_POST
			    		Session::add('feedback_negative', 'La oferta no se ha podido modificar o no se ha modificado nada');
			    		$empresa = OfertaModel::getNombreEmpresa();
			    		$array = Validaciones::sanearEntrada($_POST);
			    		$datos = ['datos' => $array, 'empresas' => $empresa];
			    		// mostramos la vista con los datos saneados,
			    		// para evitar la inyeción de código
			    		echo $this->view->render('ofertas/formularioOferta', $datos);
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

	    /**
	     * método que realiza la busqueda
	     */
	    public function buscar()
	    {
    		if ($ofertas = OfertaModel::buscar($_POST)) {
    			// llamamamos a la vista que permitira ver los resultados
    			$datos = ['ofertas' => $ofertas];
    			echo $this->view->render('ofertas/listaOfertas', $datos);
    		} else {
    			// Hay errores
    			if (isset($_POST['busqueda'])) {
    				$_POST['busqueda'] = Validaciones::limpiarString($_POST['busqueda']);
    				$ofertas = OfertaModel::todas();
	    			$datos = ['ofertas' => $ofertas,'busqueda' => $_POST['busqueda']];
	        		echo $this->view->render("ofertas/index", $datos);
    			} else {
    				// no se ha realizado busqueda alguna
    				header('Location: /Oferta');
    				exit();
    			}
    		}
	    }// buscar()

	}// fin de la clase Oferta
?>