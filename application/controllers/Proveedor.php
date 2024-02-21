<?php

include_once "Controller.php";

class Proveedor extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Proveedor");
		// $this->set_subtitle("Lista de Clientes");
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
		$this->load_model("proveedor");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->proveedor);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('nombre','ruc','direccion','telefono'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Nombre'
			,'RUC'
			,'Direccion'
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
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar proveedor");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("proveedor");
		$data = $this->proveedor->find($id);
		
		$this->set_title("Modificar proveedor");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("proveedor");
		
		$fields = $this->input->post();
		$fields['estado'] = "A";

		if(empty($fields["idproveedor"])) {
			$fields['fecha_registro'] = date("Y-m-d");
			$this->proveedor->insert($fields);
		}
		else {
			$this->proveedor->update($fields);
		}
		
		// $this->response($fields);
		$this->response($this->proveedor->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("proveedor");
		
		// cambiamos de estado
		$fields['idproveedor'] = $id;
		$fields['estado'] = "I";
		$this->proveedor->update($fields);
		
		$this->response($fields);
	}
	
	/**
	 * Metodo para llenar los autocomplete de proveedor
	 */
	public function autocomplete() {
		// $txt = $this->input->post("startsWith").'%';
		
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idproveedor, nombre, ruc
			FROM compra.proveedor
			WHERE estado='A' and (nombre ILIKE ? OR ruc ILIKE ?)
			ORDER BY nombre
			LIMIT ?";
			// echo $sql;
		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function grilla_popup() {
		$this->load_model($this->controller);
		// $this->load_model('venta.cliente_view');
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->proveedor);
		$this->datatables->setIndexColumn("idproveedor");
		$this->datatables->where('estado', '=', 'A');
		// $this->datatables->setColumns(array('idcliente','cliente','documento_cliente','tipo_cliente'));
		$this->datatables->setColumns(array('idproveedor','nombre','ruc'));
		$this->datatables->setPopup(true);
		// $this->datatables->setSubgrid("cargarDetalle", true);
		
		// $table = $this->datatables->createTable(array('Codigo','Cliente','Documento','Tipo'));
		$table = $this->datatables->createTable(array('Codigo','Razon Social','Ruc'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function grilla_popup_deuda(){
		$this->load_model("compra.proveedor_deudas_view");
		// $this->load_model('venta.cliente_view');
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->proveedor_deudas_view);
		$this->datatables->setIndexColumn("idproveedor");
		$this->datatables->where('estado', '=', 'A');
		// $this->datatables->where('cancelado', '=', 'N');
		// $this->datatables->setColumns(array('idcliente','cliente','documento_cliente','tipo_cliente'));
		$this->datatables->setColumns(array('idproveedor','proveedor','ruc'));
		$this->datatables->setPopup(true);
		// $this->datatables->setSubgrid("cargarDetalle", true);
		
		// $table = $this->datatables->createTable(array('Codigo','Cliente','Documento','Tipo'));
		$table = $this->datatables->createTable(array('Codigo','Razon Social','Ruc'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
}
?>