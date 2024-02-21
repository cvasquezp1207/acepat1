<?php

include_once "Controller.php";

class Reciboingreso extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Recibo Ingreso");
		$this->set_subtitle("Lista de Recibo Ingreso");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// $this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		
		if( ! isset($data["anulado"]))
			$data["anulado"] = false;
		
		// $data["idtipodocumento"] = '3';
		$data["idtipodocumento"] = $this->get_param("idrecibo_ingreso");
		$this->load_library('combobox');
		
		// combo moneda
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idmoneda"
				,"name"=>"idmoneda"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")->order_by("descripcion","DESC")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idmoneda"]) ) {
			$this->combobox->setSelectedOption($data["idmoneda"]);
		}
		$data["moneda"] = $this->combobox->getObject();
		// combo moneda
		
		// combo tipopago
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipopago"
				,"name"=>"idtipopago"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipopago, descripcion')->where("estado", "A")->where("mostrar_en_reciboingreso", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idtipopago"]) ) {
			$this->combobox->setSelectedOption($data["idtipopago"]);
		}
		$data["tipopago"] = $this->combobox->getObject();
		// combo tipopago

		// combo tipopago_MODAL
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipopago_modal"
				,"name"=>"idtipopago_modal"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipopago, descripcion')->where("estado", "A")->where("mostrar_en_reciboingreso", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idtipopago"]) ) {
			$this->combobox->setSelectedOption($data["idtipopago"]);
		}
		$data["tipopago_modal"] = $this->combobox->getObject();
		// combo tipopago_MODAL

		// combo tipo documento
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipodocumento_ref"
				,"name"=>"idtipodocumento_ref"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipodocumento, descripcion')->where("estado", "A")->where("mostrar_en_recibos", "S")->get("venta.tipo_documento");
		$this->combobox->addItem("");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idtipodocumento_ref"]) ) {
			$this->combobox->setSelectedOption($data["idtipodocumento_ref"]);
		}
		$data["tidocumento"] = $this->combobox->getObject();
		// combo tipo documento

		// combo tipo Recibo
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipo_recibo"
				,"name"=>"idtipo_recibo"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipo_recibo, descripcion')->where("estado", "A")->where("tipo", "I")->where("mostrar_en_recibo", "S")->get("credito.tipo_recibo");
		$this->combobox->addItem("","Seleccione...");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idtipo_recibo"]) ) {
			$this->combobox->setSelectedOption($data["idtipo_recibo"]);
		}
		$data["tipo_recibo"] = $this->combobox->getObject();
		// combo tipo Recibo

		// combo concepto Movimiento
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idconceptomovimiento"
				,"name"=>"idconceptomovimiento"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")->where("ver_reciboingreso", "S")->get("caja.conceptomovimiento");
		// $this->combobox->addItem("");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idconceptomovimiento"]) ) {
			$this->combobox->setSelectedOption($data["idconceptomovimiento"]);
		}
		$data["movimiento"] = $this->combobox->getObject();
		// combo concepto Movimiento

		// combo cuentasbancarias
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idcuentas_bancarias"
				,"name"=>"idcuentas_bancarias"
				,"class"=>"form-control input-xs"
				,"required"=>""
				,"type-name"=>"idcuentas_bancarias"
			)
		);
		$query = $this->db->select('idcuentas_bancarias, cuenta')->where("estado", "A")->where("idsucursal", $this->get_var_session("idsucursal"))->get("general.view_cuentas_bancarias");
		$this->combobox->addItem("");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idcuentas_bancarias"]) ) {
			$this->combobox->setSelectedOption($data["idcuentas_bancarias"]);
		}
		$data["cuentas_bancarias"] = $this->combobox->getObject();
		// combo cuentasbancarias

		// combo tarjeta
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtarjeta"
				,"name"=>"idtarjeta"
				,"class"=>"form-control input-xs"
				,"required"=>""
				,"type-name"=>"idtarjeta"
			)
		);
		$query = $this->db->select('idtarjeta, descripcion')->where("estado", "A")->get("general.tarjeta");
		//$this->combobox->addItem("");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idtarjeta"]) ) {
			$this->combobox->setSelectedOption($data["idtarjeta"]);
		}
		$data["tarjeta"] = $this->combobox->getObject();
		// combo tarjeta
		
		
		$es_nuevo = "true";
		if( isset($data["idreciboingreso"]) ) {
			$es_nuevo = "false";
		}
		
		$serie_re = "";
		if( isset($data["serie"]) ) {
			$serie_re = $data["serie"];
		}
		$this->js("<script>var _es_nueva = $es_nuevo;</script>", false);
		$this->js("<script>var _serie = '{$serie_re}';</script>", false);
		
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');//PARA CIENTE
		$this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		$this->css('plugins/iCheck/custom');
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('form/'.$this->controller.'/form');
		
		// formulario CLIENTE
		$this->load_controller("cliente");
		// $this->cliente_controller->load = $this->load;
		// $this->cliente_controller->db = $this->db;
		// $this->cliente_controller->session = $this->session;
		// $this->cliente_controller->combobox = $this->combobox;
		$data["form_cliente"] = $this->cliente_controller->form(null, "cli_", true);

		$this->js('form/cliente/modal');
		
		$data["modal_pago"] = $this->get_form_pago("reciboingreso", false);
		$data["controller"] = $this->controller;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		// $this->load_model($this->controller);
		$this->load_model('reciboingreso_view');
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->reciboingreso_view);
		$this->datatables->setIndexColumn("idreciboingreso");
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '<>', 'X');
		$this->datatables->where('canjeado', '=', 'N'); // recibos no canjeados, los canjeados son amortizaciones
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('idreciboingreso','recibo','cliente','concepto','fecha','monto'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Id','5%')
			,array('Recibo','9%')
			,array('Cliente','40%')
			,array('Concepto','32%')
			,array('Fecha','10%')
			,array('Monto','10%')
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		$this->datatables->setCallback('callbackRI');

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);
		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		// agregamos los css para el dataTables
		// $this->css('plugins/dataTables/dataTables.bootstrap');
		// $this->css('plugins/dataTables/dataTables.responsive');
		// $this->css('plugins/dataTables/dataTables.tableTools.min');
		
		// agregamos los scripts para el dataTables
		// $this->js('plugins/dataTables/jquery.dataTables');
		// $this->js('plugins/dataTables/dataTables.bootstrap');
		// $this->js('plugins/dataTables/dataTables.responsive');
		// $this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js($script, false);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Recibo Ingreso");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		// $this->load_model($this->controller);
		$this->load_model('reciboingreso_view');
		// $data = $this->reciboingreso->find($id);
		$data = $this->reciboingreso_view->find(array("idreciboingreso"=>$id));
		$data["anulado"] = ($this->reciboingreso_view->get("estado") != "A");

		$this->set_title("Modificar Recibo Ingreso");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		$this->load_model("tipo_documento");
		$esNuevRecibo = false;
		$idtipodocumento = $this->get_param("idrecibo_ingreso"); //(EN LA BASE 3= RECIBO INGRESO)
		
		$fields = $this->input->post();
		
		$fields['estado'] = "A";
		$fields['idsucursal'] = $this->get_var_session("idsucursal");
		$fields["idtipodocumento"] = $idtipodocumento;
		
		if(empty($fields['idtipodocumento_ref']))
			$fields['idtipodocumento_ref'] = null;

		$this->db->trans_start(); // inciamos transaccion

		if(empty($fields["idreciboingreso"])) {
			$fields['idusuario'] = $this->get_var_session("idusuario");
			$fields['fecha'] = date("Y-m-d");
			$fields['hora'] = date("H:i:s");
			if(empty($fields['canjeado']))
				$fields['canjeado'] = "N";
			$idreciboingreso = $this->reciboingreso->insert($fields);
			
			$esNuevRecibo = true;
			
		}else {
			$idreciboingreso = $fields["idreciboingreso"];
			$this->reciboingreso->update($fields);
			//aqui se coje la fecha del proceso
			//para saber cuando sue creado, y comparar con la fecha actual para ver si afecta a caja o no
			$temp = $this->reciboingreso->find($fields["idreciboingreso"]);//recojo el valor de la fecha
			$fields['fecha'] = $temp['fecha'];
		}
		
		// $fields["idtipodocumento"] = $idtipodocumento = '3';//(EN LA BASE 3= RECIBO INGRESO)
		// if ($fields["idtipopago"] == 3) {//SI ES EFECTIVO
			if($esNuevRecibo) {
				$datostipodoc = $this->tipo_documento->find($fields["idtipodocumento"]);
				if($datostipodoc["genera_correlativo"] == 'S') {
					$this->load_model("serie_documento");
					$datos_serie = $this->serie_documento->find(array("idsucursal"=>$fields["idsucursal"], 
						"idtipodocumento"=>$fields["idtipodocumento"], "serie"=>$fields["serie"]));
					$datos_serie["correlativo"] = $datos_serie["correlativo"] + 1;
					$this->serie_documento->update($datos_serie);
				}
			}else{
				$fields["tabla"] = "reciboingreso";
				$fields["idoperacion"] = $idreciboingreso;
				
				// cargamos el controlador
				// $this->load_controller("caja");
				
				// cargamos la libreria
				// $this->load->library('pay');
				// $this->pay->set_controller($this->caja_controller);
				// $this->pay->set_data($fields); // revisar metodo para verificar los datos necesarios
				
				// eliminamos el pago ingresado
				$this->load->library('pay');
				$this->pay->remove("reciboingreso", $idreciboingreso, $fields["idsucursal"]);
			}
			
			if( $fields["afecta_caja"] == 'S' ) {
				// datos necesarios para la libreria pay
				
				$monto_pago= $fields["monto_pagar"];
				$id_moneda = $fields["idmoneda"];
				unset($fields["idmoneda"]);
				$fields["descripcion"]			= strtoupper($fields['concepto']);
				$fields["referencia"]			= $fields['cliente'];
				$fields["tabla"]				= "reciboingreso";
				$fields["idoperacion"]			= $idreciboingreso;
				$fields["numero"]				= $fields["numero"];
				
				$fields["idmoneda"]				= $fields["id_moneda_cambio"]?$fields["id_moneda_cambio"]:$id_moneda;
				$fields["cambio_moneda"]		= $fields["tipocambio"]?$fields["tipocambio"]:1;
				$fields["monto_convertido_pay"]	= $fields["cambio_moneda"]*$monto_pago;//Monto para guardar en Deposito
				
				// if(!empty($fields['fecha'])){// si la fecha esta vacia, estamos editando
					// if( $fields['fecha'] == date("Y-m-d") ){// si la fecha que se hizo la operacion es igual a la fecha actual
						if(!isset($this->caja_controller)) {
							$this->load_controller("caja");
						}
						if(!isset($this->pay)) {
							$this->load->library('pay');
						}
						$this->pay->set_controller($this->caja_controller);
						$this->pay->set_data($fields);
						// $this->pay->entrada(true); // false si es salida, default true
						
						$this->pay->process();						
					// }
				// }
			}
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($this->reciboingreso->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id, $verificar_canje='S', $estado="X") {
		$this->load_model($this->controller);
		
		$this->reciboingreso->find($id);
		
		if($this->reciboingreso->get("canjeado") == "S" && $verificar_canje=='S') {
			$this->exception("Se han realizado amortizaciones con el recibo que esta por eliminar, 
				Elimine el recibo por el modulo de Pago de Letras o Amortizaciones");
			return false;
		}
		
		$this->db->trans_start();
		
		$post = $this->input->post();
		
		// anulamos el recibo de ingreso
		$fields['idreciboingreso'] = $id;
		$fields['estado'] = $estado;
		$fields["fecha_hora_anulacion"] = date("Y-m-d H:i:s");
		$fields["idusuario_anulacion"] = $this->get_var_session("idusuario");
		if( ! empty($post["motivo"]))
			$fields["motivo_anulacion"] = $post["motivo"];
		$this->reciboingreso->update($fields);
		
		// si la caja esta abierta, eliminamos el registro nomas, 
		// de lo contrario que hagan un recibo de egreso, si afecta caja
		$this->load_library('pay');
		$this->pay->remove_if_open("reciboingreso", $id, $this->reciboingreso->get('idsucursal'));
		
		$this->db->trans_complete();
		
		$this->response($fields);
	}
	
	public function anular() {
		$this->load_model("venta.reciboingreso");
		
		$post = $this->input->post();
		
		$this->reciboingreso->find($post["idreciboingreso"]);
		
		if($this->reciboingreso->get("estado") <> "A") {
			$this->exception("El Recibo de Ingreso ".$this->reciboingreso->get("serie")."-".
				$this->reciboingreso->get("numero")." se encuentra anulado");
			return false;
		}
		
		$this->eliminar($post["idreciboingreso"], "S", "I");
	}

	public function get_tarjeta(){
		$fields = $this->input->post();
		$fields['idsuc'] = $this->get_var_session("idsucursal");

		$this->load_model("movimiento_tarjeta");
		$data = $this->movimiento_tarjeta->find(array("idsucursal"=>$fields['idsuc'],"idoperacion"=>$fields['id'],"idtarjeta"=>$fields['idtablilla'],"tabla"=>$fields['tablilla']));
		$this->response($data);
	}

	public function get_deposito(){
		$fields = $this->input->post();
		$fields['idsuc'] = $this->get_var_session("idsucursal");

		/*$sql = "SELECT * FROM venta.movimiento_deposito WHERE idsucursal='{$fields['idsuc']}' AND estado='A' AND idoperacion='{$fields['id']}' AND idcuentas_bancarias='{$fields['idtablilla']}' ";
		$query = $this->db->query($sql);

		$this->response($query->result_array());*/

		$this->load_model("movimiento_deposito");
		$data = $this->movimiento_deposito->find(array("idsucursal"=>$fields['idsuc'],"idoperacion"=>$fields['id'],"idcuentas_bancarias"=>$fields['idtablilla'],"tabla"=>$fields['tablilla']));
		$this->response($data);
	}
	
	public function grilla_popup() {
		$this->load_model("venta.reciboingreso_canje_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->reciboingreso_canje_view);
		$this->datatables->setIndexColumn("idreciboingreso");
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		$this->datatables->where('idcliente', '=', $this->input->post("idcliente")); // ventas al credito
		$this->datatables->where('idmoneda', '=', $this->input->post("idmoneda")); // ventas al credito
		$this->datatables->where('canjeable', '=', 'S');
		$this->datatables->where('canjeado', '=', 'N');
		$this->datatables->setColumns(array('fecha','nrodoc','tipo_recibo','tipopago','monto','concepto'));
		$this->datatables->setPopup(true);
		$this->datatables->setCallback("callback_fecha_popup");
		
		$table = $this->datatables->createTable(array('Fecha','Nro.Doc.','Tipo recibo','Tipo pago','Monto','Concepto'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
}
?>