<?php

include_once "Controller.php";

class Tasacredito extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Tasa de Credito");
		$this->set_subtitle("Lista de Tasa de Credito");
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null, $prefix = "", $modal = false) {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model('view_tasacredito');
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->view_tasacredito);
		$this->datatables->setIndexColumn("idtasacredito");
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('to_char','porcentaje'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Descripci&oacute;n','80%')
			,array('Interes','20%')
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);
		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		// agregamos los css para el dataTables
		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/dataTables/dataTables.tableTools.min');
		
		// agregamos los scripts para el dataTables
		$this->js('plugins/dataTables/jquery.dataTables');
		$this->js('plugins/dataTables/dataTables.bootstrap');
		$this->js('plugins/dataTables/dataTables.responsive');
		$this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js($script, false);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Tasa de Credito");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->tasacredito->find($id);
		
		$this->set_title("Modificar Tasa de Credito");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$fields = $this->input->post();
		$fields['estado'] = "A";
		if(empty($fields["idtasacredito"])) {
			$this->tasacredito->insert($fields);
		}
		else {
			$this->tasacredito->update($fields);
		}
		
		$this->response($this->tasacredito->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idtasacredito'] = $id;
		$fields['estado'] = "I";
		$this->tasacredito->update($fields);
		
		$this->response($fields);
	}
}
?>