<?php

include_once "Controller.php";

class Almacen extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Almacen");
		$this->set_subtitle("Lista de Almacen");
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
	public function form($data = null, $prefix = "") {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		$data["prefix"] = $prefix;
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
		$this->datatables->setModel($this->almacen);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion','telefono','direccion'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Almacen'
			,'Telefono'
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
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Almacen");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		
		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idsucursal,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("seguridad.sucursal");
		$this->combobox->addItem($query->result_array());
		
		$data['sucursal'] = $this->combobox->getObject();
		
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->almacen->find($id);
		
		$this->set_title("Modificar Almacen");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		
		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idsucursal,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("seguridad.sucursal");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($data["idsucursal"]);
		
		$data['sucursal'] = $this->combobox->getObject();
		
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
		if(empty($fields["idalmacen"])) {
			$this->almacen->insert($fields);
		}
		else {
			$this->almacen->update($fields);
		}
		
		$this->response($this->almacen->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idalmacen'] = $id;
		$fields['estado'] = "I";
		$this->almacen->update($fields);
		
		$this->response($fields);
	}
	
}
?>