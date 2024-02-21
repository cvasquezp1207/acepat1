<?php

include_once "Controller.php";

class Reciboegreso extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Recibo Egreso");
		$this->set_subtitle("Lista de Recibo Egreso");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// $this->css('plugins/iCheck/custom');
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
		
		// $data["idtipodocumento"] = '4';
		$data["idtipodocumento"] = $this->get_param("idrecibo_egreso");
		$this->load->library('combobox');
		
		// combo moneda
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idmoneda"
				,"name"=>"idmoneda"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")->order_by("descripcion","DESC")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idmoneda"]) ) {
			$this->combobox->setSelectedOption($data["idmoneda"]);
		}
		$data["moneda"] = $this->combobox->getObject();
		
		
		/* combo moneda */
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"codmoneda"
				,"name"=>"codmoneda"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")->order_by("descripcion","DESC")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		
		$data["moneda_caja"] = $this->combobox->getObject();

		// combo tipo Recibo
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipo_recibo"
				,"name"=>"idtipo_recibo"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipo_recibo, descripcion')->where("estado", "A")->where("tipo", "E")->where("mostrar_en_recibo", "S")->get("credito.tipo_recibo");
		// $this->combobox->addItem("");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idtipo_recibo"]) ) {
			$this->combobox->setSelectedOption($data["idtipo_recibo"]);
		}
		$data["tipo_recibo"] = $this->combobox->getObject();
		// combo tipo Recibo
		
		// combo tipopago
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipopago"
				,"name"=>"idtipopago"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipopago, descripcion')->where("estado", "A")->where("mostrar_en_reciboegreso", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
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
				,"class"=>"form-control"
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
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipodocumento, descripcion')->where("estado", "A")->where("mostrar_en_recibo", "S")->get("venta.tipo_documento");
		$this->combobox->addItem("");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idtipodocumento_ref"]) ) {
			$this->combobox->setSelectedOption($data["idtipodocumento_ref"]);
		}
		$data["tidocumento"] = $this->combobox->getObject();
		
		// combo concepto Movimiento
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idconceptomovimiento"
				,"name"=>"idconceptomovimiento"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		// $query = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")->where("mostrar_en_recibo", "S")->get("caja.conceptomovimiento");
		// $query = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")->get("caja.conceptomovimiento");
		$query = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")->where("ver_reciboegreso", "S")->get("caja.conceptomovimiento");
		// $this->combobox->addItem("");
		$this->combobox->addItem($query->result_array());
		if( isset($data["idconceptomovimiento"]) ) {
			$this->combobox->setSelectedOption($data["idconceptomovimiento"]);
		}
		$data["movimiento"] = $this->combobox->getObject();

		// combo cuentasbancarias
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idcuentas_bancarias"
				,"name"=>"idcuentas_bancarias"
				,"class"=>"form-control"
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
				,"class"=>"form-control"
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
		//echo $this->combobox->getObject();
		$data["tarjeta"] = $this->combobox->getObject();
		// combo tarjeta
		
		$data["tipomov"]	= $this->tipomovimiento();
		$data["conceptos"]	= $this->conceptos();
		// $data["cierre"]	= $this->usa_cierredepostivo();
		$es_nuevo = "true";
		if( isset($data["idreciboegreso"]) ) {
			$es_nuevo = "false";
		}
		
		$serie_re = "";
		if( isset($data["serie"]) ) {
			$serie_re = $data["serie"];
		}
		$this->js("<script>var _es_nueva = $es_nuevo;</script>", false);
		$this->js("<script>var _serie = '{$serie_re}';</script>", false);
		$this->js("<script>var _current_sucursal = '{$this->get_var_session("idsucursal")}';</script>", false);
		
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('plugins/iCheck/icheck.min');
		$this->js("<script>$(document).ready(function () {
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
            });</script>", false);
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');//PARA CLIENTE
		$this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		$this->css('plugins/iCheck/custom');
		
		// formulario CLIENTE
		$this->load_controller("cliente");
		// $this->cliente_controller->load = $this->load;
		// $this->cliente_controller->db = $this->db;
		// $this->cliente_controller->session = $this->session;
		// $this->cliente_controller->combobox = $this->combobox;
		$data["form_cliente"] = $this->cliente_controller->form(null, "cli_", true);
		$this->js('form/cliente/modal');
		
		// formulario EMPLEADO
		$this->load_controller("usuario");
		// $this->usuario_controller->load = $this->load;
		// $this->usuario_controller->db = $this->db;
		// $this->usuario_controller->session = $this->session;
		// $this->usuario_controller->combobox = $this->combobox;
		$data["form_usuario"] = $this->usuario_controller->form(null, "usu_", true);
		$this->js('form/usuario/modal');


		$this->js('form/'.$this->controller.'/form');
		// $this->js('form/cliente/modal');
		
		$data["modal_pago"] = $this->get_form_pago("reciboegreso", false);
		$data["controller"] = $this->controller;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		// $this->load_model($this->controller);
		$this->load_model("venta.recibo_egreso_view");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->recibo_egreso_view);
		$this->datatables->setIndexColumn("idreciboegreso");
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '<>', 'X');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('idreciboegreso','nro_recibo','entidad','concepto','fecha','monto'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('ID','5%')
			,array('Recibo','9%')
			,array('Referencia','40%')
			,array('Concepto','32%')
			,array('Fecha','10%')
			,array('Monto','10%')
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		$this->datatables->setCallback('callbackRE');

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
		$this->set_title("Registrar Recibo Egreso");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		//$this->load_model($this->controller);
		$this->load_model('reciboegreso_view');
		$data = $this->reciboegreso_view->find(array("idreciboegreso"=>$id));
		$data["anulado"] = ($this->reciboegreso_view->get("estado") != "A");
		// print_r($data);
		$this->set_title("Modificar Recibo Egreso");
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
		
		$fields = $this->input->post();
		
		$fields['idcliente']=$fields['idpersona'];//AUDIT
		
		$fields['estado'] = "A";
		$fields['idsucursal'] = $this->get_var_session("idsucursal");
		if(empty($fields['idtipodocumento_ref']))
			$fields['idtipodocumento_ref'] = null;
		
		$this->db->trans_start(); // inciamos transaccion
		
		if($this->usa_cierredepostivo($fields['en_cierrecaja'])){
			$this->exception("Usted ya realizó un recibo de egreso con la moneda seleccionada para el cierre de de caja, elimine la anterior, para poder realizar la operación");
			return false;
		}
		
		if(empty($fields["idreciboegreso"])) {
			$fields['idusuario'] = $this->get_var_session("idusuario");
			$fields['fecha'] = date("Y-m-d");
			$fields['hora'] = date("H:i:s");
			$idreciboegreso = $this->reciboegreso->insert($fields);
			
			$esNuevRecibo = true;
			
		}else {
			$idreciboegreso = $fields["idreciboegreso"];
			$this->reciboegreso->update($fields);
		}
		
		// if ($fields["idtipopago"] == 3) {//SI ES EFECTIVO
			$fields["idtipodocumento"] = $idtipodocumento = $this->get_param("idrecibo_egreso");//(EN LA BASE 3= RECIBO EGRESO)
			$monto_pago= $fields["monto_pagar"];
			$id_moneda = $fields["idmoneda"];
			unset($fields["idmoneda"]);
			
			$fields["idmoneda"]				= $fields["id_moneda_cambio"]?$fields["id_moneda_cambio"]:$id_moneda;
			$fields["cambio_moneda"]		= $fields["tipocambio"]?$fields["tipocambio"]:$fields["cambio_moneda"];
			$fields["monto_convertido_pay"]	= $monto_pago*$fields["cambio_moneda"];//Monto para guardar en Deposito
			
			if($esNuevRecibo) {
				$datostipodoc = $this->tipo_documento->find($fields["idtipodocumento"]);
				if($datostipodoc["genera_correlativo"] == 'S') {
					$this->load_model("serie_documento");
					$datos_serie = $this->serie_documento->find(array("idsucursal"=>$fields["idsucursal"],	"idtipodocumento"=>$fields["idtipodocumento"], "serie"=>$fields["serie"]));
					$datos_serie["correlativo"] = $datos_serie["correlativo"] + 1;
					$this->serie_documento->update($datos_serie);
				}

				$fields['monto'] = ($fields['monto']) * (-1);

				// $this->load_controller("caja");
				// $this->caja_controller->idusuario = $fields['idusuario'];
				// $this->caja_controller->idsucursal = $this->get_var_session("idsucursal");
				// $this->caja_controller->db = $this->db;
				// $this->caja_controller->egresoCaja($fields['idconceptomovimiento']
					// , $fields['monto']
					// , strtoupper($fields['concepto'])
					// , $fields['cliente']
					// , $this->controller
					// , $idreciboegreso
					// , $fields['idmoneda']
					// , $fields["tipocambio"]
					// , $idtipodocumento
					// , $fields['idpersona'] 
					// , $fields["serie"]
					// , $fields["numero"] 
					// , $this->get_var_session("idsucursal")
					// , $fields["idtipopago"] );

			}else{				
				$fields["tabla"] = "reciboegreso";
				$fields["idoperacion"] = $idreciboegreso;				
				// cargamos el controlador
				$this->load_controller("caja");
				// $this->caja_controller->db = $this->db;
				
				// cargamos la libreria
				$this->load->library('pay');
				$this->pay->set_controller($this->caja_controller);
				$this->pay->set_data($fields); // revisar metodo para verificar los datos necesarios
				$this->pay->remove("reciboegreso",$fields['idoperacion'],$fields["idsucursal"]);
			}
			
			if( $fields["afecta_caja"] == 'S' ) {
				// datos necesarios para la libreria pay
				$fields["descripcion"] = strtoupper($fields['concepto']);
				$fields["referencia"] = $fields['cliente'];
				$fields["tabla"] = "reciboegreso";
				$fields["idoperacion"] = $idreciboegreso;
				$fields["numero"] = $fields["numero"];
				// $fields["idmoneda"] = $fields["id_moneda_cambio"]?$fields["id_moneda_cambio"]:$id_moneda;
				// $fields["cambio_moneda"] = $fields["tipo_cambio_vigente"]?$fields["tipo_cambio_vigente"]:$fields["cambio_moneda"];
				// $fields["monto_pagar"] = $fields["monto_convertido_pay"]?$fields["monto_convertido_pay"]:$monto_pago;
				
				if(!isset($this->caja_controller)) {
					$this->load_controller("caja");
				}
				if(!isset($this->pay)) {
					$this->load->library('pay');
				}
				$this->pay->set_controller($this->caja_controller);
				$this->pay->set_data($fields);
				$this->pay->entrada(false); // false si es salida, default true
				$this->pay->process();
			}
		
		$res = $this->reciboegreso->get_fields();

		if($res['en_cierrecaja'] == 'S' && $res['fecha'] == date('Y-m-d')){
			if(!isset($this->caja_controller))
				$this->load_controller("caja");
			$caja = $this->caja_controller->getCajaActive('',$this->get_var_session("idusuario"));
			
			if($caja !== false) {
				$this->db->query("UPDATE caja.detalle_caja SET en_deposito='S' WHERE idcaja={$caja[0]['idcaja']} AND idmoneda='{$res['idmoneda']}' AND idconceptomovimiento IN('".implode("','", $fields['id_conceptomovimiento'])."');");
			}
		}
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($res);
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id, $estado="X") {
		$this->load_model($this->controller);
		
		$this->db->trans_start();
		
		$post = $this->input->post();
		
		// cambiamos de estado
		$fields['idreciboegreso'] = $id;
		$fields['estado'] = $estado;
		$fields["fecha_hora_anulacion"] = date("Y-m-d H:i:s");
		$fields["idusuario_anulacion"] = $this->get_var_session("idusuario");
		if( ! empty($post["motivo"]))
			$fields["motivo_anulacion"] = $post["motivo"];
		$this->reciboegreso->update($fields);
		
		/* si la caja esta abierta, eliminamos el registro nomas, de lo contrario que hagan un recibo de egreso, si afecta caja */
		$this->load_library('pay');
		$this->pay->remove_if_open("reciboegreso", $id, $this->get_var_session('idsucursal'));
		
		
		$this->reciboegreso->find($fields["idreciboegreso"]);
		$res = $this->reciboegreso->get_fields();
		if($res['en_cierrecaja'] == 'S' && $res['fecha'] == date('Y-m-d')){
			if(!isset($this->caja_controller))
				$this->load_controller("caja");
			$caja = $this->caja_controller->getCajaActive('',$this->get_var_session("idusuario"));
			
			if($caja !== false) {
				$sql = "UPDATE caja.detalle_caja SET en_deposito='N' WHERE idcaja={$caja[0]['idcaja']} AND idmoneda='{$res['idmoneda']}'";
				$this->db->query($sql);
			}
		}
		
		$this->db->trans_complete();
		
		$this->response($fields);
	}
	
	public function anular() {
		$this->load_model("venta.reciboegreso");
		
		$post = $this->input->post();
		
		$this->reciboegreso->find($post["idreciboegreso"]);
		
		if($this->reciboegreso->get("estado") <> "A") {
			$this->exception("El Recibo de Egreso ".$this->reciboegreso->get("serie")."-".
				$this->reciboegreso->get("numero")." se encuentra anulado");
			return false;
		}
		
		$this->eliminar($post["idreciboegreso"], "I");
	}
	
	public function restaurar($id) {
		$this->load_model("venta.reciboegreso");
		
		$this->reciboegreso->find($id);
		
		if($this->reciboegreso->get("estado") == "A") {
			$this->exception("El Recibo de Egreso ".$this->reciboegreso->get("serie")."-".
				$this->reciboegreso->get("numero")." se encuentra activo");
			return false;
		}
		
		$this->db->trans_start();
		
		$this->reciboegreso->update(array(
			"idreciboegreso" => $id
			,"motivo_anulacion" => null
			,"fecha_hora_anulacion" => null
			,"idusuario_anulacion" => null
			,"estado" => "A"
		));
		
		// si la caja esta abierta, eliminamos el registro nomas, 
		// de lo contrario que hagan un recibo de egreso, si afecta caja
		$this->load_library('pay');
		$this->pay->restore_if_open("reciboegreso", $id, $this->get_var_session('idsucursal'));
		
		$this->db->trans_complete();
		
		$this->response($fields);
	}
	
	public function tipomovimiento() {
		$sql = "SELECT*FROM caja.tipomovimiento WHERE estado='A' ORDER BY orden ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function conceptos() {
		$sql = "SELECT*FROM caja.conceptomovimiento WHERE estado='A' ORDER BY orden;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function usa_cierredepostivo($en_cierrecaja='N'){
		$sql = "SELECT count(*) c
				FROM venta.reciboegreso 
				WHERE fecha=CURRENT_DATE 
				AND estado='A' 
				AND en_cierrecaja='S' 
				AND idusuario='{$this->get_var_session("idusuario")}'";
		if(isset($_REQUEST['idmoneda']))
			$sql.=" AND idmoneda='{$this->input->post('idmoneda')}'";
		$q = $this->db->query($sql);
		$band = $q->row();

		// print($band->c);exit;
		if($band->c>0 && $en_cierrecaja=='S')
			return true;
		else
			return false;
	}
	
	public function get_resumen_c(){//Resumen de ingresos y egresos de caja
		$fields = $this->input->post();

		$sql = "SELECT COALESCE(SUM((CASE WHEN estado='A' THEN monto ELSE 0 END)),0) monto 
				FROM caja.detalle_caja 
				WHERE detalle_caja.idcaja 
				IN (SELECT idcaja FROM caja.caja WHERE estado='A' ";

		$sql.=$this->filtro($fields);
		// echo $sql;exit;
		$query = $this->db->query($sql);
		$this->response($query->row());
	}
	
	public function filtro($fields){
		$sql="";
		if (!empty($fields['idsucursal'])) {
			$sql.=" AND idsucursal='{$fields['idsucursal']}' ";
		}

		if (!empty($fields['idusuario'])) {
			$sql.=" AND idusuario_apertura='{$fields['idusuario']}' ";
		}

		if ( !isset($fields['idconceptomovimiento']) ) {
			$sql.=" )  ";
			if (!empty($fields['idtipomovimiento'])) {
				$sql.=" AND idconceptomovimiento IN (SELECT idconceptomovimiento FROM caja.conceptomovimiento WHERE idtipomovimiento ='{$fields['idtipomovimiento']}')";
			}
		}else{
			if ( !empty($fields['idconceptomovimiento']) ) {
				$sql.=" ) AND idconceptomovimiento='{$fields['idconceptomovimiento']}' ";
			}else{
				$sql.=" ) ";
			}
		}

		if (isset($fields['idmoneda'])) {
			$sql.=" AND idmoneda='{$fields['idmoneda']}'";
		}

		if ( isset($fields['idtipopago']) && !empty($fields['idtipopago']) ) {
			$id_tipopago = explode(",",$fields['idtipopago']);
			$sql.=" AND idtipopago IN ('".implode("','", $id_tipopago)."') ";
		}else if(isset($fields['id_tipopago']) && !empty($fields['id_tipopago'])){
			$id_tipopago = explode(",",$fields['id_tipopago']);
			$sql.=" AND idtipopago IN ('".implode("','", $id_tipopago)."') ";
		}

		if (!empty($fields['fecha'])) {
			$sql.=" AND fecha='{$fields['fecha']}' ";
		}else{
			$sql.=" AND fecha=CURRENT_DATE";
		}

		if (!empty($fields['idusuario']) && $fields["idusuario"]!= 'null') {
			$sql.=" AND idusuario='{$fields['idusuario']}' ";
		}
		if (!empty($fields['iddetalle_caja'])) {
			$sql.=" AND iddetalle_caja='{$fields['iddetalle_caja']}' ";
		}
		return $sql."";
	}
}
?>