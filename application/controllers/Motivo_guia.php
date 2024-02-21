<?php

include_once "Controller.php";

class Motivo_guia extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		// $this->set_title("Mantenimiento de Lineas de productos");
		// $this->set_subtitle("Lista de lineas");
		$this->js('form/'.$this->controller.'/index');
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
		
		$this->load->library('combobox');
		
		// combo operacion
		$this->combobox->setAttr("id", "operacion");
		$this->combobox->setAttr("name", "operacion");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem("A", "ENTRADA | SALIDA");
		$this->combobox->addItem("I", "ENTRADA");
		$this->combobox->addItem("S", "SALIDA");
		if(isset($data["operacion"]))
			$this->combobox->setSelectedOption($data["operacion"]);
		$data["combo_operacion"] = $this->combobox->getObject(true);
		
		// combo tipo movimiento ingreso
		$query = $this->db->query("SELECT tipo_movimiento,descripcion FROM almacen.tipo_movimiento WHERE tipo IN ('E','A') ORDER BY 1");
		$this->combobox->setAttr("id", "ingreso_tipo_movimiento");
		$this->combobox->setAttr("name", "ingreso_tipo_movimiento");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->addItem($query->result_array());
		if(isset($data["ingreso_tipo_movimiento"]))
			$this->combobox->setSelectedOption($data["ingreso_tipo_movimiento"]);
		$data["combo_ingreso"] = $this->combobox->getObject(true);
		
		// combo tipo movimiento salida
		$query = $this->db->query("SELECT tipo_movimiento,descripcion FROM almacen.tipo_movimiento WHERE tipo IN ('S','A') ORDER BY 1");
		$this->combobox->setAttr("id", "salida_tipo_movimiento");
		$this->combobox->setAttr("name", "salida_tipo_movimiento");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->addItem($query->result_array());
		if(isset($data["salida_tipo_movimiento"]))
			$this->combobox->setSelectedOption($data["salida_tipo_movimiento"]);
		$data["combo_salida"] = $this->combobox->getObject(true);
		
		
		$data["controller"] = $this->controller;
		
		$this->js('form/'.$this->controller.'/form');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("almacen.motivo_guia");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->motivo_guia);
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->setColumns(array('idmotivo_guia','descripcion','operacion'));
		
		$table = $this->datatables->createTable(array('Id','Descripci&oacute;n','Tipo'));
		
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar nuevo motivo de guia");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("almacen.motivo_guia");
		$data = $this->motivo_guia->find($id);
		
		$this->set_title("Modificar motivo de guia");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("almacen.motivo_guia");
		
		$post = $this->input->post();
		$post["estado"] = "A";
		
		if(empty($post["mostrar_en_guia"]))
			$post["mostrar_en_guia"] = "N";
		if(empty($post["afecta_stock"]))
			$post["afecta_stock"] = "N";
		
		// config ingreso
		if(empty($post["ingreso_tipo_movimiento"]))
			$post["ingreso_tipo_movimiento"] = 0;
		if(empty($post["ingreso_buscar_guia"]))
			$post["ingreso_buscar_guia"] = "N";
		if(empty($post["ingreso_b_esta_sede"]))
			$post["ingreso_b_esta_sede"] = "N";
		if(empty($post["ingreso_b_otra_sede"]))
			$post["ingreso_b_otra_sede"] = "N";
		if(empty($post["ingreso_libre_item"]))
			$post["ingreso_libre_item"] = "N";
		
		// config salida
		if(empty($post["salida_tipo_movimiento"]))
			$post["salida_tipo_movimiento"] = 0;
		if(empty($post["salida_buscar_venta"]))
			$post["salida_buscar_venta"] = "N";
		if(empty($post["salida_buscar_compra"]))
			$post["salida_buscar_compra"] = "N";
		if(empty($post["salida_libre_item"]))
			$post["salida_libre_item"] = "N";
		
		if(empty($post["idmotivo_guia"])) {
			$this->motivo_guia->insert($post);
		}
		else {
			$this->motivo_guia->update($post);
		}
		
		$this->response($this->motivo_guia->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("idmotivo_guia");
		
		$fields['idmotivo_guia'] = $id;
		$fields['estado'] = "I";
		$this->motivo_guia->update($fields);
		
		$this->response($fields);
	}
}
?>