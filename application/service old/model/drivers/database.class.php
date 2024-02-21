<?php

include_once MODEL_DIR."/drivers/abstractDatabase.class.php";

/**
 * Clase que proporciona la conexion a la base de datos.
 */
class Database extends AbstractDatabase {

    private $connectable;
    
    /**
     * Guarda un objeto connectable segun el driver de la base de datos.
     * @param Connectable $c
     */
    public function addConnectable( $c ) {
        $this->connectable = $c;
    }

    /**
     * Crea una conexion a la base de datos desde el driver.
     * @return boolean dependiendo del exito de la conexion
     */
    public function connection() {
        return $this->connectable->setConnection($this->host, $this->user, $this->password, $this->database, $this->port);
    }
}

?>