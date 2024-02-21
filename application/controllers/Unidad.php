<?php

include_once "Controller.php";

class Unidad extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Unida de Medida");
		$this->set_subtitle("Lista de unidades de medida");
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
		$this->datatables->setModel($this->unidad);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion', 'abreviatura'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Descripci&oacute;n', '65%') // ancho de la columna
			,array('Abreviatura', '30%') // ancho de la columna
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
		$this->set_title("Registrar Unidad de Medida");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->unidad->find($id);
		
		$this->set_title("Modificar Unidad de Medida");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		$this->unidad->text_uppercase(false);
		
		$fields = $this->input->post();
		
		if(empty($fields["codsunat"]) && $this->get_param("facturacion_electronica") == "S") {
			$this->exception("Ingrese el Codigo de la Unidad de Medida. Revisar el Anexo 8, Catalogo No. 03.");
			return;
		}
		
		$fields['descripcion'] = strtoupper($fields["descripcion"]);
		$fields['estado'] = "A";
		if(empty($fields["idunidad"])) {
			$this->unidad->insert($fields);
		}
		else {
			$this->unidad->update($fields);
		}
		
		$this->response($this->unidad->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idunidad'] = $id;
		$fields['estado'] = "I";
		$this->unidad->update($fields);
		
		$this->response($fields);
	}
	
	public function options() {
		$query = $this->db->where("estado", "A")
			->order_by("descripcion", "asc")
			->get("compra.unidad");
		
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
				$html .= '<option value="'.$row->idunidad.'">'.$row->descripcion.'</option>';
			}
		}
		
		$this->response($html);
	}
	
	public function get_all() {
		$query = $this->db->where("estado", "A")
			->order_by("descripcion", "asc")
			->get("compra.unidad");
		$this->response($query->result_array());
	}
}
?>