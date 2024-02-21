<?php

/**
 * Clase abstracta para obtener los parametros de conexion a la base de datos
 * desde un archivo externo.
 */
abstract class AbstractDatabase {
	
    protected $server;
	
    protected $host;
    protected $user;
    protected $password;
    protected $database;
    protected $port;

    /**
     * Constructor de la clase
     * @param string $server
     */
    public function __construct($server = "mysql") {
        $this->server = $server;
        $this->init();
    }

    /**
     * Obtiene los parametros de conexion a la base de datos.
     */
    private function init() {
        $file = MODEL_DIR."/drivers/".$this->server.".conf";

        if(file_exists($file)) {
            $fileContent = file_get_contents($file);

            preg_match("/host = (.*)/", $fileContent, $host);
            $this->host = trim($host[1]);

            preg_match("/user = (.*)/", $fileContent, $user);
            $this->user = trim($user[1]);

            preg_match("/password = (.*)/", $fileContent, $password);
            $this->password = trim($password[1]);

            preg_match("/database = (.*)/", $fileContent, $database);
            $this->database = trim($database[1]);

            preg_match("/port = (.*)/", $fileContent, $port);
            $this->port = trim($port[1]);
        }
    }
}

?>