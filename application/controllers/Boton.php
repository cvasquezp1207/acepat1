<?php

include_once "Controller.php";

class Boton extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Boton");
		$this->set_subtitle("Lista de Boton");
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

	// public function form($data = null, $prefix = "", $modal = false) {
	public function form($data = null, $prefix = "", $modal=false) {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		$data["modal"] = $modal;
		$data["prefix"] = $prefix;
		$data["icons"] = $this->get_icons();
		
		$this->js("<script>var prefix_boton = '{$prefix}'; </script>", false);
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function form_asign($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		return $this->load->view($this->controller."/form_asign", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model($this->controller);
		$this->load->library('datatables');


		// indicamos el modelo al datatables
		$this->datatables->setModel($this->boton);
		// $this->datatables->setIndexColumn("idsistema");

		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');//

		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion','tipo','alias','icono'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Descripci&oacute;n'
			,'Tipo'
			,'Alias'
			,'Icono'
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		// $this->datatables->setCallback('callbackBoton');
		
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
		$this->set_title("Registrar Boton");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->boton->find($id);
		
		$this->set_title("Modificar Boton");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		

		$this->boton->text_uppercase(false);

		$fields = $this->input->post();
		$fields['estado'] = "A";
		if(empty($fields["idboton"])) {
			$this->boton->insert($fields);
		}
		else {
			$this->boton->update($fields);
		}
		
		$this->response($this->boton->get_fields());
	}

	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idboton'] = $id;

		$fields['estado'] = "I";
		$this->boton->update($fields);
		
		$this->response($fields);
	}

	public function get_icons() {		
		$this->load_controller("modulo");
		return $this->modulo_controller->get_icons();
	}
}
?>