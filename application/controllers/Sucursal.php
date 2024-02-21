<?php

include_once "Controller.php";

class Sucursal extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Sucursal");
		$this->set_subtitle("Lista de Sucursal");
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
		// $this->load_model($this->controller);
		$this->load_model('view_sucursal');
		$this->load->library('datatables');

		// $this->datatables->setModel($this->sucursal);
		$this->datatables->setModel($this->view_sucursal);
		
		$this->datatables->setIndexColumn("idsucursal");

		// $this->datatables->where('estado', '=', 'A');//

		$this->datatables->setColumns(array('empresa','descripcion','direccion','telefono'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Empresa'
			,'Descripci&oacuten'
			,'Direcci&oacuten'
			,'Telefono'
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
		$this->set_title("Registrar Sucursal");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		$this->combobox->setAttr("id","idempresa");
		$this->combobox->setAttr("name","idempresa");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idempresa,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("seguridad.empresa");
		$this->combobox->addItem("","Seleccione...");
		$this->combobox->addItem($query->result_array());
		
		$data['empresa'] = $this->combobox->getObject();
		
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->sucursal->find($id);
		
		$this->set_title("Modificar Sucursal");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		$this->combobox->setAttr("id","idempresa");
		$this->combobox->setAttr("name","idempresa");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idempresa,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("seguridad.empresa");
		$this->combobox->addItem("","Seleccione...");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($data["idempresa"]);
		
		$data['empresa'] = $this->combobox->getObject();
		
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$this->sucursal->text_uppercase(false);

		$fields = $this->input->post();
		$fields['estado'] = "A";
		
		// print_r($_POST);exit;
		if(empty($fields["idsucursal"])) {
			$this->sucursal->insert($fields);
		}
		else {
			$this->sucursal->update($fields);
		}
		
		$this->response($this->sucursal->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idsucursal'] = $id;
		$fields['estado'] = "I";
		$this->sucursal->update($fields);
		
		$this->response($fields);
	}
	
	public function options() {
		// combo sucursal
		$query = $this->db->select('idsucursal, descripcion')
			->where("estado", "A")->where("idempresa", $this->input->post("idempresa"))
			->order_by("descripcion", "asc")->get("seguridad.sucursal");
		
		$this->load->library('combobox');
		$this->combobox->addItem($query->result_array());
		$this->response($this->combobox->getAllItems());
	}
}
?>