<?php

include_once "Controller.php";

class Compra extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Compra");
		$this->set_subtitle("Lista de compras");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// $this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		// $this->js('form/'.$this->controller.'/index');
		// $this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		
		$this->load->library('combobox');
		
		// combo tipo compra
		$this->combobox->setAttr("id", "idtipoventa");
		$this->combobox->setAttr("name", "idtipoventa");
		$this->combobox->setAttr("class", "form-control input-sm");
		$this->combobox->setAttr("required", "");
		
		$this->db->select('idtipoventa, descripcion');
		$query = $this->db->where("estado", "A")->where("mostrar_en_compra", "S")->get("venta.tipo_venta");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["compra"]["idtipoventa"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idtipoventa"]);
		}
		
		$data["tipocompra"] = $this->combobox->getObject();
		
		// combo tipodocumento
		$this->combobox->init(); // un nuevo combo
		
		$this->combobox->setAttr(
			array(
				"id"=>"idtipodocumento"
				,"name"=>"idtipodocumento"
				,"class"=>"form-control input-sm"
				,"required"=>""
			)
		);
		$this->db->select('idtipodocumento, descripcion');
		$query = $this->db->where("estado", "A")->where("mostrar_en_compra", "S")->get("venta.tipo_documento");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["compra"]["idtipodocumento"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idtipodocumento"]);
		}
		
		$data["tipodocumento"] = $this->combobox->getObject();
		
		// combo almacen
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idalmacen"
				,"name"=>"idalmacen"
				,"class"=>"form-control input-sm"
				,"required"=>""
			)
		);
		$query = $this->db->select('idalmacen, descripcion')->where("estado", "A")->where("mostrar_en_compra", "S")
			->where("idsucursal", $this->get_var_session("idsucursal"))->get("almacen.almacen");
		$this->combobox->addItem($query->result_array());
		if( isset($data["compra"]["idalmacen"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idalmacen"]);
		}
		$data["almacen"] = $this->combobox->getObject();
		
		// combo moneda
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idmoneda"
				,"name"=>"idmoneda"
				,"class"=>"form-control input-sm"
				,"required"=>""
			)
		);
		$query = $this->db->select('idmoneda, descripcion, abreviatura')->where("estado", "A")->order_by("idmoneda", "asc")->get("general.moneda");
		$this->combobox->addItem($query->result_array(), array('idmoneda', 'descripcion', 'abreviatura'));
		if( isset($data["compra"]["idmoneda"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idmoneda"]);
		}
		$data["moneda"] = $this->combobox->getObject();

		// combo moneda flete
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idmoneda_flete"
				,"name"=>"idmoneda_flete"
				,"class"=>"combo_min"
			)
		);
		$query = $this->db->select('idmoneda, abreviatura')->where("estado", "A")->order_by("idmoneda", "asc")->get("general.moneda");
		$this->combobox->addItem($query->result_array(), array('idmoneda', 'abreviatura', 'abreviatura'));
		if( isset($data["compra"]["idmoneda_flete"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idmoneda_flete"]);
		}
		$data["moneda_flete"] = $this->combobox->getObject();
		
		// combo moneda gastos
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idmoneda_gastos"
				,"name"=>"idmoneda_gastos"
				,"class"=>"combo_min"
			)
		);
		$query = $this->db->select('idmoneda, abreviatura')->where("estado", "A")->order_by("idmoneda", "asc")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		if( isset($data["compra"]["idmoneda_gastos"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idmoneda_gastos"]);
		}
		$data["moneda_gastos"] = $this->combobox->getObject();
		
		
		// forma pago compra
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idforma_pago_compra"
				,"name"=>"idforma_pago_compra"
				,"class"=>"form-control input-sm"
			)
		);
		$query = $this->db->select('idforma_pago_compra, descripcion')->where("estado", "A")->get("compra.forma_pago_compra");
		$this->combobox->addItem($query->result_array());
		if( isset($data["compra"]["idforma_pago_compra"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idforma_pago_compra"]);
		}
		$data["forma_pago_compra"] = $this->combobox->getObject();
		
		$data["controller"] = $this->controller;
		
		$igv = $this->get_param("igv");
		if(!is_numeric($igv)) {
			$igv = 18;
		}
		$data["valor_igv"] = $igv;
		
		$es_nuevo = "true";
		if( isset($data["compra"]["idcompra"]) ) {
			$es_nuevo = "false";
		}
		$this->js("<script>var _es_nuevo_".$this->controller."_ = $es_nuevo;</script>", false);
		
		$this->load_controller("producto");
		// $this->producto_controller->load = $this->load;
		// $this->producto_controller->db = $this->db;
		// $this->producto_controller->session = $this->session;
		// $this->producto_controller->combobox = $this->combobox;
		
		$data["form_producto"] = $this->producto_controller->form(null, "", true);
		$data["form_producto_unidad"] = $this->producto_controller->form_unidad_medida(null, "", true);
		$data["modal_pago"] = $this->get_form_pago("compra", true);
		
		$fc = $this->get_param("fixed_compra");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		$this->js("<script>var _fixed_compra = $fc;</script>", false);
		
		$es_nuevo = "true";
		if( isset($data["compra"]["idcompra"]) ) {
			$es_nuevo = "false";
		}
		$this->js("<script>var _es_nuevo_".$this->controller."_ = $es_nuevo;</script>", false);
		
		if( isset($data["detalle"]) ) {
			$this->js("<script>var data_detalle = ".json_encode($data["detalle"]).";</script>", false);
		}
		
		$this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		// $this->css('plugins/iCheck/custom');
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		// $this->js('plugins/iCheck/icheck.min');
		$this->js('form/'.$this->controller.'/form');
		$this->js('form/producto/modal');
		
		// formulario PROVEEDOR
		$this->load_controller("proveedor");
		// $this->proveedor_controller->load = $this->load;
		// $this->proveedor_controller->db = $this->db;
		// $this->proveedor_controller->session = $this->session;
		// $this->proveedor_controller->combobox = $this->combobox;
		$data["form_proveedor"] = $this->proveedor_controller->form(null, "prov_", true);

		$this->js('form/proveedor/modal');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * filtros de la grilla 
	 */
	public function filtros_grilla() {
		$this->load_library("combobox");
		
		$html = '<div class="row">';
		
		// div y combobox recepcionado
		$this->combobox->setAttr("filter", "recepcionado");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->addItem("", "TODOS");
		$this->combobox->addItem("S", "RECEPCIONADO");
		$this->combobox->addItem("N", "NO RECEPCIONADO");
		
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">Recepcionado</label>';
		$html .= $this->combobox->getObject();
		$html .= '</div></div>';
		
		$html .= '</div>';
		
		$this->set_filter($html);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("compra.compra_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->compra_view);
		$this->datatables->setIndexColumn("idcompra");
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->setColumns(array('fecha_compra','tipo_documento',
			'nrodocumento','proveedor','tipoventa','moneda','subtotal','igv','descuento','total'));
		
		$this->datatables->order_by("fecha_compra", "desc"); // desc default
		
		$this->datatables->setCallback("verificarRecepcionados");
		
		$columnasName = array('F.Compra','Documento','Nro.Documento','Proveedor',
			'Tipo compra','Moneda','Subtotal','IGV','Descuento','Total');

		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		// $this->css('plugins/dataTables/dataTables.bootstrap');
		// $this->css('plugins/dataTables/dataTables.responsive');
		// $this->css('plugins/dataTables/dataTables.tableTools.min');
		
		// $this->js('plugins/dataTables/jquery.dataTables');
		// $this->js('plugins/dataTables/dataTables.bootstrap');
		// $this->js('plugins/dataTables/dataTables.responsive');
		// $this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js($script, false);
		
		$this->filtros_grilla();
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Compra");
		$this->set_subtitle("");
		
		$this->set_content($this->form());
		$this->index("content_empty");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data["compra"] = $this->compra->find($id);
		$data["total"] = $data["compra"]["subtotal"] + $data["compra"]["igv"] - $data["compra"]["descuento"];
		
		$this->load_model("detalle_compra");
		$data["detalle"] = $this->detalle_compra->get_items($id);
		
		$this->load_model("proveedor");
		$data["proveedor"] = $this->proveedor->find($data["compra"]["idproveedor"]);
		
		// verificamos si la compra es editable
		$is_editable = true;
		
		// revisamos si se ha hecho alguna recepcion
		$sql = "SELECT * FROM compra.detalle_compra
			WHERE idcompra=? AND estado=? AND recepcionado=? and afecta_stock=?";
		$query = $this->db->query($sql, array($id, "A", "S", "S"));
		if($query->num_rows() > 0) {
			// comprobamos la fecha de registro de la compra
			$arr = explode(" ", $this->compra->get("fecha_registro"));
			$fecha_reg = array_shift($arr);
			if($fecha_reg != date("Y-m-d")) {
				$is_editable = false;
			}
		}
		$data["editable"] = $is_editable;
		
		$this->set_title("Modificar Compra");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content_empty");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		$this->load_model("general.moneda");
		$this->load_model("compra.cronograma_pago");
		
		$fields = $this->input->post();
		
		$fields['idsucursal'] = $this->get_var_session("idsucursal");

		$fields['idusuario'] = $this->get_var_session("idusuario");
		$fields['fecha_registro'] = date("Y-m-d");
		$fields['recepcionado'] = (!empty($fields['recepcionado'])) ? "S" : "N";
		$fields['afecta_caja'] = (!empty($fields['afecta_caja'])) ? "S" : "N";
		$fields['estado'] = "A";
		if(empty($fields["descuento"]))
			$fields["descuento"] = 0;
		if(empty($fields["gastos"]))
			$fields["gastos"] = 0;
		if(empty($fields["flete"]))
			$fields["flete"] = 0;
		if(empty($fields["igv"]))
			$fields["igv"] = 0;
		if(empty($fields["fecha_compra"]))
			$fields["fecha_compra"] = date("Y-m-d");
		if(empty($fields["nro_letras"]))
			$fields["nro_letras"] = 1;
		if(empty($fields["idmoneda_gastos"]))
			$fields["idmoneda_gastos"] = 1;
		if(empty($fields["idmoneda_flete"]))
			$fields["idmoneda_flete"] = 1;
		if(empty($fields["cambio_moneda_flete"]))
			$fields["cambio_moneda_flete"] = 1;
		
		$m_flete = $this->moneda->find($fields['idmoneda_flete']);
		$m_gasto = $this->moneda->find($fields['idmoneda_gastos']);
		
		if(empty($fields["flete_convertido"]))
			$fields["flete_convertido"] = floatval($fields["flete"])*floatval($m_flete['valor_cambio']);
		if(empty($fields["gastos_convertido"]))
			$fields["gastos_convertido"] = floatval($fields["gastos"])*floatval($m_gasto['valor_cambio']);

		$esNuevaCompra = (empty($fields["idcompra"]) == true);
		
		$status = 'A';
		if($fields['recepcionado'] == 'S'){
			$status = 'C';
		}
		
		
		$this->db->trans_start(); // inciamos transaccion
		
		if($esNuevaCompra) {
			$idcompra = $this->compra->insert($fields);
		}
		else {
			$idcompra = $fields["idcompra"];
			
			// obtenemos los datos anteriores
			$temp = $this->compra->find($idcompra);
			
			// verificamos si la compra ha sido al credito, quizas necesitemos hacer alguna validacion
			if($temp["idtipoventa"] == 2) {
				$this->cronograma_pago->find(array("idcompra"=>$idcompra));
				// compra al credito, verificamos si hay algun pago
				if($this->cronograma_pago->get("cancelado")=='N'){
					$query = $this->db->where("estado", "A")->where("idcompra", $idcompra)->get("compra.pago_compra");
					if($query->num_rows() > 0) {
						$this->exception("Ya se han realizado pagos de la compra. No se puede modificar.");
						return false;
					}					
				}else{
					$this->exception("No se puede editar la compra por que hay letras cancelados del credito");
					return false;
				}
			}
			
			// actualizamos los datos de la compra
			$this->compra->update($fields);
			
			// eliminamos el ingreso en detalle_almacen
			$this->db->where("tabla", "C")->where("idtabla", $idcompra)
				->update("almacen.detalle_almacen", array("estado"=>"I"));
				
			// eliminamos el ingreso de las series en almacen
			$this->db->where("tabla_ingreso", "C")->where("idtabla_ingreso", $idcompra)
				->update("almacen.detalle_almacen_serie", array("estado"=>"I"));
				
			// eliminamos el detalle de la compra
			$this->db->where("idcompra", $idcompra)
				->update("compra.detalle_compra", array("estado"=>"I"));
				
			// eliminamos las series de la compra
			$this->db->where("idcompra", $idcompra)
				->update("compra.detalle_compra_serie", array("estado"=>"I"));
				
			// eliminamos la recepcion
			$this->db->where("idcompra", $idcompra)->where("referencia", "C")
				->update("almacen.recepcion", array("estado"=>"I"));
				
			// eliminamos el credito de la compra
			// $this->db->where("idcompra", $idcompra)
				// ->update("compra.cronograma_pago", array("estado"=>"I"));
				
			$this->db->where("idcompra", $idcompra)
				->delete("compra.cronograma_pago");
		}
		
		// cargamos los modelos
		$this->load_model("detalle_compra");
		$this->load_model("detalle_compra_serie");
		$this->load_model("compra.producto_precio_unitario");
		$this->load_model("compra.producto_precio_compra");
		$this->load_model("compra.producto_unidad");
		
		// llenamos datos por default para los modelos
		$this->detalle_compra->set("idcompra", $idcompra);
		$this->detalle_compra->set("estado", "A");
		$this->detalle_compra->set("recepcionado", $fields["recepcionado"]);
		$this->detalle_compra->set("idalmacen", $fields["idalmacen"]);
		
		$this->producto_precio_compra->set("idsucursal", $this->compra->get("idsucursal"));
		$this->producto_precio_compra->set("idmoneda", $this->compra->get("idmoneda"));
		
		if($fields['recepcionado'] == "S") {
			// modelos para el almacen
			$this->load_model("detalle_almacen");
			$this->load_model("detalle_almacen_serie");
			$this->load_model("recepcion"); // siempre se registra esta vaina
			$this->load_model("tipo_movi_almacen");
			
			$this->detalle_almacen->set("tipo", "E");
			$this->detalle_almacen->set("tipo_number", 1);
			$this->detalle_almacen->set("fecha", date("Y-m-d"));
			$this->detalle_almacen->set("tabla", "C");
			$this->detalle_almacen->set("idtabla", $idcompra);
			$this->detalle_almacen->set("estado", "A");
			$this->detalle_almacen->set("idsucursal", $this->compra->get("idsucursal"));
			
			$this->detalle_almacen_serie->set("fecha_ingreso", date("Y-m-d"));
			$this->detalle_almacen_serie->set("tabla_ingreso", "C");
			$this->detalle_almacen_serie->set("idtabla_ingreso", $idcompra);
			$this->detalle_almacen_serie->set("despachado", "N");
			$this->detalle_almacen_serie->set("estado", "A");
			$this->detalle_almacen_serie->set("idsucursal", $this->compra->get("idsucursal"));
			
			$this->recepcion->set("tipo_docu", $this->compra->get("idtipodocumento"));
			$this->recepcion->set("serie", $this->compra->get("serie"));
			$this->recepcion->set("numero", $this->compra->get("numero"));
			$this->recepcion->set("observacion", "RECEPCION AUTOMATICA DE OC NRO. ".$idcompra);
			$this->recepcion->set("fecha", date("Y-m-d"));
			$this->recepcion->set("hora", date("H:i:s"));
			$this->recepcion->set("idusuario", $this->compra->get("idusuario"));
			$this->recepcion->set("referencia", "C");
			
			$this->tipo_movi_almacen->find($this->get_idtipo_movimiento("compra"));
			$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
		}
		
		$arrPedido = array();
		$arrProductosKardex = array(); // datos almacen kardex
		$tipocambio = ($fields["idmoneda"] == 1) ? 1 : floatval($fields["cambio_moneda"]);
		
		// recorremos lista de producto
		foreach($fields["deta_idproducto"] as $key=>$val) {
			if(empty($fields["deta_gastos"][$key])) {
				$fields["deta_gastos"][$key] = 0;
			}
			if(empty($fields["deta_descuento"][$key])) {
				$fields["deta_descuento"][$key] = 0;
			}
			// insertamos el detalle compra
			$this->detalle_compra->set("idproducto", $val);
			$this->detalle_compra->set("idunidad", $fields["deta_idunidad"][$key]);
			$this->detalle_compra->set("cantidad", floatval($fields["deta_cantidad"][$key])); // has modificado aqui?, verifica el ingreso de series
			$this->detalle_compra->set("precio", floatval($fields["deta_precio"][$key]));
			$this->detalle_compra->set("igv", floatval($fields["deta_igv"][$key]));
			$this->detalle_compra->set("flete", floatval($fields["deta_flete"][$key]));
			$this->detalle_compra->set("gastos", floatval($fields["deta_gastos"][$key]));
			$this->detalle_compra->set("descuento", floatval($fields["deta_descuento"][$key]));
			$this->detalle_compra->set("costo", floatval($fields["deta_costo"][$key]));
			$this->detalle_compra->set("afecta_stock", $fields["deta_controla_stock"][$key]);
			$this->detalle_compra->set("afecta_serie", $fields["deta_controla_serie"][$key]);
			$this->detalle_compra->insert();
			
			// insertamos las series del detalle compra
			if($fields["deta_controla_serie"][$key] == "S") {
				if( ! empty($fields["deta_series"][$key])) {
					$this->detalle_compra_serie->set($this->detalle_compra->get_fields());
					$arr = explode("|", $fields["deta_series"][$key]);
					foreach($arr as $serie) {
						$this->detalle_compra_serie->set("serie", $serie);
						$this->detalle_compra_serie->insert(null, false);
					}
				}
			}
			
			$this->producto_unidad->find(array("idproducto"=>$val, "idunidad"=>$fields["deta_idunidad"][$key]));
			$cantidad_unidad = floatval($this->producto_unidad->get("cantidad_unidad_min"));
			
			// ingresamos el precio unitario de compra del producto
			$datos["idproducto"] = $val;
			$datos["idsucursal"] = $fields["idsucursal"];
			$datos["precio_compra"] = floatval($fields["deta_costo"][$key]) * $tipocambio / $cantidad_unidad;
			$this->producto_precio_unitario->save($datos, false);
			
			// si recepcionamos la compra directamente, ingresamos el stock y las series al almacen
			if($fields['recepcionado'] == "S") {
				// ingresamos a recepcion
				$this->recepcion->set($this->detalle_compra->get_fields());
				$this->recepcion->set("cant_recepcionada", $this->detalle_compra->get("cantidad"));
				$this->recepcion->set("correlativo", $correlativo);
				// $this->recepcion->set("estado", $status);
				$this->recepcion->set("estado", "C");
				$this->recepcion->insert();
				$correlativo = $correlativo + 1; // nuevo correlativo
				
				if($fields["deta_controla_stock"][$key] == "S") {
					// ingresamos el stock en el almacen
					$this->detalle_almacen->set($this->detalle_compra->get_fields());
					$this->detalle_almacen->set("precio_costo", $this->detalle_compra->get("costo"));
					$this->detalle_almacen->set("idrecepcion", $this->recepcion->get("idrecepcion"));
					$this->detalle_almacen->insert();
					
					// verificamos para ingresar las series al almacen
					if($fields["deta_controla_serie"][$key] == "S") {
						if(empty($fields["deta_series"][$key])) {
							$this->exception("Ingrese las series del producto ".$fields["deta_producto"][$key]);
							return false;
						}
						
						$count_real_serie = $cantidad_unidad * floatval($fields["deta_cantidad"][$key]);
						
						$arr = explode("|", $fields["deta_series"][$key]);
						if(count($arr) != $count_real_serie) {
							$this->exception("Debe ingresar $count_real_serie series para el producto: ".$fields["deta_producto"][$key]);
							return false;
						}
						
						// ingresamos las series
						$this->detalle_almacen_serie->set($this->detalle_almacen->get_fields());
						foreach($arr as $serie) {
							if( $this->detalle_almacen_serie->exists(array("serie"=>$serie, "despachado"=>"N", "estado"=>"A")) ) {
								$this->exception("La serie $serie del producto ".$fields["deta_producto"][$key]." ya existe.");
								return false;
							}
							$this->detalle_almacen_serie->set("serie", $serie);
							$this->detalle_almacen_serie->insert(null, false);
						}
					}
					
					$temp = $this->recepcion->get_fields();
					$temp["cantidad"] = $temp["cant_recepcionada"];
					$temp["preciocosto"] = $this->detalle_compra->get("costo") / $cantidad_unidad;
					$arrProductosKardex[] = $temp;
				}
			}
			
			// actualizamos el precio de costo del producto
			$this->producto_precio_compra->set($this->detalle_compra->get_fields());
			$this->producto_precio_compra->set("precio", $this->detalle_compra->get("costo"));
			$this->producto_precio_compra->save(null, false);
			
			// actualizamos el estado del pedido si se ha indicado
			if( ! empty($fields["deta_idpedido"][$key])) {
				$this->db->where("idpedido", $fields["deta_idpedido"][$key])
					->where("idproducto", $val)
					->update("compra.detalle_pedido", array("atendido"=>"S"));
				if( ! in_array($fields["deta_idpedido"][$key], $arrPedido)) {
					$arrPedido[] = $fields["deta_idpedido"][$key];
				}
			}
		} // fin [foreach]
		
		
		if( ! $esNuevaCompra) { // si estamos editando la compra
			// eliminamos el ingreso de kardex
			$this->load_library("jkardex");
			$this->jkardex->remove("compra", $idcompra, $fields["idsucursal"]);
			
			// eliminamos el pago ingresado
			$this->load->library('pay');
			$this->pay->remove("compra", $idcompra, $fields["idsucursal"]);
		}
		
		if($fields['recepcionado'] == "S") {
			if( ! empty($arrProductosKardex)) {
				// actualizamos el correlativo del tipo movimiento
				$this->tipo_movi_almacen->set("correlativo", $correlativo);
				$this->tipo_movi_almacen->update();
				
				// registramos el movimiento de kardex, obtenemos data priquis
				// $sql = "SELECT idproducto, cant_recepcionada as cantidad, idunidad, idalmacen, correlativo
					// FROM almacen.recepcion WHERE idcompra = ?";
				// $query = $this->db->query($sql, array($idcompra));
				
				if( ! isset($this->jkardex)) {
					// importamos librari
					$this->load_library("jkardex");
				}
				
				$this->jkardex->idtercero = $this->compra->get("idproveedor");
				$this->jkardex->idmoneda = $this->compra->get("idmoneda");
				$this->jkardex->tipocambio = $this->compra->get("cambio_moneda");
				
				$this->jkardex->referencia("compra", $idcompra, $fields["idsucursal"]);
				$this->jkardex->entrada();
				// $this->jkardex->calcular_precio_costo();
				// $this->jkardex->push($query->result_array());
				$this->jkardex->push($arrProductosKardex);
				$this->jkardex->run();
			}
		}
		
		if( $fields["afecta_caja"] == 'S' && $fields["idtipoventa"] == 1 ) {
			// datos necesarios para la libreria pay, revisar la clase para mas info sobre las
			// variables necesarias para la clase
			$fields["descripcion"] = "COMPRA AL CONTADO";
			$fields["referencia"] = $fields['proveedor'];
			$fields["tabla"] = "compra";
			$fields["idoperacion"] = $idcompra;
			
			if( ! isset($this->caja_controller)) {
				$this->load_controller("caja");
			}
			if( ! isset($this->pay)) {
				$this->load->library('pay');
			}
			$this->pay->set_controller($this->caja_controller);
			$this->pay->set_data($fields);
			$this->pay->entrada(false); // false si es salida, default true
			$this->pay->process();
		}
		else if($fields["idtipoventa"] == 2 && ! empty($fields["idforma_pago_compra"])) {
			// creamos el credito para la compra
			$this->load_model("compra.forma_pago_compra");
			$this->load_model("compra.cronograma_pago");
			
			$this->cronograma_pago->set("idcompra", $idcompra);
			$this->cronograma_pago->set("idmoneda", $this->compra->get("idmoneda"));
			$this->cronograma_pago->set("cancelado", "N");
			$this->cronograma_pago->set("estado", "A");
			
			$this->forma_pago_compra->find($fields["idforma_pago_compra"]);
			
			$nro_letras = intval($fields["nro_letras"]);
			$monto = floatval($fields["total"]) / $nro_letras;
			$dias = $this->forma_pago_compra->get("nrodias");
			$fecha = new DateTime($this->compra->get("fecha_compra"));
			$i = 0;
			do {
				$i ++;
				$fecha->add(new DateInterval("P".$dias."D"));
				
				$this->cronograma_pago->set("letra", $i);
				$this->cronograma_pago->set("monto_letra", $monto);
				$this->cronograma_pago->set("fecha_vencimiento", $fecha->format("Y-m-d"));
				$this->cronograma_pago->set("saldo", $monto);
				$this->cronograma_pago->insert(null, false);
			}
			while($nro_letras > $i);
		}
		
		// cambiamos de estado a los pedidoss
		if(! empty($arrPedido)) {
			$this->load_model("pedido");
			
			foreach($arrPedido as $idpedido) {
				$sql = "SELECT * FROM compra.detalle_pedido
					WHERE idpedido=? AND estado=? AND atendido=?";
				$query = $this->db->query($sql, array($idpedido, "A", "N"));
				
				if($query->num_rows() <= 0) {
					$this->pedido->update(array("idpedido"=>$idpedido, "atendido"=>"S"));
				}
			}
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		if ($this->db->trans_status() === FALSE) {
			// generate an error... or use the log_message() function to log your error
		}
		
		$this->response($this->compra->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($idcompra) {
		$this->load_model($this->controller);
		
		$this->compra->find($idcompra);
		
		$this->db->trans_start();
		
		$this->compra->update(array("idcompra"=>$idcompra, "estado"=>"I"));
		
		// eliminamos la recepcion
		$this->db->where("idcompra", $idcompra)
			->update("almacen.recepcion", array("estado"=>"I"));
		
		// eliminamos el ingreso en detalle_almacen
		$this->db->where("tabla", "C")->where("idtabla", $idcompra)
			->update("almacen.detalle_almacen", array("estado"=>"I"));
			
		// eliminamos el ingreso de las series en almacen
		$this->db->where("tabla_ingreso", "C")->where("idtabla_ingreso", $idcompra)
			->update("almacen.detalle_almacen_serie", array("estado"=>"I"));
			
		// eliminamos el detalle de la compra
		$this->db->where("idcompra", $idcompra)
			->update("compra.detalle_compra", array("estado"=>"I"));
			
		// eliminamos las series de la compra
		$this->db->where("idcompra", $idcompra)
			->update("compra.detalle_compra_serie", array("estado"=>"I"));
		
		if($this->compra->get("idtipoventa") == 1) { // contado
			// eliminamos el pago ingresado
			$this->load_library('pay');
			$this->pay->remove("compra", $idcompra, $this->compra->get("idsucursal"));
		}
		else {
			// eliminamos el credito de la compra
			$this->db->where("idcompra", $idcompra)
				->update("compra.pago_compra", array("estado"=>"I"));
				
			$this->db->where("idcompra", $idcompra)
				->update("compra.cronograma_pago", array("estado"=>"I"));
		}
		
		// eliminamos el ingreso de kardex
		$this->load_library("jkardex");
		$this->jkardex->remove("compra", $idcompra, $this->compra->get("idsucursal"));
		
		$this->db->trans_complete();
		
		$this->response($this->compra->get_fields());
	}
	
	
	/* public function autocomplete() {
		$txt = $this->input->post("startsWith").'%';
		
		$sql = "SELECT c.idcompra, p.nombre, p.ruc 
			FROM compra.compra AS c, compra.proveedor as p 
			WHERE c.idproveedor = p.idproveedor and c.estado='A'
			GROUP BY c.idcompra,p.nombre, p.ruc 
			ORDER BY c.idcompra";
			
		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function select_detalle() {
		$data = array();
		$txt = $this->input->post("startsWith").'%';
		$idcompra = $this->input->post("idcompra");
		
		// $sql = "SELECT p.idproducto, p.descripcion, u.abreviatura, d.cantidad, coalesce(sum(r.cant_recepcionada),0) AS cant_recepcionada
		$sql = "SELECT p.idproducto, p.descripcion, u.abreviatura, d.cantidad
				FROM compra.producto as p
				INNER JOIN compra.unidad as u ON p.idunidad=u.idunidad				
				INNER JOIN compra.detalle_compra as d ON p.idproducto=d.idproducto
				INNER JOIN compra.compra as c ON d.idcompra=c.idcompra
				WHERE d.idcompra=$idcompra";
				// LEFT JOIN almacen.recepcion r ON (c.idcompra=r.idcompra AND r.idproducto=p.idproducto)				
				
		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$data['lsProdctos_compras'] = $query->result_array();
		$this->response(json_encode($data));
	} */
	
	public function grilla_popup() {
		$this->load_model("compra.compra_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->compra_view);
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->setColumns(array('fecha_compra','tipo_documento','nrodocumento','proveedor','moneda','total'));
		$this->datatables->setPopup(true);
		
		$table = $this->datatables->createTable(array('Fecha','Tipo Doc.','Nro.Doc.','Proveedor','Moneda','Total'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_detalle($idcompra) {
		$sql = "select dv.iddetalle_compra, p.descripcion_detallada as producto, u.descripcion as unidad,
			dv.cantidad, dv.afecta_stock as controla_stock, dv.afecta_serie as controla_serie, 
			dv.idalmacen, dv.idproducto, dv.precio, dv.idunidad, 
			array_to_string(array_agg(dvs.serie), '|'::text) as serie
			from compra.detalle_compra dv
			join compra.producto p on p.idproducto = dv.idproducto
			join compra.unidad u on u.idunidad = dv.idunidad
			left join compra.detalle_compra_serie dvs on dvs.iddetalle_compra=dv.iddetalle_compra 
				and dvs.idcompra=dv.idcompra and dvs.idproducto=dv.idproducto and dvs.estado='A'
			where dv.estado = 'A' and dv.idcompra = ?
			group by dv.iddetalle_compra, p.descripcion_detallada, u.descripcion, dv.cantidad, dv.afecta_stock, 
				dv.afecta_serie, dv.idalmacen, dv.idproducto, dv.idcompra, dv.precio, dv.idunidad
			order by iddetalle_compra";
		$query = $this->db->query($sql, array($idcompra));
		$this->response($query->result_array());
	}
}
?>