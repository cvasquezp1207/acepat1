<?php

include_once "Controller.php";

class Transporte extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Empresas de tansporte");
		$this->set_subtitle("Lista de empresas de transporte");
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
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("general.transporte");
		
		$this->load->library('datatables');
		$this->datatables->setModel($this->transporte);
		$this->datatables->setIndexColumn("idtransporte");
		$this->datatables->where('estado', '=', "A");

		$columns = array("nombre"=>"Nombre / Razon social", "ruc"=>"RUC");
		$popup = $this->input->get("popup");
		
		$this->datatables->setColumns(array_keys($columns));
		if($popup == "S")
			$this->datatables->setPopup(true);
		
		$table = $this->datatables->createTable(array_values($columns));
		if($popup == "S")
			$script = "<script>".$this->datatables->createScript("", false)."</script>";
		else
			$script = "<script>".$this->datatables->createScript()."</script>";
		
		if($popup == "S") {
			$this->response($script.$table);
			return;
		}
		
		$this->js($script, false);

		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Transporte");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("general.transporte");

		$data = $this->transporte->find($id);
		
		$this->set_title("Modificar transporte");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("general.transporte");
		$fields = $this->input->post();
		$fields["estado"] = "A";
		
		if(empty($fields["idtransporte"])) {
			$fields["idtransporte"] = $this->transporte->insert($fields);
		}
		else {
			$this->transporte->update($fields);
		}
		
		$this->response($this->transporte->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("general.transporte");
		
		$fields['idtransporte'] = $id;
		$fields['estado'] = "I";
		
		$this->transporte->update($fields);
		
		$this->response($fields);
	}
}
?>