<?php 

include_once MODEL_DIR."/drivers/driverController.class.php";

/**
 * Clase que provee toda la funcionalidad del modelo.
 * No se puede crear instancias de esta clase, por ser abstracta.
 */
abstract class AbstractModel {
	
    private $columnName = array(); // array que contiene el nombre de los campos de la tabla de la BD
    public $dataType = array(); // contiene el tipo de dato que tiene un campo
    private $dataNull = array(); // contiene los campos que se consideraran nulos para consultas a la BD
    private $tableName; // nombre de la tabla de la BD
    protected $serverDatabase; // instancia del objeto segun el Servidor
    private $pkTable; // la columna index de la tabla de la BD
    protected $schema; // el esquema de la base de datos
    protected $gestor; // el esquema de la base de datos
    protected $oQueryBuilder = null;
	protected $sede = ""; // la sede donde se haran las consultas

    /**
     * Constructor de la clase
     * @param String $table nombre de la tabla
     * @param String $schema nombre del esquema
     */
    public function __construct($table, $schema) {
        $this->tableName = $table;
        $this->schema = $schema;
        $this->gestor = SERVER_DATABASE;
    }

    /**
     * Establece el nombre del esquema
     * @param String $schema
     */
    public function setSchema($schema) {
        $this->schema = $schema;
    }

    /**
     * Retorna el nombre del esquema usado
     * @return String
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * Recibe el nombre del gestor de base de datos y devuelve un objeto 
     * para la interaccion con la base de datos
     * @param String $gestor
     * @return Object
     */
    public function getServerDatabase($gestor) {
        $this->serverDatabase = DriverController::getDriver($gestor);
        return $this->serverDatabase;
    }
	
	public function getCurrentServerDatabase() {
		return $this->serverDatabase;
	}
	
	public function getNewInstanceServerDatabase($gestor) {
        return DriverController::getDriver($gestor);
    }

    /**
     * Habilita la conexion a la base de datos.
     * Extrae los nombres de los campos de la tabla actual.
     */
    public function init() {
        $this->serverDatabase = DriverController::getDriver($this->gestor);
        $conn = $this->serverDatabase->connect();
        if($conn) {
            $this->serverDatabase->clientEncoding();
            // genera la consulta para obtener los nombres de los campos
            $query = $this->serverDatabase->getDetailTable($this->tableName, $this->schema);

            $this->serverDatabase->execute($query); // ejecutamos la consulta
            $details = $this->serverDatabase->fetchAll(); // obtenemos el result
            if(!empty($details)) {
                // generamos la consulta para obtener el PK de la tabla
                $query = $this->serverDatabase->getPKTable($this->tableName, $this->schema);
                $this->serverDatabase->execute($query);
                $this->pkTable = $this->serverDatabase->fetchValue("p_field");

                if(empty($this->pkTable)) {
                    // si la tabla no tiene PK (e.g. Vista), se asigna como PK el primer campo de la tabla
                    $this->pkTable = $details[0][0];
                }

                // asignamos los campos y su tipo
                foreach($details as $value) {
                    $this->columnName[$value[0]] = null;
                    $this->dataType[$value[0]] = $this->serverDatabase->getDataType($value[1]);
                }
            }
        }
    }

    /**
     * Elimina las columnas obtenidas de la base de datos.
     * Util cuando se desea generar varios modelos de las tablas
     * con un solo objeto.
     */
    public function emptyColumns() {
        $this->columnName = array();
        $this->dataType = array();
    }

    /**
     * Habilita la conexion a la base de datos sin extraer los campos de la tabla.
     * Este metodo es usado por la clase ModelAdapter
     */
    public function initialize() {
        $this->serverDatabase = DriverController::getDriver($this->gestor);
        $this->serverDatabase->connect();
        $this->serverDatabase->clientEncoding();
    }

    /**
     * Metodo magico para asignar los valores a los campos de la tabla
     * @param String $name nombre del campo de la tabla
     * @param mixed $value valor asignado al campo
     * @throws Exception
     */
    public function __set($name, $value) {
        if(array_key_exists($name, $this->columnName)) {
            if($value === null) {
                $this->columnName[$name] = null;
            }
            else {
                switch($this->dataType[$name]) {
                    case "integer":
                    case "special":
                        $value = intval($value);
                        break;
                    case "float":
                        $value = floatval($value);
                        break;
                    default:
                        $value = (string) $value;
                        break;
                }

                $this->columnName[$name] = $value;
            }
        }
        else {
            throw new Exception ("El campo {$name} no existe en la tabla {$this->tableName}");
        }
    }

    /**
     * Metodo magico para obtener el valor del campo de la tabla.
     * Retorna el valor del campo o null si el campo indicado no existe en la tabla.
     * @param String $name nombre del campo
     * @return mixed or null
     */
    public function __get($name) {
        if(array_key_exists($name, $this->columnName)) {
            return $this->columnName[$name];
        }

        return null;
    }

    /**
     * OBSOLETO: Metodo para indicar que campos de la tabla seran nulos cuando
     * se hace una insercion o actualizacion de registros.
     * En su lugar asigne al campo el valor de null.
     * @param String $columnName nombre de los campos separados por coma (,).
     */
    public function acceptValuesNull($columnName) {
        if(!empty($columnName)) {
            $columnName = str_replace(' ', '', $columnName);
            $columnName = strtolower(trim($columnName));
            $columns = explode(',', $columnName);
            foreach($columns as $val) {
                if(array_key_exists($val, $this->columnName)) {
                    $this->dataNull[$val] = $val;
                }
            }
        }
    }

    /**
     * OBSOLETO: Metodo para quitar la propiedad de null al campo asignado 
     * mediante el metodo <acceptValuesNull>.
     * En su lugar asigne al campo un valor.
     * @param String $columnName nombre de los campos separados por coma (,).
     */
    public function removeValuesNull($columnName) {
        if(!empty($columnName)) {
            $columnName = str_replace(' ', '', $columnName);
            $columnName = strtolower(trim($columnName));
            $columns = explode(',', $columnName);
            foreach($columns as $val) {
                if(array_key_exists($val, $this->columnName)) {
                    unset($this->dataNull[$val]);
                }
            }
        }
    }

    /**
     * Obtener el campo de la tabla.
     * Retorna el valor del campo o null si el campo indicado no existe en la tabla.
     * @param String $columnName nombre del campo
     * @return Mixed or null
     */
    public function get($columnName) {
        $columnName = trim($columnName);
        if(array_key_exists($columnName, $this->columnName)) {
            return $this->columnName[$columnName];
        }

        return null;
    }

    /**
     * Asigna los valores a los campos de la tabla.
     * @param String $columnName nombre del campo
     * @param Mixed $value valor del campo
     * @param boolean $transformUpperCase convierte a mayusculas el valor del campo
     */
    public function set($columnName, $value, $transformUpperCase = true) {
        $columnName = trim($columnName);
        if(array_key_exists($columnName, $this->columnName)) {
            if($value === null) {
                $this->columnName[$columnName] = null;
            }
            else {
                switch($this->dataType[$columnName]) {
                    case "integer":
                    case "special":
                        $value = intval($value);
                        break;
                    case "float":
                        $value = floatval($value);
                        break;
                    default:
                        $value = (string) $value;
                        if($transformUpperCase) 
                            $value = strtoupper($value);
                        break;
                }

                $this->columnName[$columnName] = $value;
            }
        }
    }

    /**
     * Metodo para asignar los valores de los campos de la tabla.
     * El primer parametro es un array, los valores se asignan unicamente
     * si las claves del array coinciden con el nombre de los campos.
     * e.g.
     *      $columnas = array(
     *          'id' => 2
     *          ,'nombre' => 'mi nombre'
     *          ,'telefono' => 520000
     *          ,'este_campo_no_existe' => 'algun valor'
     *      );
     *      $this->setValues($columnas);
     * 
     * @param array $arrayValues array simple
     * @param boolean $transformUpperCase indica si el testo se convierte a mayuscula
     */
    public function setValues($arrayValues, $transformUpperCase = true) {
        if(!empty($arrayValues)) {
            if(is_array($arrayValues)) {
                foreach($arrayValues as $key => $value) {
                    $this->set($key, $value, $transformUpperCase);
                }                    
            }
        }
    }

    /**
     * Metodo para devolver los campos de la tabla con sus respectivos 
     * valores asignados.
     * Retorna un array con las claves compuestas por el nombre de las columnas.
     * Si en todo caso no se obtenieron los nombres de los campos retorna null.
     * @return array or null
     */
    public function getDataValues() {
        if(!empty($this->columnName)) {
            return $this->columnName;
        }

        return null;
    }

    /**
     * Estabece el nombre de la tabla de la base de datos 
     * con la cual se trabajara.
     * @param String $tableName
     */
    public function setTableName($tableName) {
		if(strpos($tableName, '.') === false) {
			$this->tableName = $tableName;
		}
		else {
			$data = explode('.', $tableName, 2);
			if(count($data) == 2) {
				$this->setSchema($data[0]);
				$this->tableName = str_replace('.', '', $data[1]);
			}
			else {
				$this->tableName = $data[0];
			}
		}
    }

    /**
     * Obtiene el nombre de la tabla con la cual se esta trabajando.
     * @return String
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Retorna el nombre completo de la tabla, incluido el esquema.
     * @return String
     */
    public function getFullTableName() {
        $table = (!empty($this->schema)) ? $this->schema . '.' . $this->tableName : $this->tableName;
        return $table;
    }

    /**
     * Retorna los nombre de los campos de la tabla actual
     * @return array
     */
    public function getColumnsName() {
        $columnsName = array_keys($this->columnName);

        return $columnsName;
    }

    /**
     * Devuelve el campo PK de la tabla actual.
     * @return String
     */
    public function getIndexColumn() {
        return $this->pkTable;
    }
	
	public function setIndexColumn($pk) {
        $this->pkTable = $pk;
    }

    /**
     * Obtiene los valores de los campos de la tabla segun el PK 
     * asignado previamente.
     */
    public function updateValues($decodeHtmlEntity = true) {
        $data = $this->prepareValues();

        $query = "SELECT * FROM ".$this->getFullTableName()." WHERE ".$this->pkTable." = ".$data[$this->pkTable];
        $this->serverDatabase->execute($query);
        $values = $this->serverDatabase->fetchArray();

        if(!empty($values)) {
            $fields = array_keys($this->columnName);

            foreach($fields as $val) {
				// if($decodeHtmlEntity)
				if(!$decodeHtmlEntity)
					$this->set($val, html_entity_decode($values[$val]), false);
				else
					$this->set($val, $values[$val], false);
            }
        }
    }

    /**
     * Valida y prepara los valores antes de insertar o modificar los registros
     * @return array
     */
    private function prepareValues() {
        $fields = array_keys($this->columnName);

        $arrayData = array();

        foreach($fields as $val) {
            
            $arrayData[$val] = "null";
            
            switch($this->dataType[$val]) {
                case "integer":
                    if( !is_null($this->columnName[$val]) ) {
                        if( is_int($this->columnName[$val]) || is_numeric($this->columnName[$val]) ) {
                            $arrayData[$val] = intval($this->columnName[$val]); // es una cadena numerica
                        }
                    }
                    break;

                case "float":
                    if( !is_null($this->columnName[$val]) ) {
                        if( is_float($this->columnName[$val]) || is_numeric($this->columnName[$val]) ) {
                            $arrayData[$val] = floatval($this->columnName[$val]); // es una cadena numerica
                        }
                    }
                    break;
                    
                default: 
                    if( !is_null($this->columnName[$val]) ) {
                        if( trim($this->columnName[$val]) != "" ) {
                            $arrayData[$val] = "'".$this->serverDatabase->escapeString($this->columnName[$val])."'";
                            // $arrayData[$val] = "'".htmlentities($this->serverDatabase->escapeString($this->columnName[$val]))."'";
                        }
                    }
                    break;
            }
        }

        return $arrayData;
    }

    /**
     * Metodo para insertar a la base de datos los valores de los 
     * campos asignados anteriormente.
     * @param boolean $pk_autoincrement indica si la PK es auto_increment
     * @param boolean $returning_pk indica si se devolvera el valor 
     * de la PK despues de la insercion
     * @return boolean true si la consulta se ejecuta correctamente 
     * false de lo caontrario
     */
    public function insert($pk_autoincrement = true, $returning_pk = false) {
        $values = $this->prepareValues();
        if($pk_autoincrement) {unset($values[$this->pkTable]);}

        $table = $this->getFullTableName();

        if($returning_pk) {
            $this->serverDatabase->prepareQuery('insert', $table, $values, $this->pkTable);
        }
        else {
            $this->serverDatabase->prepareQuery('insert', $table, $values);
        }
        $query = $this->serverDatabase->getQuery();
        $this->serverDatabase->execute($query);

        return $this->serverDatabase->isSuccess();
        //return $query;
    }
    
    /**
     * Metodo para insertar un registro y devolver el id generado 
     * para la clave primaria.
     * @return mixed or null
     */
    public function insert_get_id() {
        if($this->insert(true, true)) {
            $id = $this->getValue(0);
            $this->set($this->pkTable, $id, false);
            return $id;
        }
        
        return null;
    }

    /**
     * Metodo para actualizar los campos de la tabla con los valores asignados.
     * @param String $fieldsAffected nombre de los campos (separados por ,), 
     * que seran afectados. Son los campos despues del SET en una consulta SQL.
     * Si no se envia nada, se actualizara todos los campos de la tabla.
     * @param String $fieldsEvaluated nombre de los campos (separados por ,), 
     * por los cuales se evaluara. Son los campos despues del WHERE.
     * Si no se envia nada, se evaluara por la PK.
     * @param String $fieldsAllow indica los campos (seprados por ,) que no 
     * se actualizaran.
     * @return boolean segun el exito al ejecutar la consulta.
     */
    public function update($fieldsAffected = "", $fieldsEvaluated = "", $fieldsAllow = "") {
        $values = $this->prepareValues();
        $fieldAffected = array();
        $fieldEvaluated = array();

        if(empty($fieldsAffected)) {
            $fieldAffected = $values;
            unset($fieldAffected[$this->pkTable]);
        }
        else {
            $fieldsAffected = str_replace(' ', '', $fieldsAffected);
            $fieldsAffected = strtolower(trim($fieldsAffected));
            $fields = explode(',', $fieldsAffected);
            foreach($fields as $val) {
                $fieldAffected[$val] = $values[$val];
            }
        }

        if(!empty($fieldsAllow)) {
            $nameAllow = str_replace(' ', '', $fieldsAllow);
            $nameAllow = strtolower(trim($nameAllow));
            $fields = explode(',', $nameAllow);
            foreach($fields as $val) {
                if(array_key_exists($val, $values)) {
                    unset($fieldAffected[$val]);
                }
            }
        }

        if(empty($fieldsEvaluated)) {
            $fieldEvaluated[$this->pkTable] = $values[$this->pkTable];
        }
        else {
            $fieldsEvaluated = str_replace(' ', '', $fieldsEvaluated);
            $fieldsEvaluated = strtolower(trim($fieldsEvaluated));
            $fields = explode(',', $fieldsEvaluated);
            foreach($fields as $val) {
                $fieldEvaluated[$val] = $values[$val];
            }
        }

        $this->serverDatabase->prepareQuery('update', $this->getFullTableName(), $fieldAffected, $fieldEvaluated);
        $query = $this->serverDatabase->getQuery();
        $this->serverDatabase->execute($query);

        return $this->serverDatabase->isSuccess();
    }

    /**
     * Metodo para eliminar los registros de la tabla actual.
     * @param String $fieldsEvaluated nombre de los campos (separados por ,)
     * por las cuales se evaluara la consulta.
     * @return boolean depende del exito de la ejecucion de la consulta.
     */
    public function delete($fieldsEvaluated = "") {
        $values = $this->prepareValues();
        $fieldEvaluated = array();

        if(empty($fieldsEvaluated)) {
            $fieldEvaluated[$this->pkTable] = $values[$this->pkTable];
        }
        else {
            $fieldsEvaluated = str_replace(' ', '', $fieldsEvaluated);
            $fieldsEvaluated = strtolower(trim($fieldsEvaluated));
            $fields = explode(',', $fieldsEvaluated);
            foreach($fields as $val) {
                $fieldEvaluated[$val] = $values[$val];
            }
        }

        $this->serverDatabase->prepareQuery('delete', $this->getFullTableName(), array(), $fieldEvaluated);
        $query = $this->serverDatabase->getQuery();
        $this->serverDatabase->execute($query);

        return $this->serverDatabase->isSuccess();
    }

    /**
     * Metodo para ejecutar una consulta SELECT sobre la tabla actual.
     * Si $fieldsSelected esta vacio, se seleccionara todos los campos.
     * Si $fieldsEvaluated esta vacio y $includeWhere es false la consulta
     * no tiene WHERE, de lo contrario se evalua segun el PK.
     * @param String $fieldsSelected nombre de los campos (separados por ,),
     * ha seleccionar.
     * @param String $fieldsEvaluated nombre de los campos (separados por ,),
     * por las cuales se evaluara la consulta.
     * @param boolean $includeWhere si se incluira WHERE en la consulta.
     * @param Strin $orderBy el campo por el cual ordenar la columna.
     * @return boolean estado de la consulta
     */
    public function select($fieldsSelected = "", $fieldsEvaluated = "", $includeWhere = true, $orderBy = '') {
        $values = $this->prepareValues();
        $fieldSelected = array();
        $fieldEvaluated = array();

        if(empty($fieldsSelected)) {
            $fieldSelected = $values;
        }
        else {
            $fieldsSelected = str_replace(' ', '', $fieldsSelected);
            $fieldsSelected = strtolower(trim($fieldsSelected));
            $fields = explode(',', $fieldsSelected);
            foreach($fields as $val) {
                $fieldSelected[$val] = $values[$val];
            }
        }

        if(empty($fieldsEvaluated)) {
            if($includeWhere) {
                $fieldEvaluated[$this->pkTable] = $values[$this->pkTable];
            }
            else {
                $fieldEvaluated = array();
            }
        } 
        else {
            $fieldsEvaluated = str_replace(' ', '', $fieldsEvaluated);
            $fieldsEvaluated = strtolower(trim($fieldsEvaluated));
            $fields = explode(',', $fieldsEvaluated);
            foreach($fields as $val) {
                $fieldEvaluated[$val] = $values[$val];
            }
        }

        $this->serverDatabase->prepareQuery('select', $this->getFullTableName(), $fieldSelected, $fieldEvaluated);
        $query = $this->serverDatabase->getQuery();
		
		if(!empty($orderBy)) {
			$orderBy = trim($orderBy);
			if(array_key_exists($orderBy, $this->columnName)) {
				$query .= ' ORDER BY ' . $orderBy;
			}
		}
        
		$this->serverDatabase->execute($query);

        return $this->serverDatabase->isSuccess();
    }
    
    /**
     * Metodo para escapar las variables ha ser usadas en las consultas SQL.
     * Si la variable es tipo entero o float se hace una conversion explicita.
     * @param mixed $var variable a ser escapada
     * @param boolean $withQuotes indica si se pondra comillas sobre la variable
     * @return mixed variable escapada
     */
    public function escape_var($var, $numeric = false, $withQuotes = false) {
        if( !is_null($var) ) {
            if( is_numeric($var) ) {
                if(is_string($var)) {
                    if(!$numeric)
                        if($withQuotes)
                            return "'".htmlentities($this->serverDatabase->escapeString($var))."'";
                        else
                            return htmlentities($this->serverDatabase->escapeString($var));
                }
                
                $var = floatval($var);
                $res = abs($var) - intval($var);

                if($res > 0)
                    return $var;
                else
                    return intval($var);
            }
            else {
                if($withQuotes)
                    return "'".htmlentities($this->serverDatabase->escapeString($var))."'";
                else
                    return htmlentities($this->serverDatabase->escapeString($var));
            }
        }
        return 'null';
    }

    /**
     * Metodo para ejecutar una funcion o procedimiento almacenado (subrutina).
     * Si el parametro $params es una cadena vacia y $hasParams es true,
     * los parametros de la subrutina seran los valores asignados a los 
     * campos de la tabla, en el orden de los columnas de la tabla.
     * Si $hasParams es false, la subrutina no tiene parametros.
     * Si $nameFunction es una cadena vacia el nombre de la subrutina es el 
     * nombre de la tabla en el mismo esquema.
     * $nameAllow sirve para indicar que campos no se tendran en cuenta en los 
     * parametros (util cuando $params es vacio).
     * @param string $params nombre de los campos (separados por ,) de la tabla
     * actual, que seran enviados como parametros
     * @param string $nameFunction nombre de la subrutina
     * @param string $nameAllow campos a evitar como parametro
     * @param type $hasParams indica si la subrutina tendra parametros.
     * @return boolean exito de la consulta.
     */
    public function execute($params = "", $nameFunction = "", $nameAllow = "", $hasParams = true) {
        $dataValues = $this->prepareValues();
        
        if(empty($nameFunction)) {
            $nameFunction = !empty($this->schema) ? $this->schema . '.' . $this->tableName : $this->tableName;
        }
        else {
            if(strpos($nameFunction, '.') === false) {
                if(!empty($this->schema)) {
                    $nameFunction = $this->schema . '.' . $nameFunction;
                }
            }
        }
        
        if(empty($params)) {
            if($hasParams) {$values = $dataValues;}
            else {$values = array();}
        }
        else {
            if(is_array($params)) {
                $values = array();
                foreach($params as $key => $val) {
                    $values[$key] = $this->escape_var($val, false, true);
                }
            }
            else {
                $params = str_replace(' ', '', $params);
                $params = strtolower(trim($params));
                $value = explode(',', $params);
                $values = array();
                foreach($value as $key) {
                    if(array_key_exists($key, $dataValues)) {
                        $values[$key] = $dataValues[$key];
                    }
                }
            }
        }
        
        if(!empty($nameAllow)) {
            $nameAllow = str_replace(' ', '', $nameAllow);
            $nameAllow = strtolower(trim($nameAllow));
            $valuesAllow = explode(',', $nameAllow);
            foreach($valuesAllow as $key) {
                if(array_key_exists($key, $dataValues)) {
                    unset($values[$key]);
                }
            }
        }
        
        $this->serverDatabase->prepareQuery('subrutina', $nameFunction, $values);
        $query = $this->serverDatabase->getQuery();
        //return $query;
        $this->serverDatabase->execute($query);

        return $this->serverDatabase->isSuccess();
    }
    
    /**
     * Obtienen los valores de los campos de la tabla, 
     * despues de ejecutar la consulta.
     * @param int $index posicion de la fila a extraer
     */
    public function updateValuesWithResultSet($index) {
        if($this->getNumRows()) {
            $all = $this->getAll();
            if(count($all) > $index) {
                $this->setValues($all[$index], false);
            }
        }
    }

    /**
     * Metodo para retornar la ultima consulta SQL ejecutado en la base de datos.
     * @return string
     */
    public function getQuery() {
        return $this->serverDatabase->getQuery();
    }

    /**
     * Retorna el todo el ResultSet de la consulta SQL.
     * @return array 2D
     */
    public function getAll() {
        return $this->serverDatabase->fetchAll();
    }

    /**
     * Retorna la primera fila del ResultSet de la consulta SQL.
     * @return array simple
     */
    public function getArray() {
        return $this->serverDatabase->fetchArray();
    }

    /**
     * Retorna el valor del ResultSet segun la posicion indicada.
     * @param mixed $field int o string contiene la posicion o 
     * nombre de la columna de la tabla.
     * @param int $row posicion del registro del ResutSet.
     * @return mixed valor de la columna.
     */
    public function getValue($field, $row = 0) {
        return $this->serverDatabase->fetchValue($field, $row);
    }

    /**
     * Numero de registros del ResultSet.
     * @return int
     */
    public function getNumRows() {
        return $this->serverDatabase->numRows();
    }

    /**
     * Retorna el estado de la ejecucion de la consulta SQL.
     * @return boolean true si la consulta se ejeucuto con exito
     * false de lo contrario.
     */
    public function getStatus() {
        return $this->serverDatabase->isSuccess();
    }

    /**
     * Retorna el ultimo mensaje de error ocurrido al ejecutar una consulta SQL.
     * @return string
     */
    public function getError() {
        return $this->serverDatabase->lastError();
    }

    /**
     * Inicia una tranacciona a la base de datos.
     */
    public function startTransaction() {
        $this->serverDatabase->begind();
    }

    /**
     * Guarda la transaccion.
     */
    public function commit() {
        $this->serverDatabase->commit();
    }

    /**
     * Cancela la transaccion.
     */
    public function rollback() {
        $this->serverDatabase->rollback();
    }

    /**
     * Metodo para ejecutar consultas SQL a la base de datos.
     * @param string $query la consulta
     * @return boolean true si exito false de lo contrario.
     */
    public function query($query = '') {
        if(empty($query)) {
            if($this->oQueryBuilder instanceof Query) {
                $query = $this->oQueryBuilder->toSQL();
            }
        }
        if( !empty($query) ) {
            $this->serverDatabase->execute($query);
            return $this->serverDatabase->isSuccess();
        }
        
        return true;
    }
    
    /**
     * Metodo para establecer el valor de la clave primaria como un array
     * @param mixed $args
     */
    protected function setPkAsArray($args = array()) {
        if(!empty($args)) {
            if(!is_array($args)) {
                $v = $args;
                $args = array();
                $args[$this->pkTable] = $v;
            }
            $this->setValues($args, false);
        }
    }

    /**
     * Metodo para asignar valores a los campos, hacer una busqueda segun 
     * el PK, y obtener los valores de los campos de la base de datos.
     * @param array $args
     */
    public function find($args = array(), $decodeHtmlEntity = true) {
        $this->setPkAsArray($args);
        $this->updateValues($decodeHtmlEntity);
    }
    
    /**
     * Metodo para verificar si un regsitro existe en la base de datos
     * @param mixed $args
     * @return boolean true si existe el registro de lo contrario false
     */
    public function exists($args = array()) {
        $this->setPkAsArray($args);
        
        $data = $this->prepareValues();

        $query = "SELECT * FROM ".$this->getFullTableName()." WHERE ".$this->pkTable." = ".$data[$this->pkTable];
        $this->serverDatabase->execute($query);
        $values = $this->serverDatabase->fetchArray();

        return (!empty($values));
    }
    
    /**
     * Metodo para guardar los datos en la base
     * @return boolean
     */
    public function save($autoincrement = true) {
        $id = $this->get($this->pkTable);
        if(isset($id) && $this->exists()) {
            return $this->update();
        }
        else {
            return $this->insert($autoincrement);
        }
    }
}

?>