<?php

/**
 * Interface que ofrece a la clase que implementa la capacidad de conexion
 * a la base de datos.
 */
interface Connectable {
    /**
     * Metodo a implementar para establecer la conexion a la base de datos.
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param int $port
     */
    function setConnection($host, $user, $password, $database, $port);
}

?>