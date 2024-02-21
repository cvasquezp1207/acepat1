<?php

include_once "Controller.php";

class Cuentas_bancarias extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento Cuentas Bancarias");
		$this->set_subtitle("Lista de Cuentas Bancarias");
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
		
		$this->js("<script>var prefix_{$this->controller} = '".$data["prefix"]."';</script>", false);
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model("general.view_cuentas_bancarias");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->view_cuentas_bancarias);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('nro_cuenta','moneda','sucursal','banco'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Nro de Cuenta','15%')
			,array('Moneda','10%')
			,array('Sucursal','30%')
			,array('Banco','40%')
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
		$this->set_title("Registrar Modelo");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		$this->combobox->setAttr("id","idbanco");
		$this->combobox->setAttr("name","idbanco");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idbanco,banco');
		$query = $this->db->where("estado","A")->order_by("banco")->get("general.banco");
		$this->combobox->addItem($query->result_array());
		
		$data['banco'] = $this->combobox->getObject();
		$this->combobox->init();

		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idsucursal,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("seguridad.sucursal");
		$this->combobox->addItem($query->result_array());
		
		$data['sucursal'] = $this->combobox->getObject();
		$this->combobox->init();

		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idmoneda,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		// $this->combobox->setSelectedOption($data["idmoneda"]);
		
		$data['moneda'] = $this->combobox->getObject();
		
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->cuentas_bancarias->find($id);
		
		$this->set_title("Modificar Cuentas Bancarias");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		$this->combobox->setAttr("id","idbanco");
		$this->combobox->setAttr("name","idbanco");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idbanco,banco');
		$query = $this->db->where("estado","A")->order_by("banco")->get("general.banco");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($data["idbanco"]);
		
		$data['banco'] = $this->combobox->getObject();
		$this->combobox->init();
		
		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idsucursal,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("seguridad.sucursal");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($data["idsucursal"]);
		
		$data['sucursal'] = $this->combobox->getObject();
		$this->combobox->init();
		
		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idmoneda,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($data["idmoneda"]);
		
		$data['moneda'] = $this->combobox->getObject();
		
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
		if(empty($fields["idcuentas_bancarias"])) {
			$this->cuentas_bancarias->insert($fields);
		}
		else {
			$this->cuentas_bancarias->update($fields);
		}
		
		$this->response($this->cuentas_bancarias->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idcuentas_bancarias'] = $id;
		$fields['estado'] = "I";
		$this->cuentas_bancarias->update($fields);
		
		$this->response($fields);
	}
	
}
?>