<?php

include_once "Controller.php";

class Tipopago extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Tipo Pago");
		$this->set_subtitle("Lista de Tipos de Pago");
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
		// cargamos el modelo y la libreria
		$this->load_model($this->controller);
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->tipopago);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Descripci&oacute;n'
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
		$this->set_title("Registrar Tipo Pago");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->tipopago->find($id);
		
		$this->set_title("Modificar Tipo Pago");
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

		if (!isset($fields['mostrar_en_compra'])) {
			$fields['mostrar_en_compra'] = 'N';
		}else{
			$fields['mostrar_en_compra'] = 'S';
		}

		if (!isset($fields['mostrar_en_venta'])) {
			$fields['mostrar_en_venta'] = 'N';
		}else{
			$fields['mostrar_en_venta'] = 'S';
		}

		if (!isset($fields['mostrar_en_reciboingreso'])) {
			$fields['mostrar_en_reciboingreso'] = 'N';
		}else{
			$fields['mostrar_en_reciboingreso'] = 'S';
		}

		if (!isset($fields['mostrar_en_reciboegreso'])) {
			$fields['mostrar_en_reciboegreso'] = 'N';
		}else{
			$fields['mostrar_en_reciboegreso'] = 'S';
		}
		
		if (!isset($fields['mostrar_en_pagoproveedor'])) {
			$fields['mostrar_en_pagoproveedor'] = 'N';
		}else{
			$fields['mostrar_en_pagoproveedor'] = 'S';
		}
		
		if(empty($fields["idtipopago"])) {
			$this->tipopago->insert($fields);
		}
		else {
			$this->tipopago->update($fields);
		}
		
		$this->response($this->tipopago->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idtipopago'] = $id;
		$fields['estado'] = "I";
		$this->tipopago->update($fields);
		
		$this->response($fields);
	}
	
	/*public function options() {
		$query = $this->db->where("estado", "A")
			->order_by("descripcion", "asc")
			->get("compra.tipo_compra");
		
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
				$html .= '<option value="'.$row->idtipocompra.'">'.$row->descripcion.'</option>';
			}
		}
		
		$this->response($html);
	}*/
}
?>