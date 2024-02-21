<?php

include_once "Controller.php";

class Empresa extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Empresa");
		$this->set_subtitle("Lista de Empresa");
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
		// $data["icons"] = $this->get_icons();
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model($this->controller);
		$this->load->library('datatables');

		$this->datatables->setModel($this->empresa);

		$this->datatables->where('estado', '=', 'A');//

		$this->datatables->setColumns(array('descripcion','ruc','direccion'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Razon Social'
			,'RUC'
			,'Direccion'
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
		// echo $script;exit;
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Empresa");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->empresa->find($id);
		
		$this->set_title("Modificar Empresa");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$this->empresa->text_uppercase(false);

		$fields = $this->input->post();
		
		if(empty($fields["ruc"])) {
			$this->exception("Ingrese el RUC de la empresa");
			return;
		}
		if(strlen($fields["ruc"]) != 11) {
			$this->exception("El RUC debe tener 11 caracteres");
			return;
		}
		
		$fields['controller']=$this->controller;
		$fields['accion']=__FUNCTION__;
		
		$fields['estado'] = "A";
		$fields['logo'] = imagen_upload('logo','./app/img/empresa/','default_logo.png',true);
		
		if(empty($fields["idempresa"])) {
			$this->empresa->insert($fields);
		}
		else {
			$this->empresa->update($fields);
		}
		
		// $this->response($this->empresa->get_fields());
		$this->response($fields);
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		// cambiamos de estado
		$fields['idempresa'] = $id;
		$fields['estado'] = "I";

		$fields['controller']=$this->controller;
		$fields['accion']=__FUNCTION__;

		$this->empresa->update($fields);
		
		$this->response($fields);
	}
}
?>