<?php

include_once "Controller.php";

class Localidad extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento Localidad");
		$this->set_subtitle("");
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
		$this->load->library('combobox');
		
		$data["controller"] = $this->controller;
		$data["prefix"] = $prefix;
		
		// combo presentacion
		$this->combobox->setAttr("id","idubigeo");
		$this->combobox->setAttr("name","idubigeo");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idubigeo,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.ubigeosorsa");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idubigeo"]) ) {
			$this->combobox->setSelectedOption($data["idubigeo"]);
		}
		
		$data['ubigeosorsa'] = $this->combobox->getObject();
		
		$this->js("<script>var prefix_{$this->controller} = '".$data["prefix"]."';</script>", false);
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model("general.view_localidad");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->view_localidad);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('idzona'
			,'ruta'
			,'zona'
			,'estado'
			));
		$this->datatables->where('estado', '=', 'A');
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('ID','5%')
			,array('Ruta','40%')
			,array('Localidad','40%')
			,array('Estado','10%')
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
		$this->set_title("Registrar Localidad");
		$this->set_subtitle("");
		
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->set_title("Modificar Localidad");	

		$this->load_model("general.view_localidad");
		$data = $this->view_localidad->find(array("idzona"=>$id));		
		
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
		//if(empty($fields["idzona"])) {
			$this->zona1->insert($fields);
		//}
		//else {
			//$this->zona->update($fields);
		//}
		
		//$this->response($this->zona->get_fields());
	}
	  
	 
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idzona'] = $id;
		$fields['estado'] = "I";
		$this->cuentas_bancarias->update($fields);
		
		$this->response($fields);
	}
	
}
?>