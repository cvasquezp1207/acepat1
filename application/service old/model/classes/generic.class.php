<?php

include_once MODEL_DIR."/classes/abstractModel.class.php";

/**
 * Clase que da acceso a todas las funciones de AbstractModel.
 */
class Generic extends AbstractModel {
	
	/**
	 * El constructor.
	 * @param string $tableName nombre de la tabla
	 * @param string $schema nombre del esquema
	 */
	public function __construct($tableName, $schema = "") {
		parent::__construct($tableName, $schema);
		$this->init();
	}
}

?>