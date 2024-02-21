<?php

include_once "Controller.php";

class Tipo_documento extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Tipo Documento");
		$this->set_subtitle("Lista de Tipos de Documento");
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
		$WHERE_AND 			= '';
		$WHERE_AND_EMPRE	= '';
		$sucursal_session 	= $this->get_var_session('idsucursal');
		$idperfil 			= $this->get_var_session('idperfil');
		$permisos = $this->get_permisos();
		if(!is_array($data)) {
			$data = array();
			$WHERE_AND.= " AND idsucursal='$sucursal_session' ";
			$WHERE_AND_EMPRE.=" AND idempresa IN(SELECT idempresa FROM seguridad.sucursal WHERE idempresa IS NOT NULL $WHERE_AND)";
		}
		
		
		$query = $this->db->query("	SELECT idempresa,descripcion empr 
									FROM seguridad.empresa 
									WHERE estado='A' $WHERE_AND_EMPRE;");
		$datos = $query->result_array();

		$arrDatos = array();

		foreach($datos as $arr) {
			$sql = "SELECT idsucursal,descripcion sede 
					FROM seguridad.sucursal 
					WHERE estado = 'A' 
					AND idempresa='{$arr['idempresa']}'
					$WHERE_AND ;";
			
			$arr_q = $this->db->query($sql);
			
			$arrDatos[$arr['empr']] = $arr_q->result_array();
		}
		
		$data["controller"] 		= $this->controller;
		$data["prefix"] 			= $prefix;
		$data["modal"] 				= $modal;
		$data["sucursal_session"] 	= $sucursal_session;
		$data["sucursal"] 			= $arrDatos;

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
		$this->datatables->setModel($this->tipo_documento);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('idtipodocumento', 'descripcion'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Id', '10%') // ancho de la columna
			,array('Descripci&oacute;n', '90%') // ancho de la columna
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
		$this->set_title("Registrar Tipo de Documento");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->tipo_documento->find($id);
		
		$this->set_title("Modificar Tipo de Documento");
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
		
		// alguas validaciones para la facturacion electronica
		if($fields["facturacion_electronica"] == "S") {
			if(empty($fields["codsunat"])) {
				$this->exception("Llene el campo Codigo Sunat para el tipo de documento.<br>
					Consulte el Anexo D, Anexo 8 - Catalogo de codigos de Sunat");
				return;
			}
		}
		
		$this->db->trans_start(); // inciamos transaccion
		
		$fields['estado'] = "A";
		
		if(empty($fields["idtipodocumento"])) {
			$idtipodocumento = $this->tipo_documento->insert($fields);
		} else {
			$idtipodocumento = $fields["idtipodocumento"];
			$this->tipo_documento->update($fields);
		}
		
		$this->db->query("DELETE FROM venta.serie_documento WHERE idtipodocumento='{$idtipodocumento}' AND idsucursal='{$fields['idsucursal']}';");
		
		$this->load_model('serie_documento');
		if (!empty($fields['correlativo'])) {
			foreach($fields["correlativo"] as $key=>$val) {
				if($fields["facturacion_electronica"] == "S") {
					if(strlen($fields['serie'][$key]) != 4) {
						$this->exception("La serie del tipo documento debe tener 4 caracteres.");
						return;
					}
				}
				$data1["idsucursal"] 		= $fields['idsucursal'];
				$data1["idtipodocumento"] 	= $idtipodocumento;
				$data1["serie"] 			= $fields['serie'][$key];
				$data1["correlativo"]		= $val;

				$this->serie_documento->insert($data1,false);
			}
		}else{// NUEVO
			//foreach($fields["correlativo"] as $key=>$val) {
				$data1["idsucursal"] 		= $fields['idsucursal'];
				$data1["idtipodocumento"] 	= $idtipodocumento;
				$data1["serie"] 			= 1;
				$data1["correlativo"]		= 1;
				
				$this->serie_documento->insert($data1,false);
			//}
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($this->tipo_documento->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idtipodocumento'] = $id;
		$fields['estado'] = "I";
		$this->tipo_documento->update($fields);
		
		$this->response($fields);
	}
	public function get_series() {
		$query = $this->db
			->where("idtipodocumento", $this->input->post("idtipodocumento"))
			->where("idsucursal", $this->get_var_session("idsucursal"))
			->order_by("serie", "asc")
			->get("venta.serie_documento");
		
		$query2 = $this->db->select("serie")
				->where("idtipodocumento", $this->input->post("idtipodocumento"))
				->where("idsucursal", $this->get_var_session("idsucursal"))
				->where("idusuario", $this->get_var_session("idusuario"))
				->get("seguridad.personal_configuration");
	
		// if($query2->num_rows() > 0) {
				$serial["joder"] = array(); // ¿que es esto? ¿variable array? si no es array porque le declaran array?
		foreach($query2->result() as $row2) {
				$serial["joder"] = ''.$row2->serie.'';
				
		}
// echo "<pre>"; print_r($serial["joder"]);echo "</pre>";
		$html = '';
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				

				if($serial["joder"]== $row->serie){
					$html .= '<option value="'.$row->serie.'" selected="selected">'.$row->serie.'</option >';
				}
				else{// $html .= '<option value="'.$row->serie.'">'.str_pad($row->serie, 3, "0", STR_PAD_LEFT).'</option>';
					$html .= '<option value="'.$row->serie.'" >'.$row->serie.'</option>';
				}
			}
		}
		
		$this->response($html);
	}
	//Funcion para recoger los datos para los formularios para las transacciones
	public function get_correlativo() {
		$post = $this->input->post();
		$post["idsucursal"] = $this->get_var_session("idsucursal");
		
		$this->load_model("serie_documento");
		$res = $this->serie_documento->find($post);
		$this->response($res);
	}

	//Funcion para recoger los correlativos para editar eliminar ,etc
	public function getCorrelativo() {
        //$tipodocumento = new Generic("tipodocumento", 'general');
        $fields = $this->input->post();
        if (empty($fields['idsucursal'])) {
        	$fields['idsucursal'] = 0;
        }

        if (empty($fields['id'])) {
        	$fields['id'] = 0;
        }

        $this->load_model($this->controller);
        $tdoc = $this->tipo_documento->find($fields['id']);

        $data = array(
                    'tipodocumento' => $tdoc
                    ,'detalle' => array()
        );

        
        $sql = "SELECT * FROM venta.serie_documento 
        		WHERE idtipodocumento='{$fields['id']}'  
        		AND idsucursal='{$fields['idsucursal']}' ;";
        $series = $this->db->query($sql);
        $data['detalle'] = $series->result_array();

        $this->response($data);
    }
	
	public function get(){
		$fields = $this->input->post();
		
		if (empty($fields['idtipodocumento'])) {
        	$fields['idtipodocumento'] = 0;
        }
		
		$this->load_model($this->controller);
        $tdoc = $this->tipo_documento->find($fields['idtipodocumento']);
		
		$this->response($tdoc);
	}
}
?>