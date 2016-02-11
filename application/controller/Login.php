<?php

class Login extends Controller{

    public function __construct(View $view)
    {
        parent::__construct($view);
    }

    public function index()
    {
        // no utilizo Auth::checkAutentication();
        // porque si lo hago no puedo pasarle el titulo al formulario
        if (!Session::get('user_id')) {
            $datos = ['titulo' => 'Login'];
            echo $this->view->render('login/index', $datos);
        } else {
            header('Location: /');
            exit();
        }
    }

    public function dologin()
    {
        if (Session::get('user_id')) {
            header('Location: /');
            exit();
        }

        // Bloque TRY -CATCH para parar la posible excepción
        try {
            if(LoginModel::dologin($_POST)){
                if($origen = Session::get('origen')){
                    Session::set('origen', null);
                    header ('Location:' . $origen);
                    exit();
                }else{
                    // LLevarlo a la página personal si es necesario
                    // pero por ahora simplemente redireccionarlo al home
                    //echo $this->view->render('login/usuarioLogueado');

                    //Existe un fedback_positive desde el modelo
                    //para mostrar en home
                    header('Location: /');
                    exit();
                }

            } else {
                // Existen errores
                // Por eso necesitamos obtener el email para recuperarlo
                // y mostrarlo
                $datos = ['datos' =>$_POST];
                echo $this->view->render('login/index', $datos);

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

    public function salir()
    {

        Session::destroy();
        header('Location: /');
        exit();

    }


}