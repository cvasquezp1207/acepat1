<?php

include_once "Controller.php";

class Cuentas_cobrar extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Cuentas por Cobrar");
		$this->set_subtitle("Lista de Accesos");
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index_');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form() {
		$data["controller"] = $this->controller;
		
		$this->load->library('combobox');
		
		// combo tipo pago
		$query = $this->db->where("estado", "A")->where("mostrar_en_reciboingreso", "S")->get("venta.tipopago");
		
		$this->combobox->setAttr("id", "idtipopago_temp");
		$this->combobox->setAttr("name", "idtipopago_temp");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->addItem($query->result_array(), null, array("idtipopago", "descripcion"));
		$data["tipopago"] = $this->combobox->getObject();
		
		// combo estado de credito
		$query = $this->db->where("estado", "A")->get("credito.estado_credito");
		$this->combobox->init();
		$this->combobox->setAttr(array("name"=>"id_estado_credito", "id"=>"id_estado_credito", "class"=>"form-control"));
		$this->combobox->addItem($query->result_array(), null, array("id_estado_credito", "descripcion"));
		$data["estado_credito"] = $this->combobox->getObject();
		
		// dias del mes
		$dias = $this->get_param("dias_mes");
		if($dias == null) {
			$dias = 30;
		}
		$data["dias_mes"] = $dias;
		
		// valor de la mora
		$mora = $this->get_param("mora");
		if($mora == null) {
			$mora = 0;
		}
		$data["valor_mora"] = $mora;
		
		// id recibo ingreso
		$idrecibo_ingreso = $this->get_param("idrecibo_ingreso");
		if($idrecibo_ingreso == null) {
			throw new Exception("No existe el parametro <b>idrecibo_ingreso</b>");
		}
		
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc))
			$fc = 2;
		
		$data["modal_pago"] = $this->get_form_pago("reciboingreso");
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js("<script>var _idrecibo_ingreso_ = ".$idrecibo_ingreso.";</script>", false);
		$this->js("<script>var fixed_venta = ".$fc.";</script>", false);
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function index($tpl = "") {
		$data = array(
			"menu_title" => $this->menu_title
			,"menu_subtitle" => $this->menu_subtitle
			,"content" => $this->form()
			,"with_tabs" => $this->with_tabs
		);
		
		if($this->show_path) {
			$data['path'] = $this->get_path();
		}
		
		$str = $this->load->view("content_empty", $data, true);
		$this->show($str);
	}
	
	public function autocomplete() {
		$post = $this->input->post();
		$q = $post["q"].'%';
		
		$pagado='N';
		if(isset($post['pagado']))
			if($post['pagado']=='S')
				$pagado='S';
		
		if($post["f"] == "D") {
			$sql = "SELECT idcliente, null::integer as idcredito, dni as value, 
				dni||' | '||coalesce(nombres,'')||' '||coalesce(apellidos,'') as label
				FROM venta.cliente WHERE dni like ? AND idcliente IN (
					SELECT DISTINCT idcliente FROM credito.credito WHERE pagado='$pagado' AND estado='A' AND idsucursal='{$this->get_var_session('idsucursal')}'
				) ORDER BY label LIMIT ?";
		}
		else if($post["f"] == "R") {
			$sql = "SELECT idcliente, null::integer as idcredito, ruc as value, 
				ruc||' | '||coalesce(nombres,'')||' '||coalesce(apellidos,'') as label
				FROM venta.cliente WHERE ruc like ? AND idcliente IN (
					SELECT DISTINCT idcliente FROM credito.credito WHERE pagado='$pagado' AND estado='A' AND idsucursal='{$this->get_var_session('idsucursal')}'
				) ORDER BY label LIMIT ?";
		}
		else if($post["f"] == "N") {
			$sql = "SELECT idcliente, null::integer as idcredito, 
				coalesce(nombres,'')||' '||coalesce(apellidos,'') as label,
				coalesce(nombres,'')||' '||coalesce(apellidos,'') as value
				FROM venta.cliente 
				WHERE coalesce(nombres,'')||' '||coalesce(apellidos,'') ilike ? 
				AND idcliente IN (
					SELECT DISTINCT idcliente FROM credito.credito WHERE pagado='$pagado' AND estado='A' AND idsucursal='{$this->get_var_session('idsucursal')}'
				) ORDER BY label LIMIT ?";
		}
		else if($post["f"] == "A") {
			$sql = "SELECT idcliente, null::integer as idcredito, 
				coalesce(apellidos,'')||' '||coalesce(nombres,'') as label,
				coalesce(apellidos,'')||' '||coalesce(nombres,'') as value
				FROM venta.cliente 
				WHERE coalesce(apellidos,'')||' '||coalesce(nombres,'') ilike ? 
				AND idcliente IN (
					SELECT DISTINCT idcliente FROM credito.credito WHERE pagado='$pagado' AND estado='A' AND idsucursal='{$this->get_var_session('idsucursal')}'
				) ORDER BY label LIMIT ?";
		}
		else if($post["f"] == "C") {
			$sql = "SELECT c.idcliente, c.idcredito, c.nro_credito as value, 
				c.nro_credito||' | '||coalesce(l.nombres,'')||' '||coalesce(l.apellidos,'') as label
				FROM credito.credito c
				JOIN venta.cliente l ON l.idcliente = c.idcliente
				WHERE c.pagado='$pagado' AND c.estado='A' AND c.nro_credito like ?
				ORDER BY label LIMIT ?";
		}
		
		$query = $this->db->query($sql, array($q, $post["m"]));
		$this->response($query->result_array());
	}
	
	public function get_lista_credito($idcliente) {
		$this->load_model("credito");
		$this->response($this->credito->get_creditos_pendientes_($idcliente));
	}
	
	public function amortizar() {
		$post = $this->input->post();
		$post["tipo_recibo"] = "RI";
		
		// print_r($post);return true;
		$idtipodocumento = $this->get_param("idrecibo_ingreso");
		
		$this->load_model(array("credito.credito", "credito.letra", "credito.amortizacion", 
			"venta.reciboingreso", "venta.tipo_documento", "general.moneda","cobranza.liquidacion_visita","venta.cliente"));
		
		// buscamos datos
		$this->credito->find($post["idcredito"]);
		
		$this->cliente->find($this->credito->get("idcliente"));
		
		$idventa_ref = $this->credito->get("idventa");
		// verificamos si el credito no esta cancelado
		if($this->credito->get("pagado") == "S") {
			$this->exception("El credito ".$this->credito->get("nro_credito")." ya se ha cancelado");
			return;
		}
		
		// verificamos el recibo de ingreso
		/* $this->db->where("idtipodocumento", $idtipodocumento)->where("serie", $post["serie"])
			->where("numero", $post["correlativo"])->where("idsucursal", $this->get_var_session("idsucursal"));
		$query = $this->db->get("venta.reciboingreso");
		if($query->num_rows() > 0) {
			$this->exception("El recibo de ingreso ".$post["serie"]."-".$post["correlativo"]." ya se ha generado. 
				Haga doble clic en el numero de recibo de ingreso para generar un nuevo correlativo o vuelva abrir el modulo");
			return;
		} */
		if($this->has_comprobante("reciboingreso", $idtipodocumento, $post["serie"], $post["correlativo"])) {
			$this->exception("El recibo de ingreso ".$post["serie"]."-".$post["correlativo"]." ya se ha generado. 
				Haga doble clic en el numero de recibo de ingreso para generar un nuevo correlativo o vuelva abrir el modulo");
			return false;
		}
		
		$this->moneda->find($this->credito->get("idmoneda"));
		
		$this->db->trans_start();
		
		// datos para el recibo de ingreso
		$datosrecibo['controller'] = $this->controller;//AUDIT
		$datosrecibo['accion'] = __FUNCTION__;//AUDIT
		
		$datosrecibo["serie"] = $post["serie"];
		$datosrecibo["numero"] = $post["correlativo"];
		$datosrecibo["idcliente"] = $this->credito->get("idcliente");
		$datosrecibo["monto"] = $post["monto_pagar"];
		$datosrecibo["idmoneda"] = $this->credito->get("idmoneda"); // consultar sobre la moneda
		$datosrecibo["tipocambio"] = $this->moneda->get("valor_cambio");
		$datosrecibo["fecha"] = date("Y-m-d");
		$datosrecibo["hora"] = date("H:i:s");
		// $datosrecibo["concepto"] = "PAGO DE LETRA";
		$datosrecibo["idtipopago"] = $post["idtipopago"];
		$datosrecibo["idusuario"] = $this->get_var_session("idusuario");
		$datosrecibo["idsucursal"] = $this->get_var_session("idsucursal");
		$datosrecibo["tipo_ingreso"] = "PAGO DE LETRA"; // (OBSOLETO) creo que esto ya no es necesario
		$datosrecibo["idconceptomovimiento"] = $post["idconceptomovimiento"];
		$datosrecibo["idtipodocumento"] = $idtipodocumento;
		$datosrecibo["estado"] = "A";
		$datosrecibo["canjeado"] = "S";
		$datosrecibo["idtipo_recibo"] = 2; // pago de letra
		$datosrecibo["idcobrador"] = (!empty($post["idcobrador"])) ? $post["idcobrador"] : null; // Id cobrador, si es que se selecciona en el combo de cuentas por cobrar
		$this->reciboingreso->insert($datosrecibo);
		
		// recoremos las letras a amortizar
		if(!empty($post["idletra"])) {
			// datos amortizacion
			$datosamortizacion['controller']=$this->controller;//AUDIT
			$datosamortizacion['accion']=__FUNCTION__;//AUDIT
		
			$datosamortizacion["idsucursal"] = $datosrecibo["idsucursal"];
			$datosamortizacion["idtipo_pago"] = $datosrecibo["idtipopago"];
			$datosamortizacion["idrecibo_ingreso"] = $this->reciboingreso->get("idreciboingreso");
			$datosamortizacion["idusuario"] = $datosrecibo["idusuario"];
			$datosamortizacion["idcredito"] = $post["idcredito"];
			$datosamortizacion["tipo_recibo"] = $post["tipo_recibo"];
			$datosamortizacion["idmoneda"] = $datosrecibo["idmoneda"];
			$datosamortizacion["idtipodocumento"] = $datosrecibo["idtipodocumento"];
			$datosamortizacion["serie"] = $post["serie"];
			$datosamortizacion["numero"] = $post["correlativo"];
			$datosamortizacion["estado"] = "A";
			$datosamortizacion["fecha_pago"] = date("Y-m-d");
			
			foreach($post["idletra"] as $k=>$idletra) {
				// verificamos si la letra esta cancelado
				$datosletras = $this->letra->find(array("idletra"=>$idletra, "idcredito"=>$post["idcredito"]));
				if($datosletras["pagado"] == "S") {
					$this->exception("La letra ".$datosletras["nro_letra"]." se ha cancelado el dia ".date("d/m/Y", strtotime($datosletras["fecha_cancelado"])));
					return;
				}
				
				// ultima amortizacion
				$query = $this->db->select_max("idamortizacion")->where("idletra", $idletra)
					->where("idcredito", $post["idcredito"])->get("credito.amortizacion");
				$row = $query->row_array();
				
				// distribuimos el monto a amortizar entre la mora y la amortizacion
				$descuento = (empty($post["descuento"][$k])) ? 0 : floatval($post["descuento"][$k]);
				$saldo = floatval($post["saldo"][$k]) - $descuento; // saldo real
				$mora = floatval($post["moras"][$k]);
				$pagar = floatval($post["pagar"][$k]);
				
				if($pagar >= $mora) {
					$mora_amrtz = $mora;
					$monto_amrtz = $pagar - $mora_amrtz;
				}
				else {
					$mora_amrtz = $pagar;
					$monto_amrtz = $mora - $mora_amrtz;
				}
				
				// guardamos la amortizacion
				$datosamortizacion["idamortizacion"] = intval($row["idamortizacion"]) + 1;
				$datosamortizacion["idletra"] = $idletra;
				$datosamortizacion["monto"] = $monto_amrtz;
				$datosamortizacion["fecha_registro"] = date("Y-m-d H:i:s");
				$datosamortizacion["mora"] = $mora_amrtz;
				$this->amortizacion->insert($datosamortizacion, false);
				
				$saldo -= $monto_amrtz;
				$mora -= $mora_amrtz;
				
				// actualizamos los datos de la letra
				// actualizamos el descuento de la letra
				if($descuento > 0) {
					$datosletras["descuento"] = floatval($datosletras["descuento"]) + $descuento;
					$this->letra->update($datosletras);
				}
				// la letra se ha cancelado completamente
				if($mora <= 0 && $saldo <= 0) {
					$datosletras["fecha_cancelado"] = date("Y-m-d H:i:s");
					$datosletras["pagado"] = "S";
					$this->letra->update($datosletras);
				}
			}
		}
		
		// verificamos el estado del credito, si ya se ha cancelado
		$cancelados = $this->credito->letras_canceladas($post["idcredito"]);
		if($cancelados >= $this->credito->get("nro_letras")) {
			$this->credito->set("pagado", "S");
			$this->credito->update();
		}
		
		// ingresamos el dinero a caja
		// datos adicionales para el movimiento de caja
		$post["descripcion"] = "PAGO DE LETRAS";
		$post["referencia"] = $post['venta_comprobante'];
		$post["tabla"] = "reciboingreso";
		$post["idoperacion"] = $this->reciboingreso->get("idreciboingreso");
		$post["cambio_moneda"] = $datosrecibo["tipocambio"];
		// controlador de caja
		$this->load_controller("caja");

		// libreria para procesar el pago
		$this->load->library('pay');
		$this->pay->set_controller($this->caja_controller);
		$this->pay->set_data($post);
		$this->pay->set_data($datosrecibo);
		$this->pay->entrada(true);
		$this->pay->process();
		
		// actualizamos el correlativo del recibo de ingreso
		$this->update_correlativo($idtipodocumento, $post["serie"]);
		
		// $status = $this->verificar_visita_cobrador($post["idcredito"],$idventa_ref);
		if(!empty($datosrecibo["idcobrador"])){
			$tipo_liquidacion = 1;
			if($post['total_total']==$post['total_pagar']){
				$tipo_liquidacion = 2;
			}
			$liquidacion['id_recibo']		= $this->reciboingreso->get("idreciboingreso");
			$liquidacion['idcredito']		= $post["idcredito"];
			$liquidacion['idventa']			= $idventa_ref;
			$liquidacion['idcobrador']		= $datosrecibo["idcobrador"];
			$liquidacion['idusuario']		= $this->get_var_session("idusuario");
			$liquidacion['idcliente']		= $this->credito->get("idcliente");
			$liquidacion['idsucursal']		= $this->get_var_session("idsucursal");
			$liquidacion['idzona']			= $this->cliente->get("idzona");
			$liquidacion['idtipodocumento']	= $idtipodocumento;
			$liquidacion['serie']			= $post["serie"];
			$liquidacion['numero']			= $post["correlativo"];
			$liquidacion['idtipo_pago']		= $datosrecibo["idtipopago"];
			$liquidacion['tipo_liquidacion']= $tipo_liquidacion;// 1:amortizacion, 2: letra completa
			$liquidacion['importe']			= $post['total_pagar'];
			$liquidacion['fecha_liquidacion']= date('Y-m-d');
			$liquidacion['hora_liquidacion']= date('H:m:s');
			$liquidacion['estado']			= 'A';
			$this->liquidacion_visita->insert($liquidacion,false);
		}
		
		$this->db->trans_complete();
		
		$this->response($this->credito->get_fields());
	}
	
	protected function canjear_otro() {
		$this->response($this->input->post());
	}
	
	public function canjear() {
		$post = $this->input->post();
		
		if($post["tipo_recibo"] == "NC") {
			$this->canjear_notacredito();
			return;
		}
		else if($post["tipo_recibo"] != "RI") {
			$this->canjear_otro();
			return;
		}
		
		$this->load_model(array("credito.credito", "credito.letra", "credito.amortizacion", "venta.reciboingreso"));
		
		// buscamos datos
		$this->credito->find($post["idcredito"]);
		$datosrecibo = $this->reciboingreso->find($post["idrecibo"]);
		
		// verificamos el estado del recibo de ingreso
		if($datosrecibo["canjeado"] == "S") {
			$this->exception("El Recibo de Ingreso ".$this->reciboingreso->get("serie")."-".
				$this->reciboingreso->get("numero")." ya se ha canjeado");
			return;
		}
		
		// verificamos si el credito esta cancelado
		if($this->credito->get("pagado") == "S") {
			$this->exception("El credito ".$this->credito->get("nro_credito")." ya se ha cancelado");
			return;
		}
		
		$this->db->trans_start();
		
		// recoremos las letras a amortizar
		if(!empty($post["idletra"])) {
			// datos amortizacion
			$datosamortizacion['controller']=$this->controller;//AUDIT
			$datosamortizacion['accion']=__FUNCTION__;//AUDIT
			
			$datosamortizacion["idsucursal"] = $datosrecibo["idsucursal"];
			$datosamortizacion["idtipo_pago"] = $datosrecibo["idtipopago"];
			$datosamortizacion["idrecibo_ingreso"] = $datosrecibo["idreciboingreso"];
			$datosamortizacion["idusuario"] = $datosrecibo["idusuario"];
			$datosamortizacion["idcredito"] = $post["idcredito"];
			$datosamortizacion["tipo_recibo"] = $post["tipo_recibo"];
			$datosamortizacion["idmoneda"] = $datosrecibo["idmoneda"];
			$datosamortizacion["idtipodocumento"] = $datosrecibo["idtipodocumento"];
			$datosamortizacion["serie"] = $datosrecibo["serie"];
			$datosamortizacion["numero"] = $datosrecibo["numero"];
			$datosamortizacion["estado"] = "A";
			$datosamortizacion["fecha_pago"] = date("Y-m-d");
			
			foreach($post["idletra"] as $k=>$idletra) {
				// verificamos si la letra esta cancelado
				$datosletras = $this->letra->find(array("idletra"=>$idletra, "idcredito"=>$post["idcredito"]));
				if($datosletras["pagado"] == "S") {
					$this->exception("La letra ".$datosletras["nro_letra"]." se ha cancelado el dia ".date("d/m/Y", strtotime($datosletras["fecha_cancelado"])));
					return;
				}
				
				// ultima amortizacion
				$query = $this->db->select_max("idamortizacion")->where("idletra", $idletra)
					->where("idcredito", $post["idcredito"])->get("credito.amortizacion");
				$row = $query->row_array();
				
				// distribuimos el monto a amortizar entre la mora y la amortizacion
				$descuento = (empty($post["descuento"][$k])) ? 0 : floatval($post["descuento"][$k]);
				$saldo = floatval($post["saldo"][$k]) - $descuento; // saldo real
				$mora = floatval($post["moras"][$k]);
				$pagar = floatval($post["pagar"][$k]);
				
				if($pagar >= $mora) {
					$mora_amrtz = $mora;
					$monto_amrtz = $pagar - $mora_amrtz;
				}
				else {
					$mora_amrtz = $pagar;
					$monto_amrtz = $mora - $mora_amrtz;
				}
				
				// guardamos la amortizacion
				$datosamortizacion["idamortizacion"] = intval($row["idamortizacion"]) + 1;
				$datosamortizacion["idletra"] = $idletra;
				$datosamortizacion["monto"] = $monto_amrtz;
				$datosamortizacion["fecha_registro"] = date("Y-m-d H:i:s");
				$datosamortizacion["mora"] = $mora_amrtz;
				$this->amortizacion->insert($datosamortizacion, false);
				
				$saldo -= $monto_amrtz;
				$mora -= $mora_amrtz;
				
				// actualizamos los datos de la letra
				// actualizamos el descuento de la letra
				if($descuento > 0) {
					$datosletras["descuento"] = floatval($datosletras["descuento"]) + $descuento;
					$this->letra->update($datosletras);
				}
				// la letra se ha cancelado completamente
				if($mora <= 0 && $saldo <= 0) {
					$datosletras["fecha_cancelado"] = date("Y-m-d H:i:s");
					$datosletras["pagado"] = "S";
					// $datosletras["idtipo_pago"] = $datosrecibo["idtipopago"];//Puse esto, pero igual no graba
					$this->letra->update($datosletras);
				}
			}
		}
		
		// verificamos el estado del credito, si ya se ha cancelado
		$cancelados = $this->credito->letras_canceladas($post["idcredito"]);
		if($cancelados >= $this->credito->get("nro_letras")) {
			$this->credito->set("pagado", "S");
			$this->credito->update();
		}
		
		// actualizamos el estado del recibo de ingreso
		$this->reciboingreso->set("canjeado", "S");
		if(!empty($post['idcobrador'])){
			$this->reciboingreso->set("idcobrador", $post['idcobrador']);
			$this->reciboingreso->set("idcredito", $post['idcredito']);
			// Aqui no deberia ir tambien el idcredito en recibo egreso??
			$tipo_liquidacion = 1;
			if($post['total_total']==$post['total_pagar']){
				$tipo_liquidacion = 2;
			}
			$liquidacion['idcobrador']		= $datosrecibo["idcobrador"];
			$liquidacion['idusuario']		= $this->get_var_session("idusuario");
			$liquidacion['idcliente']		= $this->credito->get("idcliente");
			$liquidacion['idcredito']		= $post["idcredito"];
			$liquidacion['idsucursal']		= $this->get_var_session("idsucursal");
			$liquidacion['idventa']			= $idventa_ref;
			$liquidacion['idzona']			= $this->cliente->get("idzona");
			$liquidacion['idtipodocumento']	= $idtipodocumento;
			$liquidacion['serie']			= $post["serie"];
			$liquidacion['numero']			= $post["correlativo"];
			$liquidacion['idtipo_pago']		= $datosrecibo["idtipopago"];
			$liquidacion['tipo_liquidacion']= $tipo_liquidacion;// 1:amortizacion, 2: letra completa
			$liquidacion['importe']			= $post['total_pagar'];
			$liquidacion['fecha_liquidacion']= date('Y-m-d');
			$liquidacion['hora_liquidacion']= date('H:m:s');
			$liquidacion['estado']			= 'A';
			$this->liquidacion_visita->insert($liquidacion);
		}
		$this->reciboingreso->update();
		
		$this->db->trans_complete();
		
		$this->response($this->credito->get_fields());
	}
	
	protected function canjear_notacredito() {
		$post = $this->input->post();
		
		if($post["tipo_recibo"] != "NC") {
			$this->canjear_otro();
			return;
		}
		
		$this->load_model(array("credito.credito", "credito.letra", "credito.amortizacion", "venta.notacredito"));
		
		// buscamos datos
		$this->credito->find($post["idcredito"]);
		$datosrecibo = $this->notacredito->find($post["idrecibo"]);
		
		// verificamos el estado de la nota de credito
		if($datosrecibo["canjeado"] == "S") {
			$this->exception("La Nota de Credito ".$datosrecibo["serie"]."-".$datosrecibo["numero"]." ya se ha canjeado");
			return;
		}
		
		// verificamos si el credito esta cancelado
		if($this->credito->get("pagado") == "S") {
			$this->exception("El credito ".$this->credito->get("nro_credito")." ya se ha cancelado");
			return;
		}
		
		$this->db->trans_start();
		
		// recoremos las letras a amortizar
		if(!empty($post["idletra"])) {
			// datos amortizacion
			$datosamortizacion['controller']=$this->controller;//AUDIT
			$datosamortizacion['accion']=__FUNCTION__;//AUDIT
			
			$datosamortizacion["idsucursal"] = $datosrecibo["idsucursal"];
			// $datosamortizacion["idtipo_pago"] = $datosrecibo["idtipopago"]; // para nota de credito?
			$datosamortizacion["idnotacredito"] = $datosrecibo["idnotacredito"];
			$datosamortizacion["idusuario"] = $datosrecibo["idusuario"];
			$datosamortizacion["idcredito"] = $post["idcredito"];
			$datosamortizacion["tipo_recibo"] = $post["tipo_recibo"];
			$datosamortizacion["idmoneda"] = $datosrecibo["idmoneda"];
			$datosamortizacion["idtipodocumento"] = $datosrecibo["idtipodocumento"];
			$datosamortizacion["serie"] = $datosrecibo["serie"];
			$datosamortizacion["numero"] = $datosrecibo["numero"];
			$datosamortizacion["estado"] = "A";
			$datosamortizacion["fecha_pago"] = date("Y-m-d");
			
			foreach($post["idletra"] as $k=>$idletra) {
				// verificamos si la letra esta cancelado
				$datosletras = $this->letra->find(array("idletra"=>$idletra, "idcredito"=>$post["idcredito"]));
				if($datosletras["pagado"] == "S") {
					$this->exception("La letra ".$datosletras["nro_letra"]." se ha cancelado el dia ".date("d/m/Y", strtotime($datosletras["fecha_cancelado"])));
					return;
				}
				
				// ultima amortizacion
				$query = $this->db->select_max("idamortizacion")->where("idletra", $idletra)
					->where("idcredito", $post["idcredito"])->get("credito.amortizacion");
				$row = $query->row_array();
				
				// distribuimos el monto a amortizar entre la mora y la amortizacion
				$descuento = (empty($post["descuento"][$k])) ? 0 : floatval($post["descuento"][$k]);
				$saldo = floatval($post["saldo"][$k]) - $descuento; // saldo real
				$mora = floatval($post["moras"][$k]);
				$pagar = floatval($post["pagar"][$k]);
				
				if($pagar >= $mora) {
					$mora_amrtz = $mora;
					$monto_amrtz = $pagar - $mora_amrtz;
				}
				else {
					$mora_amrtz = $pagar;
					$monto_amrtz = $mora - $mora_amrtz;
				}
				
				// guardamos la amortizacion
				$datosamortizacion["idamortizacion"] = intval($row["idamortizacion"]) + 1;
				$datosamortizacion["idletra"] = $idletra;
				$datosamortizacion["monto"] = $monto_amrtz;
				$datosamortizacion["fecha_registro"] = date("Y-m-d H:i:s");
				$datosamortizacion["mora"] = $mora_amrtz;
				$this->amortizacion->insert($datosamortizacion, false);
				
				$saldo -= $monto_amrtz;
				$mora -= $mora_amrtz;
				
				// actualizamos los datos de la letra
				// actualizamos el descuento de la letra
				if($descuento > 0) {
					$datosletras["descuento"] = floatval($datosletras["descuento"]) + $descuento;
					$this->letra->update($datosletras);
				}
				// la letra se ha cancelado completamente
				if($mora <= 0 && $saldo <= 0) {
					$datosletras["fecha_cancelado"] = date("Y-m-d H:i:s");
					$datosletras["pagado"] = "S";
					// $datosletras["idtipo_pago"] = $datosrecibo["idtipopago"];//Puse esto, pero igual no graba
					$this->letra->update($datosletras);
				}
			}
		}
		
		// verificamos el estado del credito, si ya se ha cancelado
		$cancelados = $this->credito->letras_canceladas($post["idcredito"]);
		if($cancelados >= $this->credito->get("nro_letras")) {
			$this->credito->set("pagado", "S");
			$this->credito->update();
		}
		
		// actualizamos el estado de la nota de credito
		$this->notacredito->set("canjeado", "S");
		$this->notacredito->update();
		
		$this->db->trans_complete();
		
		$this->response($this->credito->get_fields());
	}
	
	public function guardar_datos() {
		$post = $this->input->post();
		
		$this->load_model("credito.credito");
		$this->credito->update($post);
		
		$this->response($post);
	}
	
	public function get_amortizaciones($id) {
		$sql = "select to_char(coalesce(i.fecha,a.fecha_pago),'DD/MM/YYYY') as fecha, 
			to_char(coalesce(i.hora::interval,to_char(a.fecha_registro,'HH24:MI:SS')::interval), 'HH12:MI am') as hora, 
			a.idletra, a.monto, a.mora, m.simbolo, m.descripcion as moneda, coalesce(t.descripcion,td.descripcion,'-') as tipo_pago, 
			td.abreviatura||'-'||a.serie||'-'||a.numero as recibo, u.nombres as usuario, s.descripcion as sucursal, 
			a.idrecibo_ingreso as id_ri, a.idnotacredito as id_nc, a.idsucursal, a.tipo_recibo
			,m.abreviatura moneda_corto
			from credito.amortizacion a
			left join venta.reciboingreso i on i.idreciboingreso = a.idrecibo_ingreso
			left join venta.tipopago t on t.idtipopago = i.idtipopago
			join venta.tipo_documento td on td.idtipodocumento = a.idtipodocumento
			join general.moneda m on m.idmoneda = a.idmoneda
			join seguridad.usuario u on u.idusuario = a.idusuario
			join seguridad.sucursal s on s.idsucursal = a.idsucursal
			where a.idcredito=? and a.estado='A'
			order by a.idletra, a.idamortizacion";
		$query = $this->db->query($sql, array($id));
		$res["array"] = $query->result_array();
		
		$sql = "select * from credito.amortizacion 
			where idcredito=? and idsucursal=? and estado=?
			order by idletra desc, idamortizacion desc limit 1";
		$query = $this->db->query($sql, array($id, $this->get_var_session("idsucursal"), 'A'));
		
		$last_recibo = array("tipo_recibo"=>"", "idnotacredito"=>"", "idrecibo_ingreso"=>"");
		if($query->num_rows() > 0)
			$last_recibo = $query->row_array();
		
		$res["last_recibo"] = $last_recibo;
		
		$this->response($res);
	}
	
	public function anular_recibo() {
		$post = $this->input->post();
		
		if($post["tipo"] == "NC") {
			$this->anular_notacredito();
			return;
		}
		
		$post["idsucursal"] = $this->get_var_session("idsucursal");
		
		$this->load_model(array("credito.credito"));
		$this->credito->find($post['idcredito']);
		
		$this->db->trans_start();
		
		// obtenemos las amortizaciones afectadas
		$query = $this->db->where('idcredito', $post['idcredito'])
			->where('idrecibo_ingreso', $post['idri'])
			->where('idsucursal', $post['idsucursal'])
			->where('estado', 'A')
			->get('credito.amortizacion');
		$rs = $query->result();
		
		$fields['estado'] = "I";
		$fields["fecha_hora_anulacion"] = date("Y-m-d H:i:s");
		$fields["idusuario_anulacion"] = $this->get_var_session("idusuario");
		$fields["motivo_anulacion"] = $post["motivo"];
		
		// anulamos las amortizaciones
		$this->load_model("credito.amortizacion");
		$this->db->where('idcredito', $post['idcredito'])
			->where('idrecibo_ingreso', $post['idri'])
			->where('idsucursal', $post['idsucursal'])
			->where('estado', 'A')
			->update('credito.amortizacion', $fields);
		
		// eliminamos el recibo de ingreso... o solo se deberia cambiar el estado del canje?
		// Version del gringo, ...yo no hice esto
		$this->load_model("venta.reciboingreso");
		$old_val = $this->reciboingreso->after_before_value_audit($this->reciboingreso,array($post['idri']), array('idreciboingreso'));
		$this->db->where('idreciboingreso', $post['idri'])->where('idsucursal', $post['idsucursal'])
			->update('venta.reciboingreso', $fields);
		$new_val = $this->reciboingreso->after_before_value_audit($this->reciboingreso,array($post['idri']), array('idreciboingreso'));
		$this->reciboingreso->insert_audit( $this->controller,'anular_recibo',$this->reciboingreso, '', array($post['idri']), $old_val , $new_val,'', array('idreciboingreso'));
		
		// si la caja esta abierta, eliminamos el registro nomas, 
		// de lo contrario que hagan un recibo de egreso, si afecta caja
		$this->load_library('pay');
		$this->pay->remove_if_open("reciboingreso", $post['idri'], $post['idsucursal']);
		
		//Probar llamado al controlador de recibo ingreso delente
		// Verision mio de mi
		/*
		$this->load_controller("reciboingreso");
		$this->reciboingreso_controller->eliminar($post['idri'],'N');
		*/
		
		// recorremos la lista de amortizaciones
		if( ! empty($rs)) {
			foreach($rs as $row) {
				// actualizamos el estado de las letras
				$this->db->where("idcredito", $post["idcredito"])
					->where("idletra", $row->idletra)
					->where("estado", "A")
					->update("credito.letra", array("pagado"=>"N"));
			}
			
			// actualizamos el estado del credito
			$sql = "SELECT count(*) FROM credito.letra
				WHERE idcredito=? AND estado=? AND pagado=?";
			$query = $this->db->query($sql, array($post["idcredito"], "A", "N"));
			
			$pagado = "N";
			if($query->num_rows() > 0) {
				$pagado = ($query->row()->count > 0) ? "N" : "S";
			}
			$this->db->where("idcredito", $post["idcredito"])
				->where("idsucursal", $post["idsucursal"])
				->update("credito.credito", array("pagado"=>$pagado));
		}
		//Examinar esta funcion para la cobranza y utlidad de los cobradores
		$this->destroy_liquidacion_visita($post["idcredito"],$this->credito->get("idventa"),$post['idri']);//Eliminamos la liquidacion de la visita, si existe
		$this->db->trans_complete();
		
		$this->response($post);
	}
	
	protected function anular_notacredito() {
		$post = $this->input->post();
		$post["idsucursal"] = $this->get_var_session("idsucursal");
		
		$this->load_model(array("credito.credito"));
		$this->credito->find($post['idcredito']);
		
		$this->db->trans_start();
		
		// obtenemos las amortizaciones afectadas
		$query = $this->db->where('idcredito', $post['idcredito'])
			->where('idnotacredito', $post['idnc'])
			->where('idsucursal', $post['idsucursal'])
			->where('estado', 'A')
			->get('credito.amortizacion');
		$rs = $query->result();
		
		$fields['estado'] = "I";
		$fields["fecha_hora_anulacion"] = date("Y-m-d H:i:s");
		$fields["idusuario_anulacion"] = $this->get_var_session("idusuario");
		$fields["motivo_anulacion"] = $post["motivo"];
		
		// anulamos las amortizaciones
		$this->db->where('idcredito', $post['idcredito'])
			->where('idnotacredito', $post['idnc'])
			->where('idsucursal', $post['idsucursal'])
			->where('estado', 'A')
			->update('credito.amortizacion', $fields);
		
		// acttualizamos la nota de credito... o se deberia eliminar?
		$this->db->where('idnotacredito', $post['idnc'])
			->where('idsucursal', $post['idsucursal'])
			->update('venta.notacredito', array('canjeado'=>'N'));
		
		// recorremos la lista de amortizaciones
		if( ! empty($rs)) {
			foreach($rs as $row) {
				// actualizamos el estado de las letras
				$this->db->where("idcredito", $post["idcredito"])
					->where("idletra", $row->idletra)
					->where("estado", "A")
					->update("credito.letra", array("pagado"=>"N"));
			}
			
			// actualizamos el estado del credito
			$sql = "SELECT count(*) FROM credito.letra
				WHERE idcredito=? AND estado=? AND pagado=?";
			$query = $this->db->query($sql, array($post["idcredito"], "A", "N"));
			
			$pagado = "N";
			if($query->num_rows() > 0) {
				$pagado = ($query->row()->count > 0) ? "N" : "S";
			}
			$this->db->where("idcredito", $post["idcredito"])
				->where("idsucursal", $post["idsucursal"])
				->update("credito.credito", array("pagado"=>$pagado));
		}
		
		$this->db->trans_complete();
		
		$this->response($post);
	}
	
	public function getLetrasPagos($idcredito) {
		// $model = new Generic('credito', 'venta');
		
		$id = intval($idcredito);
		
		$sql = "SELECT
				c.idventa
				,COALESCE(l.nombres,'')||COALESCE(l.apellidos,'') cliente
				,l.direccion_principal direccion
				,COALESCE(l.dni,l.ruc,'') dni
				,((SELECT (array_agg(telefono)) FROM venta.cliente_telefono cf WHERE cf.idcliente=c.idcliente)) telefono
				,c.nro_credito numerocredito
				,r.nombres recaudador
				,l.observacion referencia
				FROM credito.credito c
				JOIN venta.cliente l ON l.idcliente=c.idcliente
				LEFT JOIN cobranza.hoja_ruta h ON h.idcredito=c.idcredito
				LEFT JOIN seguridad.usuario r ON r.idusuario=h.idcobrador
				WHERE c.idcredito=$id;";

		$q=$this->db->query($sql);
		$arrCredito = $q->row_array();
		
		// obtenemos la lista de letras del credito
		$sql = "SELECT l.idletra
				,l.nro_letra letra
				,l.descripcion tipo_letra
				,to_char(l.fecha_vencimiento, 'DD/MM/YYYY') fecha_vencimiento
				,l.estado
				,l.monto_letra monto
				,(case when l.pagado = 'S' then coalesce(a.moras,0.00) else l.mora + coalesce(a.moras,0) end) as moras
				,coalesce(a.pago + a.moras,0) as pagos
				,coalesce(t.descripcion, '') as tipopago_credito
				,coalesce(to_char(l.fecha_cancelado, 'DD/MM/YYYY'), '') as fecha_amortizacion
				,a.moras moras_real
				,l.descuento
				,((monto_letra+(case when l.pagado = 'S' then coalesce(a.moras,0.00) else l.mora + coalesce(a.moras,0) end))-(coalesce(a.pago + a.moras,0))-l.descuento) saldito
				,coalesce(a2.serie, '') as serie
				,coalesce(a2.numero, null) as nro_recibo
				,td.abreviatura||'-'||coalesce(a2.serie||'-', '')||coalesce(a2.numero, null) recibo
				,l.pagado
				FROM credito.letra l
				LEFT JOIN venta.tipopago t ON t.idtipopago=l.idtipo_pago
				LEFT JOIN (select idletra, sum(mora) as moras, sum(monto) as pago, max(idamortizacion) as idamrzt
					from credito.amortizacion where estado<>'I' and idcredito='$id' group by idletra) a ON a.idletra = l.idletra
				LEFT JOIN credito.amortizacion a2 on a2.idamortizacion=a.idamrzt and a2.idletra=l.idletra and a2.idcredito='$id'
				LEFT JOIN venta.tipo_documento td on td.idtipodocumento=a2.idtipodocumento
				WHERE l.idcredito='$id'
				ORDER BY nro_letra;";
		
		$q=$this->db->query($sql);
		$rs = $q->result_array();
		
		$arrLetras = array();
		
		if(!empty($rs)) {
			foreach($rs as $arr) {
				$arrLetras[] = $arr;
			}
		}
		
		// obtenemos las amortizaciones de las letras, solo los que se realizaron en mas de uno
		$sql = "SELECT a.idletra
				,to_char(fecha_pago,'DD/MM/YYYY') as fecha_amortizacion
				,a.monto
				,a.mora as moras
				,a.monto+a.mora as total
				,serie
				,numero as nrorecibo
				,coalesce(t.descripcion,td.descripcion,'-') as tipopago_credito
				,td.abreviatura||'-'||coalesce(a.serie||'-', '')||coalesce(a.numero, null) as recibo
				FROM credito.amortizacion a
				JOIN venta.tipo_documento td ON td.idtipodocumento=a.idtipodocumento
				LEFT JOIN venta.tipopago t ON t.idtipopago=a.idtipo_pago
				WHERE a.estado<>'I' AND a.idcredito=$id 
				order by a.idletra, a.idamortizacion;";

		$q = $this->db->query($sql);
		$rs = $q->result_array();
		
		$arrAmrtz = array();
		$arrPago = array();
		
		if(!empty($rs)) {
			$temp = array();
			foreach($rs as $arr) {
				if(!array_key_exists($arr['idletra'], $arrAmrtz)) {
					$arrAmrtz[$arr['idletra']] = array();
					$arrPago[$arr['idletra']] = array();
				}
				
				$arrAmrtz[$arr['idletra']][] = $arr;
				$arrPago[$arr['idletra']][] = $arr['total'];
			}
		}
		
		// obtenemos los datos de la venta
		$sql = "SELECT 
				COALESCE(l.nombres,'')||COALESCE(l.apellidos,'') cliente
				,COALESCE(l.dni,l.ruc,'') dni
				,l.direccion_principal direccion
				,((SELECT (array_agg(telefono)) FROM venta.cliente_telefono cf WHERE cf.idcliente=l.idcliente)) telefono
				,e.nombres vendedor
				,t.abreviatura||' '||v.serie||'-'||v.correlativo comprobante
				,p.descripcion producto
				,m.descripcion marca
				,((SELECT (array_agg(serie)) FROM venta.detalle_venta_serie dvs WHERE dvs.idventa=v.idventa)) serie
				FROM venta.detalle_venta d
				JOIN venta.venta v ON v.idventa=d.idventa
				JOIN compra.producto p ON p.idproducto=d.idproducto
				JOIN general.marca m ON m.idmarca=p.idmarca
				JOIN venta.tipo_documento t ON t.idtipodocumento=v.idtipodocumento
				JOIN venta.cliente l ON l.idcliente=v.idcliente
				JOIN seguridad.usuario e ON e.idusuario=v.idvendedor
				WHERE v.idventa='{$arrCredito['idventa']}';";

		$q=$this->db->query($sql);
		$arrVenta = $q->result_array();
		
		$response = array('credito'=>$arrCredito, 'letras'=>$arrLetras, 'amortizaciones'=>$arrAmrtz, 'pagos'=>$arrPago, 'venta'=>$arrVenta);
		
		return $response;
	}
	
	public function imprimir(){
		if(is_numeric($_REQUEST['idcredito'])){
			$res = $this->getLetrasPagos($_REQUEST['idcredito']);
			// echo "<pre>";
			// print_r($res);exit;
			$credito = $res['credito'];
			$venta = $detalle = $res['venta'];
			$letras = $res['letras'];
			$amortizaciones = $res['amortizaciones'];
			
			
			$this->load->library("pdf");
			
			$this->load_model(array( "seguridad.empresa","seguridad.usuario","credito.estado_credito"));
		
			$this->empresa->find($this->get_var_session("idempresa"));
			$logo = ver_fichero_valido($this->empresa->get("logo"),FCPATH."app/img/empresa/");
			if( !empty($logo) )
				$this->pdf->SetLogo($logo);
			$this->pdf->SetTitle(utf8_decode("CRONOGRAMA DE PAGOS"), 11, null, true);
			
			$this->pdf->AliasNbPages(); // para el conteo de paginas
			$this->pdf->SetLeftMargin(2);

			$this->pdf->AddPage();
			$this->pdf->SetFont('Arial','',9);
			

			$this->pdf->Cell(45,3,$this->empresa->get("descripcion"),0,0,'L');
			$this->pdf->Cell(126,3,date('d/m/Y'),0,0,'R');
			$this->pdf->Cell(20,3,date('H:i:s a'),0,1,'R');
			$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
			$this->pdf->Ln(5);
			
			$this->pdf->SetFont('Arial','B',8.5);
			$this->pdf->Cell(25,5,'SR(es):',0,0,'L');
			$this->pdf->SetFont('Arial','',8.5);
			$this->pdf->Cell(76,5,utf8_decode($credito["cliente"]),0,0,'L');
			$this->pdf->SetFont('Arial','B',8.5);
			$this->pdf->Cell(16,5,'DNI/RUC:',0,0,'L');
			$this->pdf->SetFont('Arial','',8.5);
			$this->pdf->Cell(24,5,$credito['dni'],0,0,'L');
			if(!isset($venta[0]['vendedor']) ) {
				$venta[0]['vendedor'] = '';
			}
			$this->pdf->SetFont('Arial','B',8.5);
			$this->pdf->Cell(20,5,'VENDEDOR:',0,0,'L');
			$this->pdf->SetFont('Arial','',8.5);
			$this->pdf->Cell(40,5,$venta[0]['vendedor'],0,1,'L');
			
			$this->pdf->SetFont('Arial','B',8.5);
			$this->pdf->Cell(25,5,utf8_decode('TELEF.:'),0,0,'L');
			$this->pdf->SetFont('Arial','',8.5);
			$this->pdf->Cell(115,5,$credito['telefono'],0,0,'L');
			$this->pdf->SetFont('Arial','B',8.5);
			$this->pdf->Cell(20,5,utf8_decode('COBRADOR: '),0,0,'L');
			$this->pdf->SetFont('Arial','',8.5);
			$this->pdf->Cell(45,5,utf8_decode($credito['recaudador']),0,1,'L');
			
			$this->pdf->SetFont('Arial','B',8.5);
			$this->pdf->Cell(25,5,('DIRECCIÓN:'),0,0,'L');
			$this->pdf->SetFont('Arial','',8.5);
			$this->pdf->Cell(178,5,utf8_decode($credito["direccion"]),0,1,'L');
			
			if(!empty($venta) && isset($venta[0]['comprobante'])) {
				$this->pdf->SetFont('Arial','B',8.5);
				$this->pdf->Cell(26,5,utf8_decode('COMPROBANTE:'),0,0,'L');
				$this->pdf->SetFont('Arial','',8.5);
				$this->pdf->Cell(75,5,$venta[0]['comprobante'],0,0,'L');
			}
			$this->pdf->SetFont('Arial','B',8.5);
			$this->pdf->Cell(16,5,utf8_decode('CREDITO:'),0,0,'L');
			$this->pdf->SetFont('Arial','',8.5);
			$this->pdf->Cell(93,5,$credito['numerocredito'],0,1,'L');				
			// $this->pdf->Ln();
			$this->pdf->SetFont('Arial','',12);
			$this->pdf->Cell(203,2,'-----------------------------------------------------------------------------------------------------------------------------------------------',0,1,'L');
			$this->pdf->SetFont('Arial','',8.5);
			
			if(!empty($detalle)) {
				$this->pdf->Ln();
				foreach($detalle as $key => $val) {
					$this->pdf->Cell(45,3,utf8_decode($val["producto"].' '.$val['serie']),0,0,'L');
					$this->pdf->Ln();
				}
			}
			
			$this->pdf->Ln();
			$this->pdf->SetFont('Courier','B',9);
			$this->pdf->Cell(8,5,'No',1,0,'L');
			$this->pdf->Cell(15,5,'TIPO',1,0,'L');
			$this->pdf->Cell(20,5,'FEC VENC',1,0,'L');
			$this->pdf->Cell(20,5,'FEC PAGO',1,0,'L');
			$this->pdf->Cell(21,5,'TIPO PAGO',1,0,'L');
			$this->pdf->Cell(18,5,'CUOTA',1,0,'L');
			$this->pdf->Cell(16,5,'MORAS',1,0,'L');
			$this->pdf->Cell(18,5,'TOTAL',1,0,'L');
			$this->pdf->Cell(14,5,'DCT',1,0,'C');
			$this->pdf->Cell(18,5,'PAGOS',1,0,'L');
			$this->pdf->Cell(18,5,'SALDO',1,0,'L');
			$this->pdf->Cell(20,5,'RECIBO',1,0,'L');
			
			$this->pdf->SetFont('Courier','',8.3);
			if(!empty($letras)) {
				$totalCuota = $totalMora = $totalTotal = $totalPago = $totalSaldo = $totaldct = 0;
				$arrLetras = array();
				$Totalizado_montopagado=$Totalizado_interespagado=$Totalizado_descuentopagado=0;
				$Totalizado_saldoxpagar=$Totalizado_interesxpagar=$Totalizado_descuentoxpagar=0;
				$letras_pagadas = array();
				$this->pdf->SetDrawColor(204, 204, 204);
				foreach($letras as $val) {
					$total = floatval($val['monto']) + floatval($val['moras']);
					$pago = floatval($val['pagos']);
					$dct = floatval($val['descuento']);
					
					$arrLetras[ $val['idletra'] ] = array('letra'=>$val['letra'], 'estado'=>$val['pagado']);
					
					$saldo = $val['saldito'];
					
					$totalCuota += $val['monto'];
					$totalMora += $val['moras'];
					$totalTotal += $total;
					$totalPago += $pago;
					$totalSaldo += $saldo;
					$totaldct += $dct;

					$this->pdf->Ln();
					$this->pdf->Cell(8,5,$val["letra"],1,0,'L');
					$this->pdf->Cell(15,5,$val["tipo_letra"],1,0,'L');
					$this->pdf->Cell(20,5,$val["fecha_vencimiento"],1,0,'L');
					$this->pdf->Cell(20,5,$val["fecha_amortizacion"],1,0,'L');
					$this->pdf->Cell(21,5,$val["tipopago_credito"],1,0,'L');
					$this->pdf->Cell(18,5,$val["monto"],1,0,'R');
					$this->pdf->Cell(16,5,$val["moras"],1,0,'R');
					$this->pdf->Cell(18,5,number_format($total, 2, '.', ''),1,0,'R');
					$this->pdf->Cell(14,5,number_format($dct, 2, '.', ''),1,0,'R');
					$this->pdf->Cell(18,5,number_format($pago, 2, '.', ''),1,0,'R');
					$this->pdf->Cell(18,5,number_format($saldo, 2, '.', ''),1,0,'R');
					$this->pdf->Cell(20,5,$val['recibo'],1,0,'R');
					
					if(!empty($val["fecha_amortizacion"])){
						$Totalizado_montopagado   = $Totalizado_montopagado + $val["monto"];
						$Totalizado_interespagado = $Totalizado_interespagado + $val["moras"];
						$Totalizado_descuentopagado = $Totalizado_descuentopagado + $val["descuento"];
					}else{
						$Totalizado_interesxpagar = $Totalizado_interesxpagar + $val["moras"];
						$letras_pagadas[] = array($val["letra"]);
						$Totalizado_saldoxpagar = $Totalizado_saldoxpagar + $val["monto"];
					}
				}
				
				$this->pdf->Ln();
				
				$this->pdf->SetFont('Courier','B',8.3);
				$this->pdf->Cell(84,5,'TOTALES S/.',0,0,'L');
				$this->pdf->Cell(18,5,number_format($totalCuota, 2, '.', ''),1,0,'R');
				$this->pdf->Cell(16,5,number_format($totalMora, 2, '.', ''),1,0,'R');
				$this->pdf->Cell(18,5,number_format($totalTotal, 2, '.', ''),1,0,'R');
				$this->pdf->Cell(14,5,number_format($totaldct, 2, '.', ''),1,0,'R');
				$this->pdf->Cell(18,5,number_format($totalPago, 2, '.', ''),1,0,'R');
				$this->pdf->Cell(18,5,number_format($totalSaldo, 2, '.', ''),1,0,'R');
				$this->pdf->Cell(20,5,'',0,0,'L');
				
				if(count($amortizaciones)) {
					$has_title = false;
					foreach($amortizaciones as $codletra => $arr) {
						$continuar = (count($arr) > 1 && $arrLetras[$codletra]['estado'] == 'S');
						if(!$continuar) {
							$continuar = ($arrLetras[$codletra]['estado'] == 'N');
						}
						
						if($continuar) {
							if(!$has_title) {
								$this->pdf->SetDrawColor(0, 0, 0);
								$this->pdf->Ln();$this->pdf->Ln();$this->pdf->Ln();$this->pdf->Ln();
								$this->pdf->SetFont('Courier','B',15);
								$this->pdf->Cell(205,5,'AMORTIZACIONES',0,0,'L');
								$this->pdf->Ln();
								$this->pdf->SetFont('Courier','B',10);
								$this->pdf->Cell(10,5,'No',1,0,'L');
								$this->pdf->Cell(20,5,'TIPO',1,0,'L');
								$this->pdf->Cell(20,5,'LETRA',1,0,'L');
								$this->pdf->Cell(30,5,'FECHA PAGO',1,0,'L');
								$this->pdf->Cell(40,5,'TIPO PAGO',1,0,'L');
								$this->pdf->Cell(20,5,'CUOTA',1,0,'L');
								$this->pdf->Cell(20,5,'MORAS',1,0,'L');
								$this->pdf->Cell(20,5,'TOTAL',1,0,'L');
								$this->pdf->Cell(25,5,'RECIBO',1,0,'L');
								$has_title = true;
								$this->pdf->SetFont('Courier','',8.3);
								$this->pdf->SetDrawColor(204, 204, 204);
							}
							
							foreach($arr as $key => $amrtz) {
								$this->pdf->Ln();
								$this->pdf->Cell(10,5,($key+1),1,0,'L');
								$this->pdf->Cell(20,5,'AMRTZ',1,0,'L');
								$this->pdf->Cell(20,5,$arrLetras[$codletra]['letra'],1,0,'L');
								$this->pdf->Cell(30,5,$amrtz["fecha_amortizacion"],1,0,'L');
								$this->pdf->Cell(40,5,$amrtz["tipopago_credito"],1,0,'L');
								$this->pdf->Cell(20,5,$amrtz["monto"],1,0,'R');
								$this->pdf->Cell(20,5,$amrtz["moras"],1,0,'R');
								$this->pdf->Cell(20,5,$amrtz["total"],1,0,'R');
								$this->pdf->Cell(25,5,$amrtz["recibo"],1,0,'R');

								if($letras_pagadas[0][0]!=$arrLetras[$codletra]['letra']){
									$Totalizado_saldoxpagar = $Totalizado_saldoxpagar;
								}else{
									$Totalizado_interesxpagar = $Totalizado_interesxpagar - $amrtz["moras"];
									$Totalizado_interespagado = $Totalizado_interespagado + $amrtz["moras"];
									$Totalizado_montopagado = $Totalizado_montopagado + $amrtz["monto"];
									$Totalizado_saldoxpagar = $Totalizado_saldoxpagar - $amrtz["monto"];
								}
							}
						}
					}
				}
				
				if(isset($_REQUEST['resumen']) && !empty($_REQUEST['resumen'])){
					$this->pdf->SetFont('Courier','B',13);
					$this->pdf->Ln(20);
					$this->pdf->Cell(10,5,'',0,0,'L');
					$this->pdf->Cell(30,5,'',0,0,'L');
					$this->pdf->Cell(30,5,'MONTO',1,0,'C');
					$this->pdf->Cell(30,5,'INTERES',1,0,'C');
					$this->pdf->Cell(30,5,'DESCUENTO',1,0,'C');
					$this->pdf->Cell(40,5,'TOTAL',1,1,'C');
					
					$this->pdf->SetFont('Courier','B',10);
					$this->pdf->Cell(10,5,'',0,0,'L');
					$this->pdf->Cell(30,5,'Monto Pagado',1,0,'L');
					$this->pdf->SetFont('Courier','',10);
					$this->pdf->Cell(30,5,number_format($Totalizado_montopagado,2),1,0,'R');
					$this->pdf->Cell(30,5,number_format($Totalizado_interespagado,2),1,0,'R');
					$this->pdf->Cell(30,5,number_format($Totalizado_descuentopagado,2),1,0,'R');
					$this->pdf->Cell(40,5,number_format(($Totalizado_montopagado+$Totalizado_interespagado - $Totalizado_descuentopagado),2),1,1,'R');
					
					$this->pdf->SetFont('Courier','B',10);
					$this->pdf->Cell(10,5,'',0,0,'L');
					$this->pdf->Cell(30,5,'Saldo x pagar',1,0,'L');
					$this->pdf->SetFont('Courier','',10);
					$this->pdf->Cell(30,5,number_format($Totalizado_saldoxpagar,2),1,0,'R');
					$this->pdf->Cell(30,5,number_format($Totalizado_interesxpagar,2),1,0,'R');
					$this->pdf->Cell(30,5,number_format($Totalizado_descuentoxpagar,2),1,0,'R');
					$this->pdf->Cell(40,5,number_format(($Totalizado_saldoxpagar+$Totalizado_interesxpagar - $Totalizado_descuentoxpagar),2),1,1,'R');
				}
			}
			
			$this->pdf->Output();
		}else{
			echo "No se encontró el credito que esta buscando :'( ";
		}
	}
}
?>