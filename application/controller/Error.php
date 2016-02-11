<?php

class Error extends Controller
{
    private $msg;

    public function __construct($msg = '',View $view)
    {
        parent::__construct($view);
        $this->msg = $msg;
    }

    public function index()
    {
        echo $this->view->render('error/index',[
            'msg' => $this->msg
        ]);
    }

}
