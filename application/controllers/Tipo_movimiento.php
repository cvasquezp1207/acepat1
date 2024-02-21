<?php

include_once "Controller.php";

class Tipo_movimiento extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Tipo Movimiento - Almacen");
		$this->set_subtitle("Lista de Tipos de Movimiento");
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
		
		$this->load->library('combobox');
		$this->combobox->setAttr("id", "tipo");
		$this->combobox->setAttr("name", "tipo");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem("A", "ENTRADA | SALIDA");
		$this->combobox->addItem("E", "ENTRADA");
		$this->combobox->addItem("S", "SALIDA");
		if(isset($data["tipo"]))
			$this->combobox->setSelectedOption($data["tipo"]);
		$data["combo_tipo"] = $this->combobox->getObject();
		
		$data["controller"] = $this->controller;
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model($this->controller);
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->tipo_movimiento);
		
		$this->datatables->setColumns(array('tipo_movimiento','descripcion'));
		
		$table = $this->datatables->createTable(array('Id','Descripcion'));
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->js($script, false);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Tipo Movimiento - Almacen");
		$this->set_subtitle("");
		$this->set_content($this->form(array("readonly"=>"", "reg"=>"N")));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->tipo_movimiento->find($id);
		$data["readonly"] = "readonly";
		$data["reg"] = "E";
		
		$this->set_title("Modificar Tipo Movimiento - Almacen");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$post = $this->input->post();
		if(empty($post["correlativo"]))
			$post["correlativo"] = 1;
		
		if(empty($post["tipo_movimiento"]))
			$this->exception("Ingrese el campo Id");
		
		if($post["reg"] == "N") {
			if($this->tipo_movimiento->exists(array("tipo_movimiento"=>$post["tipo_movimiento"])) == true)
				$this->exception("Ya existe el tipo movimiento ".$post["tipo_movimiento"]);
			
			$this->tipo_movimiento->insert($post);
		}
		else {
			if(empty($post["edit_correlativo"]))
				unset($post["correlativo"]);
			
			$this->tipo_movimiento->update($post);
		}
		
		$this->response($this->tipo_movimiento->get_fields());
	}
	
	public function options() {
		$query = $this->db->where("estado", "A")
			->order_by("descripcion", "asc")
			->get("almacen.tipo_movimiento");
		
		$html = '';
		if($query->num_rows() > 0) {
			$fo = "true";
			if($this->input->post("first_option") == "false") {
				$fo = "false";
			}
			if($fo == "true") {
				$html .= '<option value=""></option>';
			}
			foreach($query->result() as $row) {
				$html .= '<option value="'.$row->tipo_movimiento.'">'.$row->descripcion.'</option>';
			}
		}
		
		$this->response($html);
	}
}
?>