<?php

include_once "Controller.php";

class Tipomovimiento extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Tipo Movimiento - Caja");
		$this->set_subtitle("Lista de Tipo Movimiento");
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
	public function form($data = null) {
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
		$this->load_model($this->controller);

		$this->load->library('datatables');


		$this->datatables->setModel($this->tipomovimiento);

		$this->datatables->setIndexColumn("idtipomovimiento");

		$this->datatables->where('estado', '=', 'A');

		$this->datatables->setColumns(array('descripcion','alias','orden'));

		$columnasName = array(
			array('Descripci&oacute;n','60%')
			,array('Alias','10%')
			,array('Orden','5%')
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
		$this->set_title("Registrar Tipo Movimiento - Caja");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);

		$data = $this->tipomovimiento->find($id);
		
		$this->set_title("Modificar Tipo Movimiento - Caja");
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
		if(empty($fields["idtipomovimiento"])) {
			$this->tipomovimiento->insert($fields);
		}
		else {
			$this->tipomovimiento->update($fields);
		}
		
		$this->response($this->tipomovimiento->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		// cambiamos de estado
		$fields['idtipomovimiento'] = $id;
		$fields['estado'] = "I";
		$this->tipomovimiento->update($fields);
		
		$this->response($fields);
	}
}
?>