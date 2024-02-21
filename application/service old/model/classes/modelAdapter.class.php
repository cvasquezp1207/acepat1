<?php 

/**
 * Al instanciar la clase ModelAdapter se debera enviar el nombre del "objeto" a crear,
 * por default se crea con nombre <<adapter>>, si se desea tener las funcionalidades de
 * cualquier otro modelo se debera instanciar este con el nombre del modelo o el nombre 
 * de la tabla de la base de datos.
 * Despues de crear un objeto de la clase ModeloAdapter se debera llamar a cualquiera de
 * los dos siguientes metodos heredados de la clase AbstractModel segun la funcionalidad
 * requerida.
 * e.g.
 *		...
 *		$modelAdapter = new ModelAdapter(); // por default adapter
 *		$modelAdapter->initialize(); // habilita la conexion a la BD
 *		....
 *		$modelAdapter2 = new ModelAdapter('menu'); // se creara un adaptador de Menu
 *		$modelAdapter2->init(); // carga los campos y atributos de la tabla Menu
 *		.....
 * Note que segun el parametro enviado se invoca al metodo necesario.
 */
include_once MODEL_DIR."/classes/abstractModel.class.php";
 
class ModelAdapter extends AbstractModel {
	
	public function __construct($name = "adapter", $schema = "") {
		parent::__construct($name, $schema);
	}
	
	//execute('getAlumno', array(1, 'nombre'))
	public function executte($nameFunction, $values = array()) {
		$this->serverDatabase->executte($this->schema . "." . $nameFunction, $values);
		return $this->serverDatabase->isSuccess();
	}
}

?>