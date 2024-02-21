 <?php

include_once "Controller.php";

class Venta extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Venta");
		$this->set_subtitle("Lista de ventas");
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		
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
		
		$this->load->library('combobox');
		
		///////////////////////////////////////////////////// combo tipo compra
		$query = $this->db->select('idtipoventa, descripcion')
			->where("estado", "A")->where("mostrar_en_venta", "S")
			->order_by("descripcion", "asc")->get("venta.tipo_venta");
		
		$this->combobox->setAttr("id", "idtipoventa");
		$this->combobox->setAttr("name", "idtipoventa");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["venta"]["idtipoventa"]) ) {
			$this->combobox->setSelectedOption($data["venta"]["idtipoventa"]);
		}
		$data["tipoventa"] = $this->combobox->getObject();
		
		/////////////////////////////////////////////////////// combo modalida venta
		$query = $this->db->select('idmodalidad, modalidad')
			->where("estado", "A")
			->order_by("modalidad", "asc")->get("venta.modalidad");
		$this->combobox->init();
		$this->combobox->setAttr("id", "idmodalidad");
		$this->combobox->setAttr("name", "idmodalidad");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["venta"]["idmodalidad"]) ) {
			$this->combobox->setSelectedOption($data["venta"]["idmodalidad"]);
		}
		$data["modalidad"] = $this->combobox->getObject();
		
		////////////////////////////////////////////////////// combo tipodocumento
		$query = $this->db->where("estado", "A")->where("mostrar_en_venta", "S")
			->order_by("descripcion", "asc")->get("venta.tipo_documento");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idtipodocumento","name"=>"idtipodocumento","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($query->result_array(), array("idtipodocumento","descripcion","codsunat","facturacion_electronica","ruc_obligatorio","dni_obligatorio"));
		
		if( isset($data["venta"]["idtipodocumento"]) ) {
			$this->combobox->setSelectedOption($data["venta"]["idtipodocumento"]);
		}
		$data["tipodocumento"] = $this->combobox->getObject();
		
		/////////////////////////////////////////////////////// combo serie documento
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"serie","name"=>"serie","class"=>"form-control input-xs","required"=>""));
		$this->combobox->setStyle("width", "78px");
		if( isset($data["venta"]["idtipodocumento"]) ) {
			// $query = $this->db->select("serie, lpad(serie::text, 3, '0')")
			$query = $this->db->select("serie, serie")
				->where("idtipodocumento", $data["venta"]["idtipodocumento"])
				->where("idsucursal", $this->get_var_session("idsucursal"))
				->order_by("serie", "asc")->get("venta.serie_documento");
			
			$this->combobox->addItem($query->result_array());
			if( isset($data["venta"]["serie"]) ) {
				$this->combobox->setSelectedOption($data["venta"]["serie"]);
			}
		}
		$data["serie"] = $this->combobox->getObject();
		$this->combobox->removeStyle("width");
		
		//////////////////////////////////////////////////////// combo almacen
		$query = $this->db->select('idalmacen, descripcion')->where("estado", "A")->where("mostrar_en_venta", "S")
			->where("idsucursal", $this->get_var_session("idsucursal"))->get("almacen.almacen");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idalmacen","name"=>"idalmacen","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($query->result_array());
		if( isset($data["venta"]["idalmacen"]) ) {
			$this->combobox->setSelectedOption($data["venta"]["idalmacen"]);
		}
		$data["almacen"] = $this->combobox->getObject();
		
		//////////////////////////////////////////////////////// combo moneda
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")
			->order_by("idmoneda", "asc")->get("general.moneda");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idmoneda","name"=>"idmoneda","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($query->result_array());
		if( isset($data["venta"]["idmoneda"]) ) {
			$this->combobox->setSelectedOption($data["venta"]["idmoneda"]);
		}
		$data["moneda"] = $this->combobox->getObject();
		
		//////////////////////////////////////////////////////// combo moneda
		// $query = $this->db->select('idmoneda, descripcion')->where("estado", "A")
			// ->order_by("idmoneda", "asc")->get("general.moneda");
		
		// $this->combobox->init();
		// $this->combobox->setAttr(array("id"=>"idmoneda","name"=>"idmoneda","class"=>"form-control input-xs","required"=>""));
		// $this->combobox->addItem($query->result_array());
		// if( isset($data["preventa"]["idmoneda"]) ) {
			// $this->combobox->setSelectedOption($data["preventa"]["idmoneda"]);
		// }
		// $data["moneda"] = $this->combobox->getObject();
		
		///////////////////////////////////////////////////// combo tipo operacion (sunat)
		$query = $this->db->select('codtipo_operacion, descripcion')
			->order_by("codtipo_operacion", "asc")->get("general.tipo_operacion");
		
		$this->combobox->init();
		$this->combobox->setAttr("id", "codtipo_operacion");
		$this->combobox->setAttr("name", "codtipo_operacion");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["preventa"]["codtipo_operacion"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["codtipo_operacion"]);
		}
		$data["tipo_operacion"] = $this->combobox->getObject();
		
		//////////////////////////////////////// combos temporales facturacion /////////////////////////////
		$query = $this->db->order_by("orden", "asc")->get("general.grupo_igv");
		$this->combobox->removeAllItems();
		$this->combobox->setAttr("id", "grupo_igv_temp");
		$this->combobox->setAttr("name", "grupo_igv_temp");
		$this->combobox->addItem($query->result_array(), array("codgrupo_igv","decripcion","tipo_igv_default","tipo_igv_oferta","igv"));
		$data["combo_grupo_igv"] = $this->combobox->getObject();
		
		$this->load_model("seguridad.empresa");
		$this->empresa->find($this->get_var_session("idempresa"));
		$igvs	= $this->empresa->get("igv");
		
		$sql = "select codtipo_igv, codtipo_igv||': '||descripcion as descripcion from general.tipo_igv order by 1";
		$query = $this->db->query($sql);
		$this->combobox->removeAllItems();
		$this->combobox->setAttr("id", "tipo_igv_temp");
		$this->combobox->setAttr("name", "tipo_igv_temp");
		$this->combobox->addItem($query->result_array());
		$data["combo_tipo_igv"] = $this->combobox->getObject();
		/*
		if ($igvs == 'N' ):
		$data["default_igv"] = $this->get_param("default_igv");
		endif;	

		if ($igvs == 'S' ):
		$data["default_igv"] = $this->get_param("default_igvdos");
		endif;
		*/
		
		$data["default_igv"] = $this->get_param("default_igv");
		
		///////////////////////////////////////////////////////// combo vendedor
		// $idperfil = 4; // id del perfil vendedor, tal vez deberia ser contante
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idvendedor","name"=>"idvendedor","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($datos);
		if( isset($data["venta"]["idvendedor"]) ) {
			$this->combobox->setSelectedOption($data["venta"]["idvendedor"]);
			if( ! $this->combobox->item_exists($data["venta"]["idvendedor"])) {
				$this->combobox->addItem($this->usuario->get_empleado($data["venta"]["idvendedor"]));
			}
		}
		else {
			/* Comenetado para evitar que los cajeros seleccionen al vendedor correcto */
			// $this->combobox->setSelectedOption($this->get_var_session("idusuario"));
		}
		$data["vendedor"] = $this->combobox->getObject();
		
		$data["modal_pago"] = $this->get_form_pago("venta", true);

		$data["controller"] = $this->controller;
		
		$igv = $this->get_param("igv");
		if(!is_numeric($igv)) {
			$igv = 18;
		}
		$data["valor_igv"] = $igv;
		$data["validar_ruc"] = $this->get_param("validar_ruc");
		$data["mostrar_precio_costo"] = $this->get_param("mostrar_precio_costo", "N");
		$data["precio_venta_cero"] = $this->get_param("precio_venta_cero", "N");
		
		$nueva_venta = "true";
		if( isset($data["venta"]["idventa"]) ) {
			$nueva_venta = "false";
		}
		$this->js("<script>var _es_nueva_venta_ = $nueva_venta;</script>", false);
		
		if( isset($data["detalle"]) ) {
			$this->js("<script>var data_detalle = ".json_encode($data["detalle"]).";</script>", false);
		}
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
		
		// $this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		$this->css('plugins/iCheck/custom');
		
		
		// $this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('plugins/iCheck/icheck.min');
		$this->js('form/'.$this->controller.'/form');
		
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		$data["fixed"] = $fc;
		$this->js("<script>var _fixed_venta = $fc;</script>", false);

		// formulario CLIENTE
		$this->load_controller("cliente");
		// $this->cliente_controller->load = $this->load;
		// $this->cliente_controller->db = $this->db;
		// $this->cliente_controller->session = $this->session;
		// $this->cliente_controller->combobox = $this->combobox;
		$data["form_cliente"] = $this->cliente_controller->form(null, "cli_", true);

		$this->js('form/cliente/modal');
		
		// formulario
		// $this->load_controller("consultarproducto");
		// $this->consultarproducto_controller->load = $this->load;
		// $this->consultarproducto_controller->db = $this->db;
		// $this->consultarproducto_controller->session = $this->session;
		// $this->consultarproducto_controller->combobox = $this->combobox;
		// $data["form_consultarpr"] = $this->consultarproducto_controller->form(null, "conspr_", true);
		
		$data["editar_correlativo"] = $this->get_param("editar_correlativo_v", "N");
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function filtros_grilla() {
		$this->load_library("combobox");
		
		$this->combobox->setAttr("class", "form-control input-sm");
		$this->combobox->addItem("", "TODOS");
		
		$html = '<div class="row">';
		
		// div y combobox tipo venta
		$query = $this->db->select('idtipoventa, descripcion')
			->where("estado", "A")->where("mostrar_en_venta", "S")
			->order_by("descripcion", "asc")->get("venta.tipo_venta");
		$this->combobox->setAttr("filter", "idtipoventa");
		$this->combobox->addItem($query->result_array());
		$html .= '<div class="col-sm-3"><div class="form-group">';
		$html .= '<label class="control-label">Tipo venta</label>';
		$html .= $this->combobox->getObject();
		$html .= '</div></div>';
		
		// div y combobox cancelado
		$this->combobox->setAttr("filter", "cancelado");
		$this->combobox->removeItems(1);
		$this->combobox->addItem("S", "SI");
		$this->combobox->addItem("N", "NO");
		$html .= '<div class="col-sm-3"><div class="form-group">';
		$html .= '<label class="control-label">Cancelado</label>';
		$html .= $this->combobox->getObject();
		$html .= '</div></div>';
		
		// div y combobox despachado
		$this->combobox->setAttr("filter", "despachado");
		$html .= '<div class="col-sm-2"><div class="form-group">';
		$html .= '<label class="control-label">Despachado</label>';
		$html .= $this->combobox->getObject();
		$html .= '</div></div>';
		
		/* div y fecha venta desde /hasta */
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">Rango de fechas</label>';
		$html .= '<div class="input-daterange input-group input-group-xs">';
		$html .= '	<span class="input-group-addon">De</span>';
		$html .= '	<input type="text" class="input-sm form-control" filter="fecha_venta" id="fecha_i" value="'.date('d/m/Y').'" placeholder="dd/mm/aaaa" >';
		$html .= '	<span class="input-group-addon">Hasta</span>';
		$html .= '	<input type="text" class="input-sm form-control" filter="fecha_venta" id="fecha_f" placeholder="dd/mm/aaaa" >';
		$html .= '</div>';
		$html .= '</div></div>';
		
		/* div y combobox credito */
		// $this->combobox->setAttr("filter", "con_credito");
		// $html .= '<div class="col-sm-3"><div class="form-group">';
		// $html .= '<label class="control-label">Cronograma</label>';
		// $html .= $this->combobox->getObject();
		// $html .= '</div></div>';
		
		// $html .= '</div>';
		
		$this->set_filter($html);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$modal  = $this->html_modal();
		$this->load_model("venta.venta_view");
		$this->load->library('datatables');
		$this->add_button_content(null,$modal,null,null,'white',array('display'=>'inline-block'));
		$this->datatables->setModel($this->venta_view);
		$this->datatables->setIndexColumn("idventa");
		
		$this->datatables->where('estado', '<>', 'X');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		$this->datatables->where('fecha_venta', '=', date("Y-m-d"));//
		
		// $this->datatables->setColumns(array('idventa','fechaventa','cliente','comprobante','serie','descripcion'));
		$this->datatables->setColumns(array('fecha_venta','comprobante','full_nombres', 'tipo_venta', 'moneda', 'subtotal', 'igv', 'descuento', 'total'));
		$this->datatables->order_by('fecha_registro');
		$this->datatables->setCallback("formatoFechaGrilla");
		
		$columnasName = array(
			array('Fecha','5%')
			,array('Comprobante','8%')
			,array('Cliente','35%')
			,array('Tipo','5%')
			,array('Moneda','12%')
			,array('Subtotal','6%')
			,array('IGV','5%')
			,array('Descuento','6%')
			,array('Total','5%')
		);

		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		$this->filtros_grilla();
		$this->set_content($this->get_form_pago("venta", true));
		
		return $table;
	}
	
	public function html_modal(){
		$html = ' <div id="form_anul" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel">
						<div class="modal-dialog" >
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Anular comprobante vacio</h4>
								</div>
								<div class="modal-body">
									<div class="row">
										<br>
										<div class="col-sm-12"><div style=""><strong>No seleccion&oacute; un comprobante</strong>... Ingrese los datos del comprobante vac&iacute;o que desea anular.</div></div>
									</div>
									
									<div class="row">
										<br>
										<div class="col-sm-5">
											<label class="required">Tipo Documento</label><select class="form-control input-xs t_doc"></select>
										</div>
										<div class="col-sm-3">
											<label class="required">Serie</label><select class="form-control input-xs serie_doc"></select>
										</div>
										<div class="col-sm-4">
											<label class="required">Numero</label><input type="text" class="form-control input-xs nro_doc" style>
										</div>
									</div>
									
									<div class="row">
										<br>
										<div class="col-sm-12">
											<label class="required">Motivo anulaci&oacute;n</label>
											<textarea id="txtMotivoAnulacion" class="form-control input-xs"></textarea>
										</div>
									</div>									
								</div>
								
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
									<button type="button" id="anular_vacio" class="btn btn-danger">Anular Comprobante</button>
								</div>
							</div>
						</div>
					</div>';
		return $html;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Venta");
		$this->set_subtitle("");
		
		$this->set_content($this->form(array("readonly"=>false)));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model(array("venta.venta_view", "venta.venta", "venta.detalle_venta"));
		$this->venta_view->set_column_pk("idventa");
		
		$data["venta"] = $this->venta_view->find($id);
		$es_anulado = ($this->venta_view->get("estado") != "A");
		
		$data["detalle"] = $this->detalle_venta->get_items($id, $es_anulado);
		
		// $data["readonly"] = $this->venta->has_despacho($id);
		$data["readonly"] = false;
		$data["anulado"] = $es_anulado;
		
		// echo "<pre>";var_dump($data);
		$this->set_title("Modificar Venta");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */	
	public function guardar() {
		$this->unlimit();
		$this->load_model($this->controller);
		$this->load_model("venta.tipo_documento");
		
		$fields = $this->input->post();
		$fields['idsucursal'] = $this->get_var_session("idsucursal");
		$fields['idusuario'] = $this->get_var_session("idusuario");
		$fields['fecha_registro'] = date("Y-m-d H:i:s");
		$fields['pasa_despacho'] = (empty($fields['pasa_despacho'])) ? "N" : "S";
		$fields['idtipopago'] = (!empty($fields['idtipopago'])) ? $fields['idtipopago'] : 0;
		// $fields['cobrar_venta'] = (!empty($fields['cobrar_venta'])) ? $fields['cobrar_venta'] : 'N';
		$fields['vuelto'] = (!empty($fields['monto_vuelto'])) ? $fields['monto_vuelto'] : 0;
		$fields['afecta_caja'] = (!empty($fields['afecta_caja'])) ? "S" : "N";
		$fields['despachado'] = ($fields['pasa_despacho'] == "S") ? "N" : "S";
		$fields['cancelado'] = ($fields['idtipoventa'] == 1 && $fields["afecta_caja"] == "S") ? "S" : "N";
		$fields['estado'] = "A";
		$fields["con_credito"] = "N";
		if(empty($fields["fecha_venta"]))
			$fields['fecha_venta'] = date("Y-m-d");
		if(empty($fields["descuento"]))
			$fields["descuento"] = 0;
		if(empty($fields["igv"]))
			$fields["igv"] = 0;
		if(empty($fields["monto_entregado"]))
			$fields["monto_entregado"] = 0;
		if($fields["afecta_caja"] == "N")
			$fields['idtipopago'] = 0;
		if(empty($fields["idpreventa"]))
			$fields["idpreventa"] = 0;
		if(empty($fields["idmodalidad"]))
			$fields["idmodalidad"] = 0;
		if(empty($fields["idcliente"]))
			$fields["idcliente"] = 0;
		if(empty($fields["idvendedor"]))
			$fields["idvendedor"] = $this->get_var_session("idusuario");
		
		$esNuevaVenta = empty($fields["idventa"]);
		
		//Aqui falta verificar si el cliente es varios, saltar la validacion, de lo contrario, validar DNI o RUC
		$this->tipo_documento->find($fields["idtipodocumento"]);
		if($this->tipo_documento->get("ruc_obligatorio")=='S'){
			$long_ruc = $this->get_param("long_ruc", 11);
			if( $long_ruc > strlen($fields["cliente_doc"])){
				$this->exception("Falta ".($long_ruc - strlen($fields["cliente_doc"]) )." digitos del RUC del cliente");
				return false;
			}
		}else if($this->tipo_documento->get("dni_obligatorio")=='S'){
			$long_dni = $this->get_param("long_dni", 8);
			if( $long_dni > strlen($fields["cliente_doc"])){
				$this->exception("Falta ".($long_dni - strlen($fields["cliente_doc"]) )." digitos del DNI del cliente");
				return false;
			}
		}
		
		$venta_con_linea = $this->get_param("venta_con_linea", "N");
		
		$this->load_model("venta.cliente");
		if($venta_con_linea=='S' && ($fields['idtipoventa']==2) && $esNuevaVenta){//Si las ventas al credito estan limitadas con linea de credito
			$saldo = $this->cliente->saldo($fields["idcliente"]);
			
			if($fields['total']>$saldo){
				$this->exception("El monto de la venta supera a la linea de credito del cliente {$fields['cliente']}<br><b>Solo le queda ".number_format($saldo,2,'.',',')." Nuevos soles</b></br>Solicite al ADMINISTRADOR la ampliacion de linea de credito y concluir la operacion :)");
				return false;
			}
			
			$res = $this->credito_vencido($fields['idcliente']);
			if(!empty($res)){
				
				$this->exception("El cliente {$fields['cliente']}<br><b>Tiene deuda vencida del credito {$sms['nro_credito']}</b></br>Solicite al ADMINISTRADOR el desbloqueo.");
				return false;
			}
		}
		
		if($fields['idtipoventa']==2){//Venta al credito, validacion para las cobranzas
			$this->load_model("venta.cliente_view");
			
			$this->cliente_view->find(array("idcliente"=>$fields['idcliente']));
			
			$x_zona = trim($this->cliente_view->get("idzona"));
			$x_dirc = trim($this->cliente_view->get("direccion"));
			$x_telf = trim($this->cliente_view->get("telefono"));
			
			$mensaje ="El cliente debe tener <br>";
			$continue = true;
			if(empty($x_dirc)){
				$continue = false;
				$mensaje.="<b>Dirección</b></br>";
			}
			
			if(empty($x_zona)){
				$continue = false;
				$mensaje.="<b>Zona</b></br>";
			}
			if(empty($x_telf)){
				$continue = false;
				$mensaje.="<b>Teléfono</b></br>";
			}
			$mensaje.=" Para poder continuar la venta al credito";
			if(!$continue){
				$this->exception($mensaje);
				return false;
			}
		}
		
		// if($esNuevaVenta) {
			// verificamos datos necesarios segun el tipo comprobante
			$valid = $this->is_valid_doc($fields["idtipodocumento"], $fields["serie"], $fields["idcliente"],$fields["total"],$fields["idmoneda"]);
			if($valid !== true) {
				$this->exception($valid);
				return;
			}
		// }
		
		$ingresoCaja = true;
		$hasCredito = false;
		
		$this->db->trans_start(); // inciamos transaccion
		
		if($esNuevaVenta) {
			// verificamos el documento generado
			if($this->has_comprobante("venta", $fields["idtipodocumento"], $fields["serie"], $fields["correlativo"])) {
				$this->exception("Ya se ha generado el comprobante ".$fields["serie"]."-".$fields["correlativo"]);
				return false;
			}
			
			$idventa = $this->venta->insert($fields);
			
			// actualizamos el correlativo del documento
			if(empty($fields["edit_correlativo"]))
				$this->update_correlativo($fields["idtipodocumento"], $fields["serie"]);
		} else {
			$this->load_model("credito");
			$idcredito = 0;
			$idventa = $fields["idventa"];
			
			// obtenemos los datos anteriores
			$temp = $this->venta->find($idventa);
			
			//aqui se coje la fecha y porseacaso la hora del proceso
			//para saber cuando sue creado, y comparar con la fecha actual para ver si afecta a caja o no
			$fields['fecha_venta'] = $temp['fecha_venta'];
			
			// verificamos si la venta ha sido al credito y se han generado las letras
			if($temp["idtipoventa"] == 2 && $temp["con_credito"] == "S") {
				$query = $this->db->where("idventa", $idventa)->where("estado", "A")->get("credito.credito");
				if($query->num_rows() >= 1) {
					$row = $query->row();
					$idcredito = $row->idcredito;
					// verificamos si se ha hecho alguna amortizacion
					if($this->credito->has_amortizacion($idcredito)) {
						$this->exception("Existen amortizaciones relacionadas con la venta ".$temp["serie"]."-".
							$temp["correlativo"].". Elimine primero las amortizaciones para modificar la venta");
						return;
					}
					$hasCredito = true;
				}
			}
			
			///////////////////////////////////////////////////////////////////////////////////////
			// va todo bien, actualizamos la venta
			$this->venta->update($fields);
			
			// eliminamos la salida del detalle_almacen
			$this->db->where("tabla", "V")->where("idtabla", $idventa)
				->update("almacen.detalle_almacen", array("estado"=>"I"));
				
			// eliminamos las series del detalle_almacen
			$this->db->where("tabla_salida", "V")->where("idtabla_salida", $idventa)->where("estado", "A")
				->update("almacen.detalle_almacen_serie", array("despachado"=>"N"));
				
			// eliminamos el detalle de la venta
			$this->db->where("idventa", $idventa)
				->update("venta.detalle_venta", array("estado"=>"I"));
				
			// eliminamos las series de la venta
			$this->db->where("idventa", $idventa)
				->update("venta.detalle_venta_serie", array("estado"=>"I"));
				
			// eliminamos el despacho
			$this->db->where("idreferencia", $idventa)->where("referencia", "V")
				->update("almacen.despacho", array("estado"=>"I"));
			
			// si la venta es al contado
			if(intval($fields["idtipoventa"]) == 1) {
				if($hasCredito) {
					// la venta anteriormente tenia credito, eliminamos el credito
					$this->db->where("idcredito", $idcredito)->delete("credito.amortizacion");
					$this->db->where("idcredito", $idcredito)->delete("credito.letra");
					$this->db->where("idcredito", $idcredito)->update("credito.credito", array("estado"=>"I"));
					$idcredito = 0;
				}
			}
			else {
				// eliminamos las amortizaciones y las letras del credito
				$this->db->where("idcredito", $idcredito)->delete("credito.amortizacion");
				$this->db->where("idcredito", $idcredito)->delete("credito.letra");
				
				// editamos algunos datos del credito
				$this->credito->find($idcredito);
				$this->credito->set("idcliente", $fields["idcliente"]);
				$this->credito->set("inicial", 0);
				$this->credito->set("idmoneda", $fields["idmoneda"]);
				$this->credito->set("monto_facturado", $fields["total"]);
				$this->credito->set("interes", 0);
				$this->credito->set("monto_credito", $fields["total"]);
				$this->credito->set("pagado", "N");
				$this->credito->set("capital", $fields["total"]);
				$this->credito->update();
			}
		}
		
		$this->load_model("detalle_venta");
		$this->load_model("detalle_venta_serie");
		$this->load_model("compra.producto_precio_unitario");
		$this->load_model("producto");
		$this->load_model("producto_unidad");
		$this->load_model("general.grupo_igv");
		
		// llenamos datos por default para los modelos
		$this->detalle_venta->set("idventa", $idventa);
		$this->detalle_venta->set("estado", "A");
		$this->detalle_venta->set("despachado", $fields["despachado"]);
		
		if($fields['despachado'] == "S") {
			$this->load_model("detalle_almacen");
			$this->load_model("detalle_almacen_serie");
			$this->load_model("almacen.despacho"); // siempre se registra esta vaina
			$this->load_model("tipo_movi_almacen");
			
			$this->detalle_almacen->set("tipo", "S");
			$this->detalle_almacen->set("tipo_number", -1);
			$this->detalle_almacen->set("fecha", date("Y-m-d"));
			$this->detalle_almacen->set("tabla", "V");
			$this->detalle_almacen->set("idtabla", $idventa);
			$this->detalle_almacen->set("estado", "A");
			$this->detalle_almacen->set("idsucursal", $this->venta->get("idsucursal"));
			
			$this->despacho->set("idreferencia", $idventa);
			$this->despacho->set("referencia", "V");
			$this->despacho->set("tipo_docu", $this->venta->get("idtipodocumento"));
			$this->despacho->set("serie", $this->venta->get("serie"));
			$this->despacho->set("numero", $this->venta->get("correlativo"));
			$this->despacho->set("observacion", "DESPACHO AUTOMATICO DE VENTA");
			$this->despacho->set("fecha", date("Y-m-d"));
			$this->despacho->set("hora", date("H:i:s"));
			$this->despacho->set("idusuario", $this->venta->get("idusuario"));
			// echo $this->get_idtipo_movimiento("venta");
			$this->tipo_movi_almacen->find($this->get_idtipo_movimiento("venta"));
			$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
			// $tipo_movimiento = $this->get_idtipo_movimiento("venta");
		}
		
		$arrProductosKardex = array(); // datos almacen kardex
		// $deta_igv = ( ! empty($fields["valor_igv"])) ? floatval($fields["valor_igv"])/100 : 0;
		$redondeo_sunat = $this->get_param("facturacion_redondeo_sunat", "S");
		$tipocambio = ($fields["idmoneda"] == 1) ? 1 : floatval($fields["cambio_moneda"]);
		
		foreach($fields["deta_idproducto"] as $key=>$val) {
			// obtenemos el precio de costo
			// $costo = $this->producto->get_precio_compra_unitario(
				// $val, $this->venta->get("idsucursal"), $fields["deta_idunidad"][$key], 
				// $this->venta->get("idmoneda"));
			
			$this->producto_unidad->find(array("idproducto"=>$val, "idunidad"=>$fields["deta_idunidad"][$key]));
			
			$costo_unit = $this->producto->get_precio_costo_unitario($val, $this->venta->get("idsucursal"));
			$costo_unit *= $tipocambio;
			$costo = $costo_unit * floatval($this->producto_unidad->get("cantidad_unidad_min"));
			
			if(empty($fields["deta_oferta"][$key]) || $fields["deta_oferta"][$key] != 'S')
				$fields["deta_oferta"][$key] = 'N';
			
			$cantidad = floatval($fields["deta_cantidad"][$key]);
			$precio = empty($fields["deta_precio"][$key]) ? 0 : floatval($fields["deta_precio"][$key]);
			if($fields["deta_oferta"][$key] == 'N' && $cantidad > 0 && $redondeo_sunat == "S") {
				$precio = floatval($fields["deta_importe"][$key]) / $cantidad;
			}
			$precio_unit = $precio / floatval($this->producto_unidad->get("cantidad_unidad_min"));
			
			$desc = str_replace("(A TITULO GRATUITO)", "", $fields["deta_producto"][$key]);
			if($fields["deta_oferta"][$key] == "S") {
				$desc = trim($desc) . " (A TITULO GRATUITO)";
			}
			
			$this->grupo_igv->find($fields["deta_grupo_igv"][$key]);
			
			// insertamos el detalle venta
			$this->detalle_venta->set("idproducto", $val);
			$this->detalle_venta->set("descripcion", $desc);
			$this->detalle_venta->set("idunidad", $fields["deta_idunidad"][$key]);
			$this->detalle_venta->set("cantidad", $cantidad);
			$this->detalle_venta->set("precio", $precio);
			$this->detalle_venta->set("costo", $costo);
			$this->detalle_venta->set("idalmacen", $fields["deta_idalmacen"][$key]);
			$this->detalle_venta->set("afecta_stock", $fields["deta_controla_stock"][$key]);
			$this->detalle_venta->set("afecta_serie", $fields["deta_controla_serie"][$key]);
			$this->detalle_venta->set("oferta", $fields["deta_oferta"][$key]);
			$this->detalle_venta->set("igv", floatval($this->grupo_igv->get("igv")));
			$this->detalle_venta->set("codgrupo_igv", $fields["deta_grupo_igv"][$key]);
			$this->detalle_venta->set("codtipo_igv", $fields["deta_tipo_igv"][$key]);
			$this->detalle_venta->set("cantidad_um", $this->producto_unidad->get("cantidad_unidad_min"));
			$this->detalle_venta->insert();
			
			// ingresamos las series en el detalle venta
			if($fields["deta_controla_serie"][$key] == "S") {
				if( ! empty($fields["deta_series"][$key])) {
					$this->detalle_venta_serie->set($this->detalle_venta->get_fields());
					$arr = explode("|", $fields["deta_series"][$key]);
					foreach($arr as $serie) {
						$this->detalle_venta_serie->set("serie", $serie);
						$this->detalle_venta_serie->insert(null, false);
					}
				}
			}
			
			// ingresamos el precio unitario de venta del producto
			if($fields["deta_oferta"][$key] != "S") {
				$datos["idproducto"] = $val;
				$datos["idsucursal"] = $fields["idsucursal"];
				$datos["precio_venta"] = $precio_unit * $tipocambio;
				$this->producto_precio_unitario->save($datos, false);
			}
			
			if($fields['despachado'] == "S") {
				// registramos el despacho
				$this->despacho->set($this->detalle_venta->get_fields());
				$this->despacho->set("cant_despachado", $this->detalle_venta->get("cantidad"));
				$this->despacho->set("correlativo", $correlativo);
				$this->despacho->set("estado", "C");
				$this->despacho->set("iddetalle_referencia", $this->detalle_venta->get("iddetalle_venta"));
				$this->despacho->insert();
				$correlativo = $correlativo + 1; // nuevo correlativo
				
				// si el item controla stock hacemos egreso del almacen
				if($fields["deta_controla_stock"][$key] == "S") {
					// verificamos el stock del producto
					$stock = $this->has_stock($this->detalle_venta->get_fields());
					if($stock !== TRUE) {
						$this->exception("No existe stock para el producto ".$fields["deta_producto"][$key].". 
							Stock disponible: ".number_format($stock, 2));
						return false;
					}
					
					// retiramos el stock en el almacen
					$this->detalle_almacen->set($this->detalle_venta->get_fields());
					$this->detalle_almacen->set("precio_costo", $this->detalle_venta->get("costo"));
					$this->detalle_almacen->set("precio_venta", $this->detalle_venta->get("precio"));
					$this->detalle_almacen->set("iddespacho", $this->despacho->get("iddespacho"));
					$this->detalle_almacen->insert();
					
					// verificamos para retirar las series del almacen
					if($fields["deta_controla_serie"][$key] == "S") {
						if(empty($fields["deta_series"][$key])) {
							$this->exception("Ingrese las series del producto ".$fields["deta_producto"][$key]);
							return false;
						}
						
						$count_real_serie = intval($this->producto_unidad->get("cantidad_unidad_min")) * $cantidad;
						
						$arr = explode("|", $fields["deta_series"][$key]);
						if(count($arr) != $count_real_serie) {
							$this->exception("Debe ingresar $count_real_serie series para el producto: ".$fields["deta_producto"][$key]);
							return false;
						}
						
						// despachamos las series
						foreach($arr as $serie) {
							$sql = 'SELECT * FROM almacen.detalle_almacen_serie 
								WHERE estado=? AND despachado=? AND serie=? AND idalmacen=?';
							$query = $this->db->query($sql, array('A', 'N', $serie, $this->detalle_almacen->get("idalmacen")));
							
							if($query->num_rows() <= 0) {
								$this->exception("La serie {$serie} no existe o ya ha sido despachado");
								return false;
							}
							
							$this->detalle_almacen_serie->set($query->row_array());
							$this->detalle_almacen_serie->set("despachado", "S");
							$this->detalle_almacen_serie->set("fecha_salida", date("Y-m-d"));
							$this->detalle_almacen_serie->set("tabla_salida", "V");
							$this->detalle_almacen_serie->set("idtabla_salida", $idventa);
							$this->detalle_almacen_serie->set("iddespacho", $this->detalle_almacen->get("iddespacho"));
							$this->detalle_almacen_serie->update();
						}
					}
					
					$temp = $this->despacho->get_fields();
					$temp["cantidad"] = $temp["cant_despachado"];
					$temp["preciocosto"] = $costo_unit;
					$temp["precioventa"] = $precio_unit;
					$arrProductosKardex[] = $temp;
				}
			}
		}// fin [foreach]
		
		if( ! $esNuevaVenta) {
			// eliminamos el ingreso de kardex
			$this->load_library("jkardex");
			$this->jkardex->remove("venta", $idventa, $fields["idsucursal"]);
			
			// eliminamos el pago ingresado
			$this->load->library('pay');
			$this->pay->remove("venta", $idventa, $fields["idsucursal"]);
		}
		
		if($fields['despachado'] == "S") {
			if( ! empty($arrProductosKardex)) {
				// actualizamos el correlativo del tipo movimiento
				$this->tipo_movi_almacen->set("correlativo", $correlativo);
				$this->tipo_movi_almacen->update();
				
				if( ! isset($this->jkardex)) {
					// importamos librari
					$this->load_library("jkardex");
				}
				
				$this->jkardex->idtercero = $this->venta->get("idcliente");
				$this->jkardex->idmoneda = $this->venta->get("idmoneda");
				// $this->jkardex->tipo_movimiento = $tipo_movimiento;
				$this->jkardex->tipocambio = $this->venta->get("cambio_moneda");
				$this->jkardex->numero = $this->venta->get("correlativo");
				
				$this->jkardex->referencia("venta", $idventa, $fields["idsucursal"]);
				$this->jkardex->salida();
				// $this->jkardex->calcular_precio_costo();
				$this->jkardex->push($arrProductosKardex);
				$this->jkardex->run();
			}
		}
		
		if( $fields["afecta_caja"] == 'S' && $fields["idtipoventa"] == 1 ) {
			// datos necesarios para la libreria pay
			$monto_pago= $fields["monto_pagar"];
			$id_moneda = $fields["idmoneda"];
			unset($fields["idmoneda"]);
			
			$fields["descripcion"] = "VENTA AL CONTADO";
			$fields["referencia"] = $fields['cliente'];
			$fields["tabla"]		= "venta";
			$fields["idoperacion"] = $idventa;
			$fields["numero"] = $fields["correlativo"];
			
			// $fields["idmoneda"] = $fields["id_moneda_cambio"]?$fields["id_moneda_cambio"]:$id_moneda;
			// $fields["cambio_moneda"] = $fields["tipo_cambio_vigente"]?$fields["tipo_cambio_vigente"]:$fields["cambio_moneda"];
			// $fields["monto_pagar"] = $fields["monto_convertido_pay"]?$fields["monto_convertido_pay"]:$monto_pago;
			
			// if( $fields['fecha_venta'] == date("Y-m-d") ){// si la fecha que se hizo la operacion es igual a la fecha actual
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
		}
		
		// verificamos si ha seleccionado preventa
		if(!empty($fields["idpreventa"])) {
			$this->db->where("idpreventa", $fields["idpreventa"])
				->update("venta.preventa", array("pendiente"=>"N"));
		}
		
		$fields['idventa']= $idventa;
		if(!empty($idcredito)) {
			$fields["idcredito"] = $idcredito;
			
			//Aqui guardamos la asignacion a la cartera de cobranzas
			// $this->cliente->find($this->venta->get("idcliente"));
			// $this->load_model("cobranzas.hoja_ruta");
			// $data_c['idzona']		= $this->cliente->get("idzona");
			// $data_c['idempleado']	= $this->get_var_session("idusuario");
			// $data_c['idsucursal']	= $this->get_var_session("idsucursal");
			// $data_c['idcredito']	= $fields["idcredito"];
			// $data_c['idventa']		= $idventa;
			// $data_c['idcobrador']	= $fields['idvendedor'];
			// $data_c['idcliente']	= $this->venta->get("idcliente");
			// $data_c['estado']		= "A";
			// $this->hoja_ruta->insert($data_c);
		}
		$this->db->trans_complete(); // finalizamos transaccion
		
		// verificamos si se va crear los archivos de la facturacion
		if($this->es_electronico($fields["idtipodocumento"]) && $this->get_param("facturacion_electronica") == "S") {
			$fc = $this->get_param("fixed_venta");
			if(!is_numeric($fc)) {
				$fc = 2;
			}
			if($esNuevaVenta) {
				$this->send_to_facturador("venta", $idventa, $this->get_var_session("idsucursal"),$fc);
			}
			else {
				$this->update_to_facturador("venta", $idventa, $this->get_var_session("idsucursal"),$fc);
			}
		}
		
		//$this->response($fields);
		$this->response($this->venta->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($idventa, $estado = "X") {
		$this->load_model($this->controller);
		
		$this->venta->find($idventa);
		$idsucursal = $this->venta->get("idsucursal");
		
		$this->db->trans_start();
		
		$post = $this->input->post();
		
		// $this->venta->update(array("idventa"=>$idventa, "estado"=>$estado));
		$datos = array("idventa"=>$idventa, "estado"=>$estado, "fecha_hora_anulacion"=>date("Y-m-d H:i:s"), 
			"idusuario_anulacion"=>$this->get_var_session("idusuario"));
		if( ! empty($post["motivo"])) {
			$datos["motivo_anulacion"] = $post["motivo"];
		}
		$this->venta->update($datos);
		
		// eliminamos el despacho
		$this->db->where("idreferencia", $idventa)->where("referencia", "V")
			->update("almacen.despacho", array("estado"=>"I"));
		
		// eliminamos la salida de stock del detalle_almacen
		$this->db->where("tabla", "V")->where("idtabla", $idventa)
			->update("almacen.detalle_almacen", array("estado"=>"I"));
			
		// eliminamos la salida de las series en almacen
		$this->db->where("tabla_salida", "V")->where("idtabla_salida", $idventa)->where("estado", "A")
			->update("almacen.detalle_almacen_serie", array("despachado"=>"N"));
			
		// eliminamos el detalle de la venta
		$this->db->where("idventa", $idventa)
			->update("venta.detalle_venta", array("estado"=>"I"));
			
		// eliminamos las series de la compra
		$this->db->where("idventa", $idventa)
			->update("venta.detalle_venta_serie", array("estado"=>"I"));
		
		$recibo_egreso = 0;
		
		if($this->venta->get("idtipoventa") == 1) { // contado
			// si se afecta a caja y ademas se ha hecho el cobro
			if($this->venta->get("afecta_caja") == "S" && $this->venta->get("cancelado") == "S") {
				$this->load_library('pay');
				if($this->pay->remove_if_open("venta", $idventa, $idsucursal)) {
					// monto eliminado de la caja abierta
				}
				else { // la caja se ha cerrado, realizamos un egreso de la caja actual
					// deben realizar un RECIBO DE EGRESO en la caja actual, si va afectar caja
					$recibo_egreso = 1;
					
					/* // datos necesarios para la libreria pay
					$datos_detalle["descripcion"] = "ANULACION DE VENTA";
					$datos_detalle["idoperacion"] = $datos_caja["idtabla"];
					$datos_detalle["cambio_moneda"] = $datos_detalle["tipocambio"];
					$datos_detalle["monto_pagar"] = $datos_detalle["monto"];
					$datos_detalle["monto_entregado"] = $datos_detalle["monto"];
					
					$this->load_controller("caja");
					$this->load->library('pay');
					$this->pay->set_controller($this->caja_controller);
					$this->pay->set_data($datos_detalle);
					$this->pay->salida();
					$this->pay->process();
					
					if($datos_detalle["idtipopago"] == 2) {
						// eliminamos el movimiento de tarjeta
						$this->db->where("tabla", "venta")->where("idoperacion", $idventa)
							->where("idsucursal", $idsucursal)->delete("venta.movimiento_tarjeta");
					}
					else if($datos_detalle["idtipopago"] == 3) {
						// eliminamos el movimiento de deposito
						$this->db->where("tabla", "venta")->where("idoperacion", $idventa)
							->where("idsucursal", $idsucursal)->delete("venta.movimiento_deposito");
					} */
				}
			}
		}
		else {
			// eliminamos el credito de la venta
			$query = $this->db->where("idventa", $idventa)->where("estado", "A")->get("credito.credito");
			if($query->num_rows() >= 1) {
				$idcredito = $query->row()->idcredito;
				$this->db->where("idcredito", $idcredito)->update("credito.amortizacion", array("estado"=>"I"));
				$this->db->where("idcredito", $idcredito)->update("credito.letra", array("estado"=>"I"));
				$this->db->where("idcredito", $idcredito)->update("credito.credito", array("estado"=>"I"));
				
				//eliminamos la hoja de ruta de cobranzas
				$this->destroy_hojaruta($idcredito);
			}
		}
		
		// eliminamos el ingreso de kardex
		$this->load_library("jkardex");
		$this->jkardex->remove("venta", $idventa, $idsucursal);
		
		$this->db->trans_complete();
		
		$res["idventa"] = $idventa;
		$res["estado"] = "OK";
		$res["recibo_egreso"] = $recibo_egreso;
		$res["total"] = $this->venta->get("subtotal") + $this->venta->get("igv") - $this->venta->get("descuento");
		
		$this->response($res);
	}
	
	public function anular() {
		$this->load_model("venta.venta");
		$this->load_model("venta.venta_view");
		$this->venta_view->set_column_pk("idventa");
		
		$post = $this->input->post();
		// $post['controller']=$this->controller;
		// $post['accion']=__FUNCTION__;
		$this->venta_view->find($post["idventa"]);
		
		if($this->venta_view->get("estado") <> "A") {
			$this->exception("El comprobante ".$this->venta_view->get("comprobante")." se encuentra anulado");
			return false;
		}
		
		$this->eliminar($post["idventa"], "I");
		
		$var = $this->db->select("max(numero) as numero")
                        ->from("venta.baja")
                        ->where("idempresa", $_SESSION['idempresa'])
                        ->get()->row();
                if(empty($var->numero)){
                    $var->numero = 0;
                }
                
                $data_insert = array(
                    "idempresa"=>$_SESSION['idempresa'],
                    "idventa"=>$post["idventa"],
                    "fecha_emision"=>date('Y-m-d'),
                    "serie"=>'RA-'.date('Ymd'),
                    "numero"=>$var->numero+1,
                    "motivo"=>$post['motivo']
                );
                $this->db->insert('venta.baja',$data_insert);
	}
	
	public function restaurar($idventa) {
		$this->load_model("venta.venta");
		$this->load_model("venta.venta_view");
		
		$this->venta_view->set_column_pk("idventa");
		$this->venta_view->find($idventa);
		
		// comprobamos si continua anulado
		if($this->venta_view->get("estado") == "A") {
			$this->exception("El comprobante ".$this->venta_view->get("comprobante")." se encuentra activo");
			return false;
		}
		
		$this->db->trans_start();
		
		$this->venta->find($idventa);
		
		// restablecemos la venta
		$this->venta->update(array(
			"idventa" => $idventa
			,"motivo_anulacion" => null
			,"fecha_hora_anulacion" => null
			,"idusuario_anulacion" => null
			,"estado" => "A"
		));
		
		$idsucursal = $this->venta_view->get("idsucursal");
		
		// restauramos el despacho
		$this->db->where("idreferencia", $idventa)->where("referencia", "V")
			->update("almacen.despacho", array("estado"=>"A"));
		
		// restauramos la salida de stock del detalle_almacen
		$this->db->where("tabla", "V")->where("idtabla", $idventa)
			->update("almacen.detalle_almacen", array("estado"=>"A"));
			
		// restauramos la salida de las series en almacen
		$this->db->where("tabla_salida", "V")->where("idtabla_salida", $idventa)->where("estado", "A")
			->update("almacen.detalle_almacen_serie", array("despachado"=>"S"));
			
		// restauramos el detalle de la venta
		$this->db->where("idventa", $idventa)
			->update("venta.detalle_venta", array("estado"=>"A"));
			
		// restauramos las series de la venta
		$this->db->where("idventa", $idventa)
			->update("venta.detalle_venta_serie", array("estado"=>"A"));
		
		if($this->venta_view->get("idtipoventa") == 1) {
			// si se afecta a caja y ademas se ha hecho el cobro
			if($this->venta->get("afecta_caja") == "S" && $this->venta->get("cancelado") == "S") {
				// restauramos el ingreso a caja
				$this->load_library('pay');
				$this->pay->restore("venta", $idventa, $idsucursal);
			}
		}
		else {
			// restauramos el credito de la venta
			$query = $this->db->where("idventa", $idventa)->where("estado", "I")->get("credito.credito");
			if($query->num_rows() >= 1) {
				$idcredito = $query->row()->idcredito;
				$this->db->where("idcredito", $idcredito)->update("credito.amortizacion", array("estado"=>"A"));
				$this->db->where("idcredito", $idcredito)->update("credito.letra", array("estado"=>"A"));
				$this->db->where("idcredito", $idcredito)->update("credito.credito", array("estado"=>"A"));
			}
		}
		
		// restauramos el ingreso de kardex
		$this->load_library("jkardex");
		$this->jkardex->restore("venta", $idventa, $idsucursal);		
		
		$this->db->trans_complete();
		
		$this->response($this->venta_view->get_fields());
	}
	
	public function grilla_popup() {
		$this->load_model("venta.venta_view");
		$this->load->library('datatables');
		
		$credito = $this->input->get("c");
		$guia = $this->input->get("g");
		$nota_c = $this->input->get("nc");
		
		$this->datatables->setModel($this->venta_view);
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		if($credito == "S") { // grilla para credito
			// $this->datatables->where('idtipoventa', '=', 2); // ventas al credito
			$this->datatables->where('tipo_venta', 'ilike', '%credito%'); // ventas al credito
			$this->datatables->where('con_credito', '=', 'N');
		}
		if($guia == "S") { // grilla para las guias
			//$this->datatables->where('tipo_documento', 'ilike', '%factura%'); // ventas factura
			$this->datatables->where('con_guia', '=', 'N'); // si no tiene guia
			$this->datatables->order_by("fecha_venta", "desc");
		}
		if($nota_c == "S") { // grilla para las notas
			$anio = intval(date("Y"));
			if(intval(date("m")) == 1) {
				$anio = $anio - 1;
			}
			
			// $sql = $this->db->query("SELECT idventa FROM venta.notacredito WHERE estado='A'");
			// $ventas_nota = $sql->row_array();
			// $this->datatables->where("idventa", 'NOT IN', "(".implode(',', $ventas_nota).")");
			// echo fecha_es($anio."-01-01");
			$this->datatables->where("fecha_venta", '>=', "{$anio}-01-01");
			$this->datatables->where("con_notacredito", '=', "N");
		}
		
		$this->datatables->setColumns(array('fecha_venta','comprobante','full_nombres','moneda','total'));
		$this->datatables->setPopup(true);
		
		$table = $this->datatables->createTable(array('Fecha','Nro.Doc.','Cliente','Moneda','Total'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_all($idventa) {
		$this->load_model(array("venta.venta_view", "seguridad.sucursal", "seguridad.usuario"));
		$this->venta_view->set_column_pk("idventa");
		
		$res["venta"] = $this->venta_view->find($idventa);
		$res["sucursal"] = $this->sucursal->find($this->venta_view->get("idsucursal"));
		$res["vendedor"] = $this->usuario->find($this->venta_view->get("idvendedor"));
		
		$sql = "SELECT d.iddetalle_venta
				, d.descripcion
				, u.abreviatura
				, d.cantidad 
				,CASE WHEN oferta='N' THEN precio ELSE 0.00 END precio
				FROM venta.detalle_venta d 
				JOIN compra.unidad u on u.idunidad = d.idunidad
				WHERE d.estado='A' AND idventa=?
				ORDER BY iddetalle_venta";
		$query = $this->db->query($sql, array($idventa));
		
		$res["detalle_venta"] = $query->result_array();
		$id_zona = $this->venta_view->get('idzona');
		if(empty($id_zona))
			$id_zona = 0;
		$query = $this->db->query("SELECT DISTINCT h.idcobrador,u.user_nombres cobrador FROM cobranza.hoja_ruta h
									JOIN seguridad.view_usuario u ON u.idusuario=h.idcobrador
									WHERE h.idzona={$id_zona} 
									AND h.idsucursal={$this->venta_view->get('idsucursal')}
									AND h.estado='A'");
		$res["cobradores"] = $query->result_array();
		$this->response($res);
	}
	
	public function is_valid_cobro($id) {
		$this->load_model("venta.venta");
		$this->venta->find($id);
		
		if($this->venta->get("estado") != "A") {
			$this->exception("La venta se encuentra anulado.");
			return false;
		}
		if($this->venta->get("cancelado") == "S") {
			$this->exception("Ya se ha hecho el cobro de la venta.");
			return false;
		}
		if($this->venta->get("idtipoventa") != 1) { // venta no es al contado, supongo que es al credito
			$this->exception("Para cobrar una venta al credito, ingrese al modulo de cobros o pagos del cliente.");
			return false;
		}
		
		$res = $this->venta->get_fields();
		$res["total"] = $res["subtotal"] + $res["igv"] - $res["descuento"];
		
		$this->response($res);
	}
	
	public function cobrar() {
		$post = $this->input->post();
		$post['afecta_caja'] = ( ! empty($post['afecta_caja'])) ? "S" : "N";
		
		if($post["afecta_caja"] == "N") {
			$this->exception("Por favor indique el tipo de pago.");
			return false;
		}
		
		$this->load_model(array("venta.venta", "venta.cliente"));
		
		$this->venta->find($post["idventa"]);
		$this->cliente->find($this->venta->get("idcliente"));
		
		if($this->venta->get("estado") != "A") {
			$this->exception("La venta ha sido anulado.");
			return false;
		}
		
		if($this->venta->get("cancelado") == "S") {
			$this->exception("La venta ya se ha cobrado.");
			return false;
		}
		
		// datos necesarios para la libreria pay
		$post["descripcion"] = "VENTA AL CONTADO";
		$post["referencia"] = $this->cliente->get("nombres").' '.$this->cliente->get("apellidos");
		$post["tabla"] = "venta";
		$post["idoperacion"] = $this->venta->get("idventa");
		$post["numero"] = $this->venta->get("correlativo");
		
		// cargamos libreria y controlador
		$this->load_controller("caja");
		$this->load->library('pay');
		
		// inicio de la transaccion
		$this->db->trans_start();
		
		// ingreso de movimiento en caja
		$this->pay->set_controller($this->caja_controller);
		$this->pay->set_data($this->venta->get_fields());
		$this->pay->set_data($post);
		$this->pay->entrada();
		$this->pay->process();
		
		// actualizamos el estado de la venta
		$this->venta->set($post);
		$this->venta->set("cancelado", "S");
		$this->venta->update();
		
		// finalizamos transaccion
		$this->db->trans_complete();
		
		$this->response("ok");
	}
	
	public function get_detalle($idventa) {
		$sql = "select dv.iddetalle_venta, dv.descripcion as producto, u.descripcion as unidad,
			dv.cantidad, dv.afecta_stock as controla_stock, dv.afecta_serie as controla_serie, 
			dv.idalmacen, dv.idproducto, dv.precio, dv.idunidad, 
			array_to_string(array_agg(dvs.serie), '|'::text) as serie
			from venta.detalle_venta dv
			join compra.unidad u on u.idunidad = dv.idunidad
			left join venta.detalle_venta_serie dvs on dvs.iddetalle_venta=dv.iddetalle_venta 
				and dvs.idventa=dv.idventa and dvs.idproducto=dv.idproducto and dvs.estado='A'
			where dv.estado = 'A' and dv.idventa = ?
			group by dv.iddetalle_venta, dv.descripcion, u.descripcion, dv.cantidad, dv.afecta_stock, 
				dv.afecta_serie, dv.idalmacen, dv.idproducto, dv.idventa, dv.precio, dv.idunidad
			order by iddetalle_venta";
		$query = $this->db->query($sql, array($idventa));
		$this->response($query->result_array());
	}
	
	public function tipo_doc_anular(){
		$query = $this->db->query("SELECT idtipodocumento,descripcion FROM venta.tipo_documento WHERE mostrar_en_venta='S';");
		$this->response($query->result_array());
	}
	
	public function anular_vacio(){
		$post 		= $this->input->post();
		$idsucursal	= $this->get_var_session("idsucursal");
		$query 		= $this->db->query("SELECT * FROM 
										venta.venta 
										WHERE estado='A' 
										AND serie='{$post['serie']}' 
										AND correlativo='{$post['numero']}' 
										AND idtipodocumento='{$post['idtipodocumento']}' 
										AND idsucursal='$idsucursal';");
		$res = $query->result_array();
		if( empty($post['serie']) || empty($post['numero']) || empty($post['idtipodocumento']) ){
			$res['sms']    = "El campo serie, numero y tipo documento no debe estar vac&iacute;o ....!!!";
			$res['status'] = "0";
			$this->response($res);
			return false;
		}
		if(empty($res)){
			$this->load_model($this->controller);
			$fields['idventa'] = '';
			$fields['idsucursal'] = $idsucursal;
			$fields['idusuario'] = $this->get_var_session("idusuario");
			
			if(empty($fields["fecha_venta"]))
				$fields['fecha_venta'] = date("Y-m-d");
			
			$fields['serie'] 				= $post['serie'];
			$fields['correlativo'] 			= $post['numero'];
			$fields['idtipodocumento'] 		= $post['idtipodocumento'];
			$fields['fecha_registro'] 		= date("Y-m-d H:i:s");
			$fields['pasa_despacho'] 		= (empty($fields['pasa_despacho'])) ? "N" : "S";
			$fields['idtipopago'] 			= (!empty($fields['idtipopago'])) ? $fields['idtipopago'] : 0;
			$fields['vuelto'] 				= (!empty($fields['monto_vuelto'])) ? $fields['monto_vuelto'] : 0;
			$fields['afecta_caja'] 			= (!empty($fields['afecta_caja'])) ? "S" : "N";
			$fields['despachado'] 			= ($fields['pasa_despacho'] == "S") ? "N" : "S";
			$fields['cancelado'] 			=  "N";
			$fields['estado'] 				= "I";
			$fields['idtipoventa'] 			= 1;
			$fields['idalmacen'] 			= 1;
			$fields['cambio_moneda'] 		= 0.00;
			$fields['idvendedor'] 			= $this->get_var_session("idusuario");
			$fields["con_credito"] 			= "N";
			
			$fields["motivo_anulacion"] 	= $post['motivo'];
			$fields["fecha_hora_anulacion"]	= date("Y-m-d H:i:s");
			$fields["idusuario_anulacion"] 	= $this->get_var_session("idusuario");
			
			if(empty($fields["descuento"]))
				$fields["descuento"] 		= 0;
			if(empty($fields["igv"]))
				$fields["igv"] 				= 0;
			if(empty($fields["monto_entregado"]))
				$fields["monto_entregado"] 	= 0;
			if($fields["afecta_caja"] == "N")
				$fields['idtipopago'] 		= 0;
			if(empty($fields["idpreventa"]))
				$fields["idpreventa"] 		= 0;
			if(empty($fields["idcliente"]))
				$fields["idcliente"] 		= 0;
			
			$this->db->trans_start(); // inciamos transaccion
			$post['idventa'] = $this->venta->insert($fields);

			$this->db->trans_complete(); // finalizamos transaccion
			
			$res['sms']    = "El comprobante {$post['t_documento']} {$post['serie']} - {$post['numero']} se anul&oacute; correctamente...!!!!!!";
			$res['status'] = "1";
			$this->response($res);
		}else{
			$res['sms']    = "El comprobante {$post['t_documento']} {$post['serie']} - {$post['numero']} no est&aacute; vac&iacute;o o ya se encuentra anulado.";
			$res['status'] = "0";
			$this->response($res);
		}
	}
	
	public function imprimir($id){
		$this->load_model(array('venta.venta','venta.tipo_documento','venta.venta_view'));
		$this->load->library('numeroLetra');
		
		$this->venta->find($id);
		$venta_view = $this->venta_view->find(array("idventa"=>$id));
		
		// verificamos si corresponde a la facturacion electronica
		$cdp = $this->tipo_documento->find($this->venta->get("idtipodocumento"));
		$fe = $this->get_param("facturacion_electronica");
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		
		if($cdp["facturacion_electronica"] == "S" && $fe == "S") {
			$this->imprimir_formato($id,"venta","venta",false,$fc);
			return;
		}

		$idsucursal 	 = $this->venta->get("idsucursal");
		$idtipodocumento = $this->venta->get("idtipodocumento");
		$serie 			 = $this->venta->get("serie");

		$sql = $this->db->query("SELECT contenido,cantidad_filas_detalle,ver_borde FROM general.formato_documento WHERE estado='A' AND idtipodocumento='$idtipodocumento' AND serie='$serie' AND idsucursal='$idsucursal';");
		$reg 				= $sql->row('contenido');
		$cant_filas_detalle = $sql->row('cantidad_filas_detalle');
		$ver_borde  	 	= $sql->row("ver_borde");
		
		$border = 'none';
		if($ver_borde=='S'){
			$border = '0.5px solid #ccc';
		}
		
		$this->load_model('general.formato_documento');
		$this->formato_documento->find(array("idtipodocumento"=>$idtipodocumento,"idsucursal"=>$idsucursal,"serie"=>$serie));
		if(!empty($reg)){
			$sql = $this->db->query("SELECT (COALESCE(td.abreviatura||'-','')||v.serie||'-'||v.correlativo) comprobante_op,
			(COALESCE(c.nombres||' ','')||COALESCE(c.apellidos,'')) nombre_cliente
									,c.dni dni_cliente
									,c.ruc ruc_cliente
									,to_char(v.fecha_venta,'DD/MM/YYYY') f_op
									,CASE WHEN v.cancelado='S' THEN to_char(v.fecha_venta,'DD/MM/YYYY')ELSE '' END f_pago
									,(SELECT direccion dir FROM venta.cliente_direccion cdir WHERE cdir.dir_principal='S' 
										AND cdir.idcliente=c.idcliente) direccion_cliente
									,(SELECT COALESCE(gr.serie||'-','')||COALESCE(gr.numero,'') FROM almacen.guia_remision gr 
										WHERE gr.referencia='V' AND gr.idreferencia=v.idventa AND gr.estado='A') nro_guia_remision
									,CAST(v.subtotal AS numeric(10,2)) subt_op
									,v.igv igv_op
									,CAST((v.subtotal+v.igv-v.descuento) AS numeric(10,2)) total_op 
									,(v.subtotal+v.igv) total_letras
									,(COALESCE(vend.nombres||' ','')||COALESCE(vend.appat)||COALESCE(vend.apmat)) vendedor
									
									FROM venta.venta v
									JOIN venta.cliente c ON c.idcliente=v.idcliente
									JOIN venta.tipo_documento td ON td.idtipodocumento=v.idtipodocumento
									JOIN seguridad.usuario vend ON vend.idusuario=v.idvendedor
									WHERE v.idventa=$id");
			$dato = $sql->row_array();
			foreach($dato as $k=>$v){
				if($k=='total_letras'){
					$v = $this->numeroletra->convertir(number_format($v, 2, '.', ''), true)." ".$this->venta_view->get("moneda");
				}
				$reg=str_replace("{".$k."}",$v,$reg);

			}

			$sql = $this->db->query("SELECT (ROW_NUMBER() OVER (ORDER BY idventa))||':::'||COALESCE(dv.descripcion,'') d_descripcion
									,(ROW_NUMBER() OVER (ORDER BY idventa))||':::'||CAST(dv.cantidad AS numeric(10,2)) d_cant
									,(ROW_NUMBER() OVER (ORDER BY idventa))||':::'||CAST(dv.precio AS numeric(10,2)) d_pu 
									,(ROW_NUMBER() OVER (ORDER BY idventa))||':::'||CAST(dv.precio*dv.cantidad AS numeric(10,2)) d_imp
									FROM venta.detalle_venta dv WHERE dv.idventa=$id AND dv.estado='A'
									ORDER BY (ROW_NUMBER() OVER (ORDER BY idventa));");

			$detalle = $sql->result_array();

			$dato_detalle=0;
			foreach($detalle as $k=>$v){
				foreach($v as $key=>$val){
					$extend = explode(":::",$val);
					$reg=str_replace("{".$key.$extend[0]."}",$extend[1],$reg);
				}
				$dato_detalle++;
			}

			for($xy=($dato_detalle + 1);$xy<=$cant_filas_detalle;$xy++){
				foreach($v as $key=>$val){
					$reg=str_replace("{".$key.$xy."}",'',$reg);
				}
			}
			
			echo "<style>";
			echo "	.panel-body{border:0px solid black;}";
			echo "";
			echo "@media print,screen{
				@page{
					margin: 0;
					size: ".$this->formato_documento->get('width')." ".$this->formato_documento->get('height')."
				}
				*{
					margin: 0px;font-family: ".$this->formato_documento->get('fuente_letra').";font-size:".$this->formato_documento->get('font_size').";
				}
				#content{width:".$this->formato_documento->get('width').";height:".$this->formato_documento->get("height").";border:0px solid #ccc; }
				table td,table{border:$border !important;}
				table thead tr td{border:none !important;}
				table{border-top: 0px !important;border-left: 0px !important;border-right: 0px !important;}
			}";

			echo "</style>";
			echo "<div id='content'>".$reg."</div>";
			// echo "<script>window.print();</script>";
			// echo "<script>window.close();</script>";
		}else{
			echo "Error, formato no definido :(";
		}
	}
	
	public function print_test($id,$in_tcpdf=false){
		if(!$in_tcpdf)
			$this->imprimir_formato($id,"venta","venta",true);
		else
			$this->formato_personalizado_1($id);
	}
	

	public function formato_personalizado_1($id=0, $data=array(),$fixed = 2){
		$this->load->library("tcpdf");
		$this->load->library("pdf");
		$this->load->library('numeroLetra');
		
		$this->load_model(array( "seguridad.empresa","seguridad.sucursal","seguridad.view_usuario","venta.venta_view","venta.tipo_documento","venta.facturacion"));
		$this->empresa->find($this->get_var_session("idempresa"));
		$venta_view = $this->venta_view->find(array("idventa"=>$id));
		
		$this->tipo_documento->find($this->venta_view->get("idtipodocumento"));
		$this->sucursal->find($this->get_var_session("idsucursal"));

		if(empty($data)){
			$data=$this->facturacion->find(array("idreferencia"=>$id));
		}
		
		$label_doc="R.U.C.";
		$doc_cli = $this->venta_view->get("ruc");
		// echo "<pre>";
		// print_r($venta_view);exit;
		if($venta_view['idtipodocumento']=='002'){//BOLETA
			$label_doc="D.N.I.";
			$doc_cli = $this->venta_view->get("dni");
		}
		// echo FCPATH;exit;
		// $this->tcpdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		
		$this->tcpdf->setPrintHeader(false);
		$this->tcpdf->setPrintFooter(true);
		
		$this->tcpdf->SetLeftMargin(4);
		$this->tcpdf->AddPage(); // para el conteo de paginas
		$this->tcpdf->SetFont('Helvetica','',10);
		// $this->tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);   
		// $this->tcpdf->SetFont('Helvetica','',10);
		$this->tcpdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"), 3, 8, 25, 25);
		// $this->tcpdf->Cell(45, 0, 'TEST CELL STRETCH: scaling', 1, 1, 'C', 0, '', 1);
		$this->tcpdf->setFillColor(249, 249, 249);
        $this->tcpdf->SetDrawColor(204, 204, 204);
		
		$alto_comprob=9;
		$this->tcpdf->Cell(130,$alto_comprob,'',0,0,'L');
		$this->tcpdf->Cell(65,$alto_comprob,('R.U.C. N° ').$this->empresa->get("ruc"),'LTR',1,'C');
		
		$this->tcpdf->Cell(35,$alto_comprob,'',0,0,'L');
		$this->tcpdf->SetFont('Helvetica','',15);
		$this->tcpdf->Cell(95,$alto_comprob,$this->empresa->get("descripcion"),0,0,'C');
		$this->tcpdf->SetFont('Helvetica','',10);
		$this->tcpdf->Cell(65,$alto_comprob,$this->tipo_documento->get('descripcion'),'LR',1,'C');
		
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell(35,$alto_comprob,'',0,0,'L');
		$this->tcpdf->Cell(95,$alto_comprob,$this->empresa->get("direccion")." - Telf. ".$this->sucursal->get("telefono"),0,0,'C');

		$this->tcpdf->SetFont('Helvetica','',10);
		$this->tcpdf->Cell(65,$alto_comprob,$data['serie']."-".$data['numero'],'LBR',1,'C');
		
		$this->tcpdf->Cell(125,3,'',0,1,'C');
		
		$this->tcpdf->Ln();
		$this->tcpdf->SetFont('Helvetica', 'B', 9);
		$this->tcpdf->Cell(20,6,('SEÑOR(ES)'),0,0,'L');
		$this->tcpdf->Cell(2,6,(':'),0,0,'C');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell(128,6,($this->venta_view->get("full_nombres")),0,0,'L');
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell(20,6,'   '.$label_doc,0,0,'L');
		$this->tcpdf->Cell(2,6,(':'),0,0,'C');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell(36,6,$this->venta_view->get("ruc"),0,1,'L');
		
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell(20,6,('DIRECCIÓN'),0,0,'L');
		$this->tcpdf->Cell(2,6,(':'),0,0,'C');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell(128,6,$this->venta_view->get("direccion"),0,0,'L');

		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell(20,6,'   FECHA',0,0,'L');
		$this->tcpdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell(36,6,$this->venta_view->get("fecha_venta_format"),0,1,'L');
		
		
		
		$this->tcpdf->Ln();
		
		// $this->tcpdf->Cell(190,0,"",1,1,'C');
		
		/* elementos array('key_colum',array('name_colum','ancho','align','salto')) */
		/* Cabecera */
		$width_cod=18;
		$width_cant=20;
		$width_um=15;
		$width_descr=102;
		$width_vu=22;
		$width_vtotal=25;
		
		$cabecera = array('idproducto'=> array('CÓDIGO',$width_cod,'R',0)
							,'cantidad' => array('CANT',$width_cant,'R',0)
							,'um' => array('UM',$width_um,'C',0)
							,'detalle' => array('DESCRIPCIÓN',$width_descr,'L',0)
							,'precio' => array('V. UNITARIO',$width_vu,'R',0)
							,'importe' => array('V. VENTA T.',$width_vtotal,'R',1)
						);
		$this->tcpdf->SetFont('Helvetica','B',8);
		
		foreach($cabecera as $f=>$b){
			$this->tcpdf->Cell(($b[1]),6,((($b[0]))),1,$b[3],'C', true);
		}
		$this->tcpdf->SetFont('Helvetica','',8);
		/* Cabecera */
					
		$total_importe=0;
		$totalGra = $totalIna = $totalExo = $sumaIgv = $total_descuento = $totalOferta = 0;
		// $detalle = $sql->result_array();
		$cols = array('idproducto','cantidad','um','detalle','precio','importe');
		$pos = array("R", "R", "C", "L", "R", "R");
		$width = array($width_cod, $width_cant, $width_um, $width_descr, $width_vu, $width_vtotal);
		$detalle = $this->detalle_impresion($id,'venta');
		foreach($detalle as $k=>$v){
			// foreach($cabecera as $f=>$b){
				// $this->tcpdf->Cell(($b[1]),5,((($v[$f]))),1,$b[3],$b[2]);
			// }
			$this->tcpdf->SetWidths($width);
			$values = array();			
			
			if($v["codgrupo_igv"] == "GRA")
				$totalGra += redondeosunat($v["valor_venta"],$fixed);
			else if($v["codgrupo_igv"] == "EXO")
				$totalExo += redondeosunat($v["valor_venta"],$fixed);
			else if($v["codgrupo_igv"] == "INA")
				$totalIna += redondeosunat($v["valor_venta"],$fixed);
				// $totalIna += $v["valor_venta"];
				
			$sumaIgv += $v["sum_igv"];
			
			if($v["oferta"] == "S") {
				$totalOferta += $v["pu_real"] * $v["cantidad"];
			}
			
			foreach($cols as $f){
				if(!empty($v['serie']) && $f=='detalle'){
					$v[$f] = $v[$f]." SERIE: ".$v['serie'];
				}else if($f=='importe'){
					$v[$f] = redondeosunat($v[$f],$fixed);
				}
				$values[] = utf8_decode((($v[$f])));
			}
			
			$this->tcpdf->Row($values, $pos, "Y", "Y");
			
			$total_importe = $total_importe + $v['importe'];
			$total_descuento = $total_descuento + $v['descuento'];
		}
		$importeTotal = $totalGra + $totalIna + $totalExo + $sumaIgv - $venta_view['descuento'];
		/* Cuerpo */
		
		
		/* Pie */
		$this->tcpdf->SetFont('Helvetica','',9);
		$width_monto_descrp = $width_cant + $width_um +$width_descr;
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_cod ,4,"SON : ",1,0,'R');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_monto_descrp,4,$this->numeroletra->convertir(number_format($importeTotal, 2, '.', ''), true)." ".$this->venta_view->get("moneda"),1,0,'L');
		$this->tcpdf->SetFont('Helvetica','B',7);
		$this->tcpdf->Cell($width_vu ,4,"T. DESCUENTO",1,0,'L');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_vtotal ,4,number_format($total_descuento,2),1,1,'R');
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_cod ,4,"",0,0,'R');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell($width_monto_descrp,4,"",0,0,'L');
		$this->tcpdf->SetFont('Helvetica','B',7);
		$this->tcpdf->Cell($width_vu ,4,"OP. GRAVADA",1,0,'L');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_vtotal ,4,number_format($totalGra,$fixed,'.',','),1,1,'R');
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_cod ,4,"",0,0,'R');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell($width_monto_descrp,4,"",0,0,'L');
		$this->tcpdf->SetFont('Helvetica','B',7);
		$this->tcpdf->Cell($width_vu ,4,"OP. INAFECTA",1,0,'L');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_vtotal ,4,number_format($totalIna,$fixed,'.',','),1,1,'R');
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_cod ,4,"",0,0,'R');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell($width_monto_descrp,4,"",0,0,'L');
		$this->tcpdf->SetFont('Helvetica','B',7);
		$this->tcpdf->Cell($width_vu ,4,"OP. EXONERADA",1,0,'L');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_vtotal ,4,number_format($totalExo,$fixed,'.',','),1,1,'R');
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_cod ,4,"",0,0,'R');
		$this->tcpdf->SetFont('Helvetica','',9);
		$this->tcpdf->Cell($width_monto_descrp,4,"",0,0,'L');
		$this->tcpdf->SetFont('Helvetica','B',7);
		$this->tcpdf->Cell($width_vu ,4,"TOTAL IGV",1,0,'L');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_vtotal ,4,number_format($venta_view['igv'],$fixed,'.',','),1,1,'R');
		
		if($venta_view["descuento"] > 0) {
			$this->tcpdf->SetFont('Helvetica','B',9);
			$this->tcpdf->Cell($width_cod ,6,"",0,0,'R');
			$this->tcpdf->SetFont('Helvetica','B',9);
			$this->tcpdf->Cell($width_monto_descrp,6,"",0,0,'L');
			$this->tcpdf->SetFont('Helvetica','B',9);
			$this->tcpdf->Cell($width_vu ,4,"DSCTO. GLOBAL",1,0,'L');
			$this->tcpdf->SetFont('Helvetica','B',8);
			$this->tcpdf->Cell($width_vtotal ,4,number_format($venta_view['descuento'],2,'.',','),1,1,'R');
		}
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_cod ,6,"",0,0,'R');
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_monto_descrp,6,"",0,0,'L');
		$this->tcpdf->SetFont('Helvetica','B',6);
		$this->tcpdf->Cell($width_vu ,4,"OP. GRATUITA",1,0,'L');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_vtotal ,4,number_format($totalOferta,$fixed,'.',','),1,1,'R');
		
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_cod ,6,"",0,0,'R');
		$this->tcpdf->SetFont('Helvetica','B',9);
		$this->tcpdf->Cell($width_monto_descrp,6,"",0,0,'L');
		$this->tcpdf->SetFont('Helvetica','B',7);
		$this->tcpdf->Cell($width_vu ,6,"IMPORTE TOTAL",1,0,'L');
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->Cell($width_vtotal ,6,number_format($importeTotal,$fixed,'.',','),1,1,'R');
		/* Pie */
		
		
		/* Content PDF FOOT */
		$this->tcpdf->SetDrawColor(0, 0, 0);
		$this->tcpdf->setY(-76);
		$this->tcpdf->SetFont('Helvetica','',10);
		$this->tcpdf->Cell(202 ,6,$data['resumen_value'],0,1,'C');
		$this->tcpdf->setY(-33);
		$this->tcpdf->SetFont('Helvetica','',10);
		$style = array(
			'border' => 0,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);
		// echo "<pre>";
		// print_r($data);exit;

		if($this->venta_view->get('idtipodocumento')==2){//BOLETA
			$data['tipo_doc_adquiriente']='DNI';
			$data['nro_doc_adquiriente']=$this->venta_view->get('dni');
		}else{//FACTURA
			$data['tipo_doc_adquiriente']='RUC';
			$data['nro_doc_adquiriente']=$this->venta_view->get('ruc');
		}
		
		if(empty($data['resumen_value']))
			$data['resumen_value']='';
		
		if(empty($data['resumen_firma']))
			$data['resumen_firma']='';
		
		$code_bar = $data['num_ruc'];
		$code_bar.= "|".$data['tip_docu'];
		$code_bar.= "|".$data['serie'];
		$code_bar.= "|".$data['numero'];
		$code_bar.= "|".$this->venta_view->get('igv');
		$code_bar.= "|".$this->venta_view->get('total');
		$code_bar.= "|".$this->venta_view->get("fecha_venta_format");
		$code_bar.= "|".$data['tipo_doc_adquiriente'];
		$code_bar.= "|".$data['nro_doc_adquiriente'];
		$code_bar.= "|".$data['resumen_value'];
		$code_bar.= "|".$data['resumen_firma'];
		// echo $code_bar."<br>";
		$this->tcpdf->write2DBarcode($code_bar, 'PDF417', 55, 226,100,28,$style,'',true);
			// Parametros
			// Codigo de barra
			// 60 = Formato
			// 226 = centrar, de izquierda a derecha
			// 100 = altura(Y)
			// 28 = ancho
			//$style = estilos
			//'' = alineacion (T,M,N,B)
			// true = distorcionar proporcion automatica del ancho
			
		$this->tcpdf->SetFont('Helvetica','',8);
		$this->tcpdf->MultiCell(202 ,6,("esentación impresa de la boleta de venta electrónica generada desde el sistema facturador SUNAT. Puede verificarla utilizando su clave SOL"),0,'J');
		/* Content PDF FOOT */
		
		

		$this->tcpdf->Output();
	}
}

?>