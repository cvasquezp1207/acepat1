<?php

include_once MODEL_DIR."/drivers/connectable.interface.php";
include_once MODEL_DIR."/drivers/database.class.php";

/**
 * Driver de la base de datos.
 * Clase que ofrece metodos para acceder a la base de datos, 
 * ejecutar consultas, obtener registros.
 */
class PostgreSQL implements Connectable {
	
    private static $instance;
    private $engineDatabase;
    private $link;
    private $query;
    private $resultSet;
    private $msgError;
    private $success;
    private $throwException;
    private $dataType = array(
        'smallint'=>'special'
        ,'integer'=>'integer'
        ,'decimal'=>'float'
        ,'numeric'=>'float'
        ,'real'=>'float'
        ,'double precision'=>'float'
        ,'serial'=>'integer'
        ,'character varying'=>'text'
        ,'character'=>'text'
        ,'char'=>'text'
        ,'text'=>'text'
        ,'date'=>'date'
        ,'timestamp'=>'date'
        ,'boolean'=>'text'
    );
	private $dataTypeDefault = 'text';
    
    /**
     * El constructor de la clase.
     */
    public function __construct() {
        $this->throwException = (defined("THROW_EXCEPTION")) ? THROW_EXCEPTION : false;
        $this->engineDatabase = new Database("PostgreSQL");
    }
    
    /**
     * Establece el driver al objeto Database, para obtener los 
     * parametros de conexion.
     */
    public function setConectable() {
        $this->engineDatabase->addConnectable(self::$instance);
    }
    
    /**
     * Establece la conexion a la base de datos con los parametros de 
     * conexion obtenidos.
     * Este metodo ejecuta el metodo <setConnection> del driver.
     * @return boolean
     */
    public function connect() {
        return $this->engineDatabase->connection();
    }
    
    /**
     * Metodo para conectar a la base de datos directamente con los parametros
     * enviados.
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param int $port
     * @return boolean
     * @throws Exception si existe un error de conexion
     */
    public function setConnection($host, $user, $password, $database, $port) {
        $url = "host=$host port=$port dbname=$database user=$user password=";
        $this->link = @pg_pconnect($url.$password);
        if(!$this->link) {
            if($this->throwException) {
                throw new Exception('Error de conexion a la base de datos: <br>'.$url.'******');
            }
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Cierra la conexion a la base de datos.
     * @return int
     */
    public function closeConnection() {
        return @pg_close($this->link);
    }
    
    /**
     * Metodo para indicar la codificacion de los datos
     */
    public function clientEncoding($encoding = 'UTF8') {
        pg_set_client_encoding($encoding);
    }
    
    /**
     * Obtiene la consulta necesaria para obtener los campos de las tablas 
     * con sus respectivos tipos de datos.
     * @param string $table nombre de la tabla
     * @param string $schema nombre del esquema
     * @return string consulta SQL
     */
    public function getDetailTable($table, $schema) {
        $schema = (!empty($schema)) ? $schema : 'public';
        $query = "SELECT column_name, data_type FROM information_schema.columns 
                WHERE table_name = '$table' and table_schema = '$schema'";
		
        return $query;
    }
    
    /**
     * Retorna la consulta para obtener el PK de la tabla.
     * @param string $table nombre de la tabla
     * @param string $schema nombre del esquema
     * @return string consulta SQL
     */
    public function getPKTable($table, $schema) {
        $schema = (!empty($schema)) ? $schema : 'public';
        $query = "
                SELECT
                        c.oid AS conid, c.contype, c.conname, pg_catalog.pg_get_constraintdef(c.oid, true) AS consrc,
                        ns1.nspname as p_schema, r1.relname as p_table, ns2.nspname as f_schema,
                        r2.relname as f_table, f1.attname as p_field, f1.attnum AS p_attnum, f2.attname as f_field,
                        f2.attnum AS f_attnum
                FROM
                        pg_catalog.pg_constraint AS c
                        JOIN pg_catalog.pg_class AS r1 ON (c.conrelid=r1.oid)
                        JOIN pg_catalog.pg_attribute AS f1 ON (f1.attrelid=r1.oid AND (f1.attnum=c.conkey[1]))
                        JOIN pg_catalog.pg_namespace AS ns1 ON r1.relnamespace=ns1.oid
                        LEFT JOIN (
                                pg_catalog.pg_class AS r2 JOIN pg_catalog.pg_namespace AS ns2 ON (r2.relnamespace=ns2.oid)
                        ) ON (c.confrelid=r2.oid)
                        LEFT JOIN pg_catalog.pg_attribute AS f2 ON
                                (f2.attrelid=r2.oid AND ((c.confkey[1]=f2.attnum AND c.conkey[1]=f1.attnum)))
                WHERE
                        r1.relname = '$table' AND ns1.nspname='$schema'  AND c.contype = 'p'
                ORDER BY 1
        ";
        return $query;
    }
    
    /**
     * Metodo para escapar una cadena string, util para evitar injecciones SQL
     * @param string $value la cadena a escapar
     * @return string cadena escapada
     */
    public function escapeString($value) {
        return trim(pg_escape_string($value));
    }

    /**
     * Metodo para generar las consultas SQL con los parametros enviados.
     * @param string $operation tipo de consulta a crear
     * @param string $nameTable nombre de la tabla o subrutina
     * @param array $fieldsAffected cuyas claves contienen los nombres de 
     * las columnas afectadas con sus respectivos vaores
     * @param mixed $fieldsEvaluated array o string, en el caso que el tipo
     * de consulta sea insert indica el campo a retornar despues del insert,
     * en el caso de array indica los campos de evaluacion de la consulta. 
     */
    public function prepareQuery($operation, $nameTable, $fieldsAffected, $fieldsEvaluated = array()) {
        $operation = trim(strtolower($operation));
        $this->query = "";
		
        switch($operation) {
            case 'insert': 
                if(!empty($fieldsAffected)) {
                    $field = array_keys($fieldsAffected);
                    $value = array_values($fieldsAffected);
                    $this->query = "INSERT INTO $nameTable (".implode(', ', $field).") VALUES (".implode(', ', $value).")";
                    if(!empty($fieldsEvaluated)) {
                        $this->query .= " RETURNING ".$fieldsEvaluated;
                    }
                }
                break;
							
            case 'update': 
                if(!empty($fieldsAffected)) {
                    $this->query = "UPDATE $nameTable SET   ";
                    foreach($fieldsAffected as $key => $val) {
                        $this->query .= "$key = $val, ";
                    }
                    $this->query = substr_replace($this->query, "", -2);
                    $this->query .= " WHERE    ";
                    foreach($fieldsEvaluated as $key => $val) {
                        $this->query .= " $key = $val AND";									
                    }
                    $this->query = substr_replace($this->query, "", -3);
                }
                break;

            case 'delete': 
                if(!empty($fieldsEvaluated)) {
                    $this->query = "DELETE FROM $nameTable WHERE    ";
                    foreach($fieldsEvaluated as $key => $val) {
                        $this->query .= " $key = $val AND";
                    }
                    $this->query = substr_replace($this->query, "", -3);
                }
                break;

            case 'select': 
                if(!empty($fieldsAffected)) {
                    $this->query = "SELECT   ";
                    foreach($fieldsAffected as $key => $val) {
                        $this->query .= "$key, ";
                    }
                    $this->query = substr_replace($this->query, "", -2);
                    $this->query .= " FROM $nameTable ";
                    if(!empty($fieldsEvaluated)) {
                        $this->query .= "WHERE    ";
                        foreach($fieldsEvaluated as $key => $val) {
                            if($val == "null" || $val == "NULL")
                                $this->query .= " $key is $val AND";
                            else
                                $this->query .= " $key = $val AND";
                        }
                        $this->query = substr_replace($this->query, "", -3);
                    }
                }
                break;

            case 'subrutina':
                $values = "";
                if(!empty($fieldsAffected)) {
                    $values = implode(', ', $fieldsAffected);
                }
                $this->query = "SELECT * FROM $nameTable ($values)";
                break;
                
            default :
                break;
        }
    }

    /**
     * Establece la consulta que sera ejecutada.
     * @param string $query consulta SQL
     */
    public function setQuery($query) {
        $this->query = $query;
    }
    
    /**
     * Retorna la ultima consulta establecida.
     * @return string consulta SQL
     */
    public function getQuery() {
        return $this->query;
    }
	
    /**
     * Metodo para ejecutar una funcion existente en la base de datos.
     * @param string $functionName nombre de la funcion
     * @param array $params contiene los valores de los parametros.
     * @throws Exception si la consulta no tuvo exito.
     */
    public function executte($functionName, $params) {
        if(!empty($functionName)) {
            $this->query = "SELECT * FROM $functionName (".implode(', ', $params).")";
		
            $this->resultSet = @pg_query($this->link, $this->query);
			
            if(!$this->resultSet) {
                $this->success = false;
                $this->msgError = @pg_last_error($this->link);
                if($this->throwException) {
                    throw new Exception($this->msgError);
                }
            }
            else {
                $this->success = true;
            }
        }
    }
    
    /**
     * Ejecuta una consulta SQL en la base de datos.
     * @param string $query la consulta SQL
     * @throws Exception si ocurrio algun error.
     */
    public function execute($query = "") {
        if(!empty($query)) {
            $this->query = $query;
            $this->resultSet = @pg_query($this->link, $this->query);
			
            if(!$this->resultSet) {
                $this->success = false;
                $this->msgError = @pg_last_error($this->link);
                if($this->throwException) {
                    throw new Exception($this->msgError);
                }
            }
            else {
                $this->success = true;
            }
        }
    }
    
    /**
     * Retorna el tido de variable a tratar segun el tipo de columna 
     * de la tabla de la base de datos.
     * @param string $var nombre del tipo de columna de la tabla
     * @return string o null nombre del tipo de variable
     */
    public function getDataType($var) {
        if(array_key_exists($var, $this->dataType)) {
            return $this->dataType[$var];
        }
		
        return $this->dataType[$this->dataTypeDefault];
    }
    
    /**
     * Retorna el numero de registros obtenidos al ejecutar una consulta SQL.
     * Se obtienen registros si la consulta es un SELECT.
     * @return int numero de registros.
     */
    public function numRows() {
        return @pg_num_rows($this->resultSet);
    }
    
    /**
     * Retorna todos los registros obtenidos por la consulta SQL.
     * @return array
     */
    public function fetchAll() {
        $response = array();
        if(!empty($this->resultSet)) {
            while($row = @pg_fetch_array($this->resultSet)) {
                $response[] = $row;
            }
        }
		
        return $response;
    }
    
    /**
     * Retorna el primer registro del conjunto de registros obtenidos
     * tras ejecutar una consulta SQL.
     * @return array
     */
    public function fetchArray() {
        $response = array();
        if(!empty($this->resultSet)) {
            $response = @pg_fetch_array($this->resultSet);
        }
		
        return $response;
    }

    /**
     * Retorna un valor del conjunto de registros ResultSet
     * @param mixed $field int o string conteniene la posicion o nombre
     * de la columna que se desea obtener
     * @param int $row posicion del registro que se desea obtener.
     * @return mixed o null
     */
    public function fetchValue($field, $row = 0) {
        $response = null;
        if(!empty($this->resultSet)) {
            if(is_int($field))
                $response = @pg_fetch_result($this->resultSet, $row, $field);
            else if(is_string($field))
                $response = @pg_fetch_result($this->resultSet, $field);
        }
		
        return $response;
    }

    /**
     * Retorna el exito o no de la ejecucion de una consulta.
     * @return boolean
     */
    public function isSuccess() {
        return $this->success;
    }

    /**
     * Retorna el ultimo error ocurrido tras la ejecucion de una consulta.
     * @return string
     */
    public function lastError() {
        return $this->msgError;
    }
    
    /**
     * Inicia una transaccion a la base de datos
     */
    public function begind() {
        $this->execute('START TRANSACTION');
    }

    /**
     * Guarda la transaccion.
     */
    public function commit() {
        $this->execute('COMMIT');
    }

    /**
     * Cancela la transaccion.
     */
    public function rollback() {
        $this->execute('ROLLBACK');
    }

    /**
     * Metodo estatico para obtener la instancia activa del driver.
     * Se usa el patron Singleton para crear una sola instancia del driver.
     * @return Connectable
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    /**
     * Metodo para evitar la clonacion de la clase.
     */
    private function __clone() {}
}

?>