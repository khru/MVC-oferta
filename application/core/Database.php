<?php

class Database
{
    // ======================================
    // Atributos
    // =====================================
    private static $instancia = null;
    private $db = null;

    /**
     * Método mágico constructor
     */
    private function __construct()
    {
        $options = array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        try{
            $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            exit("No tenemos accesible la Base de Datos");
        }
    }// __construct()

    /**
     * Método que crea el patron Singleton con las conexiones
     * Actualmente inecesario porque se esta utilizando el de Dice
     */
    public static function getInstance()
    {
        if(is_null(self::$instancia)){
            self::$instancia = new Database();
        }
        return self::$instancia;
    }// getInstance()

    /**
     * Método que devuelve la conexión a la base de datos
     * @return [type] [description]
     */
    public function getDatabase()
    {
        return $this->db;
    }// getDatabase()

    /**
     * Método que debuelve true si el numero de filas de una consulta es superior a 0
     * @param  Integer $filas Número de filas afectadas en una consulta
     * @return Boolean        true = si ha habido cambios en las filas, false en caso contrario
     */
    public static function comprobarConsulta($filas){
        if ($filas === 0) {
            return false;
        }
        return true;
    }//comprobarConsulta()

    /**
     * Método que ejecuta la consulta pasada como parametro
     * @param  String  $ssql   Consulta con el bindeo de parametros
     * @param  Array  $parms  Array asociativo, listo para realizar el bindeo de parametros
     * @param  boolean $return true = devuelve un Booleano, falso = devuelve el resultado
     */
    public static function consulta($ssql, $parms, $return = 2)
    {
        // creamos la conexión
        $conn = Database::getInstance()->getDatabase();
        // preparamos la consulta
        $prepare = $conn->prepare($ssql);
        // ejecutamos la consulta
        $prepare->execute($parms);
        if ($return === 2) {
            $count = $prepare->rowCount();
            return Database::comprobarConsulta($count);
        } elseif ($return === 1) {
            return $prepare->fetchAll();
        } elseif($return === 0) {
            return $prepare->fetch();
        } else {
            $_POST['last_id'] = $conn->lastInsertId();
            $count = $prepare->rowCount();
            return Database::comprobarConsulta($count);
        }
    }// $consulta
}