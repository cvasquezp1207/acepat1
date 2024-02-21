<?php

include_once "Controller.php";

class Comisiones extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Comisiones");
		//$this->set_subtitle("Lista de");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// $this->js('form/'.$this->controller.'/registrar');
		$this->js('<script>setTimeout(function() {deleteEvent();}, 1000);</script>', false);
		$this->js('<script>setTimeout(function() {addEvent();}, 2000);</script>', false);
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
		return $this->load->view($this->controller."/registrar", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model("comision.conf_comision_view");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->conf_comision_view);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		// $this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('nombre','empresa','sucursal','fecha_inicio', 'fecha_fin'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'NOMBRE'
			,'EMPRESA'
			,'SUCURSAL'
			,'FECHA INICIO'
			, 'FECHA FIN'
		);

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);
		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo($data = null, $prefix = "", $modal = false) {
		if( ! is_array($data))
			$data = array();
		
		$this->load_model("seguridad.sucursal");
		$sucursal = $this->sucursal->find($this->get_var_session("idsucursal"));
		
		$this->load->library('combobox');
		
		// combo empresa
		$query = $this->db->select('idempresa, descripcion')
			->where("estado", "A")->order_by("descripcion", "asc")
			->get("seguridad.empresa");
		
		$this->combobox->setAttr("id", "idempresa");
		$this->combobox->setAttr("name", "idempresa");
		$this->combobox->setAttr("class", "form-control input-sm");
		$this->combobox->addItem($query->result_array());
		if( ! empty($data['comision']['idempresa']))
			$this->combobox->setSelectedOption($data["comision"]['idempresa']);
		else
			$this->combobox->setSelectedOption($sucursal["idempresa"]);
		
		$data["empresa"] = $this->combobox->getObject();
		
		// combo sucursal
		$query = $this->db->select('idsucursal, descripcion')
			->where("estado", "A")->where("idempresa", $sucursal["idempresa"])
			->order_by("descripcion", "asc")->get("seguridad.sucursal");
		
		$this->combobox->setAttr("id", "idsucursal");
		$this->combobox->setAttr("name", "idsucursal");
		$this->combobox->removeItems();
		$this->combobox->addItem($query->result_array());
		if( ! empty($data['comision']['idsucursal']))
			$this->combobox->setSelectedOption($data['comision']['idsucursal']);
		else
			$this->combobox->setSelectedOption($sucursal["idsucursal"]);
		
		$data["sucursal"] = $this->combobox->getObject();
		
		$this->combobox->setSelectedOption(null);
		
		// combo marca
		$query = $this->db->select('idmarca, descripcion')->where("estado", "A")
			->order_by("descripcion", "asc")->get("general.marca");
		
		$this->combobox->setAttr("id", "idmarca");
		$this->combobox->setAttr("name", "idmarca");
		$this->combobox->removeItems();
		$this->combobox->addItem($query->result_array());
		// if( ! empty($data['comision']['idsucursal']))
			// $this->combobox->setSelectedOption($data['comision']['idsucursal']);
		
		$data["marca"] = $this->combobox->getObject();
		
		// combo rango dias
		$this->combobox->setAttr("id", "idrango");
		$this->combobox->setAttr("name", "idrango");
		$this->combobox->removeItems();
		$this->get_rangos(false);
		$data["rangodias"] = $this->combobox->getObject();
		
		// formulario
		$data["controller"] = $this->controller;
		$form = $this->load->view($this->controller."/registrar", $data, true);
		
		$this->css("plugins/chosen/chosen");
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/chosen/chosen.jquery");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('form/'.$this->controller.'/registrar');
		if( ! empty($data['rangos'])) {
			$this->js('<script>cargarDatos('.json_encode($data['rangos']).');</script>', false);
		}
		
		$this->set_title("Configurar datos comisi&oacute;n");
		$this->set_subtitle("");
		$this->set_content($form);
		$this->index("content");
	}
	
	public function get_rangos($response = TRUE) {
		$query = $this->db->select('dias_min, dias_max')->where("estado", "A")
			->order_by("dias_min", "asc")->get("comision.rango_comision");
		
		if( ! isset($this->combobox))
			$this->load->library('combobox');
		
		if($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				$i = $row["dias_min"].";".$row["dias_max"];
				$l = $row["dias_min"]." - ";
				if($row["dias_max"] == -1)
					$l .= "mas";
				else
					$l .= $row["dias_max"];
				
				$this->combobox->addItem($i, $l);
			}
		}
		
		if($response === TRUE)
			$this->response($this->combobox->getAllItems());
	}
	
	public function guardar_rango() {
		$post = $this->input->post();
		if(empty($post["dias_max"]))
			$post["dias_max"] = -1;
		$post["estado"] = "A";
		
		$this->load_model("comision.rango_comision");
		$this->rango_comision->save($post, false);
		
		$this->response($this->rango_comision->get_fields());
	}
	
	public function eliminar_rango() {
		$str = $this->input->post("id");
		list($min, $max) = explode(";", $str);
		$data["dias_min"] = $min;
		$data["dias_max"] = $max;
		
		$this->load_model("comision.rango_comision");
		$this->rango_comision->delete($data);
		
		$this->response($this->rango_comision->get_fields());
	}

		public function get_parametros($idsucursal, $anio = FALSE, $mes = FALSE, $return = FALSE) {
		// $idsucursal = $this->input->post("idsucursal");
		
		// obtenemos ultimos mes configurado
		if($anio === FALSE) {
			$sql = "select max(fecha_fin) as fecha from comision.param_comision where idsucursal=?";
			$query = $this->db->query($sql, array($idsucursal));
			$anio = $query->row()->fecha;
		}
		
		if($mes === FALSE && $anio !== false) {
			$sql = "select max(fecha_inicio) as fecha from comision.param_comision where idsucursal=? and fecha_fin=?";
			$query = $this->db->query($sql, array($idsucursal, $anio));
			$mes = $query->row()->fecha;
		}
		
		// obtenemos los datos de comision configurado
		$sql = "select pc.*, m.descripcion as marca
			from comision.param_comision pc
			join general.marca m on m.idmarca = pc.idmarca
			where pc.idsucursal = ? and pc.fecha_inicio = ? and pc.fecha_fin = ?
			order by pc.dias_min asc, marca";
		$query = $this->db->query($sql, array($idsucursal, $mes, $anio));
		
		if($return === TRUE)
			return $query->result_array();
		
		$this->response($query->result_array());


	}
	
	public function guardar_parametros() {
		$post = $this->input->post();
		// print_r($post);exit;
		// $datos["anio"] = (int) date("Y");
		// $datos["mes"] = (int) date("m");
		$datos["idsucursal"] = $post["idsucursal"];
		$datos["fecha_inicio"] = $post["fecha_inicio"];
		$datos["fecha_fin"] = $post["fecha_fin"];
		
		$this->load_model("comision.param_comision");
		
		// eliminamos datos almacenados
		$this->param_comision->delete($datos);
		$datos["nombre"] 	= $post["nombre"];
		
		if( ! empty($post["datos"])) {
			foreach($post["datos"] as $val) {
				list($min, $max) = explode(";", $val["rango"]);
				
				// $datos["nombre"] = $val["nombre"];
				$datos["idmarca"] = $val["idmarca"];
				$datos["comision"] = floatval($val["comision"]);
				$datos["dias_min"] = $min;
				$datos["dias_max"] = $max;
				
				$this->param_comision->insert($datos, false);
			}
		}
		
		$this->response($this->param_comision->get_fields());
	}
	/**
	 * Metodo para editar registro
	 */
	public function editar() {
		$post = $this->input->get();
		// print_r($post);return;
		$data = array();
		$this->load_model('comision.param_comision');
		$lista = $this->param_comision->get_rangos($post['idsucursal'], $post['fecha_inicio'], $post['fecha_fin']);
		
		$data['comision'] = (count($lista) > 0) ? $lista[0] : $lista;
		$data['rangos'] = $lista;
		
		$this->nuevo($data);
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("general.color");
		
		$fields = $this->input->post();
		$fields['estado'] = "A";
		if(empty($fields["idcolor"])) {
			if($this->color->exists(array("descripcion"=>$fields["descripcion"])) == false) {
				$this->color->insert($fields);
			}
			else {
				$this->exception("El color ".$fields["descripcion"]." ya existe");
			}
		}
		else {
			$this->color->update($fields);
		}
		
		$this->response($this->color->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("general.color");
		
		// cambiamos de estado
		$fields['idcolor'] = $id;
		$fields['estado'] = "I";
		$this->color->update($fields);
		
		$this->response($fields);
	}
	
	public function options() {
		$query = $this->db->where("estado", "A")
			->order_by("descripcion", "asc")
			->get("general.color");
		
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
				$html .= '<option value="'.$row->idcolor.'">'.$row->descripcion.'</option>';
			}
		}
		
		$this->response($html);
	}
	
	public function autocomplete() {
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idcolor, descripcion
			FROM general.color
			WHERE estado='A' and (descripcion ILIKE ?)
			ORDER BY descripcion
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
}
?>