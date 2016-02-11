<?php
	 /**
	 * Clase Usuario
	 */
	class Usuario extends Controller
	{

		/**
		 * Método constructor, que permitira utilizar este controlador
		 * @param View $view Objeto de tipo vista, para que dice pueda crear el objeto
		 */
	    public function __construct(View $view)
	    {
	        parent::__construct($view);
	        // Compruebo que no existe sesión, en caso de existir
	        // lo redirecciono al home
	        Auth::checkNoAutentication();
	    }

	    public function index()
	    {
	        echo $this->view->render("usuario/index");
	    }

	    public function alta()
	    {
	    	// Bloque try catch, para parar cualquier excepción producida
	    	// por la conexión
	    	try{
	    		if ($estado = UsuarioModel::alta($_POST)) {
		    		header('Location: /');
		    		exit();
		    	} else{
		    		// saneamos $_POST y lo mostramos
		    		$datos = Validaciones::sanearEntrada($_POST);
		    		$datos = ['datos' => $datos];
		    		echo $this->view->render('usuario/index', $datos);
		    		//header('Location: /Usuario');
		    	}
	    	}catch (PDOException $e){
	    		// llamamos a la vista de error 500
	    		$array = ['msg' => 'Error del servidor, disculpe las molestias.'];
	    		echo $this->view->render('error/error500',$array);
	    		// modo debbug ON
	    		/*echo '<pre>';
	    		echo $e->getMessage();*/
	    	}

	    }
	}
?>