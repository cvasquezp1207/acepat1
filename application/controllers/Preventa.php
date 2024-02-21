<?php

include_once "Controller.php";

class Preventa extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Lista de pedidos de venta");
		// $this->set_subtitle("Lista de pedidos de venta");
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
		
		///////////////////////////////////////////////////// combo tipo venta
		$query = $this->db->select('idtipoventa, descripcion')
			->where("estado", "A")->where("mostrar_en_venta", "S")
			->order_by("descripcion", "asc")->get("venta.tipo_venta");
		
		$this->combobox->setAttr("id", "idtipoventa");
		$this->combobox->setAttr("name", "idtipoventa");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["preventa"]["idtipoventa"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idtipoventa"]);
		}
		$data["tipoventa"] = $this->combobox->getObject();
		////////////////////////////////////////////////////// combo Modalidad de Venta


		//////////////////////////////////////////////////////////
		$query = $this->db->select('idmodalidad, modalidad')
			->where("estado", "A")
			->order_by("modalidad", "asc")->get("venta.modalidad");
		$this->combobox->init();
		$this->combobox->setAttr("id", "idmodalidad");
		$this->combobox->setAttr("name", "idmodalidad");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["preventa"]["idmodalidad"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idmodalidad"]);
		}
		$data["modalidad"] = $this->combobox->getObject();


		////////////////////////////////////////////////////// combo Rampa
		$query = $this->db->where("estado", "A")
			->order_by("descripcion", "asc")->get("venta.rampa");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idrampa","name"=>"idrampa","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("0"," ");
		$this->combobox->addItem($query->result_array(), array("idrampa","descripcion"));
		
		if( isset($data["preventa"]["idrampa"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idrampa"]);
		}
		$data["rampa"] = $this->combobox->getObject();


		////////////////////////////////////////////////////// combo Mecanico
		$query = $this->db->where("estado", "A")->where("baja","N")->order_by("nombre", "asc")->get("seguridad.mecanico_vista");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idmecanico","name"=>"idmecanico","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("0"," ");
		$this->combobox->addItem($query->result_array(), array("idmecanico","nombre"));
		
		if( isset($data["preventa"]["idmecanico"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idmecanico"]);
		}
		$data["mecanico_vista"] = $this->combobox->getObject();


		
		////////////////////////////////////////////////////// combo tipodocumento
		$query = $this->db->where("estado", "A")->where("mostrar_en_venta", "S")
			->order_by("descripcion", "asc")->get("venta.tipo_documento");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idtipodocumento","name"=>"idtipodocumento","class"=>"form-control input-xs","required"=>""));
		
		$this->combobox->addItem($query->result_array(), array("idtipodocumento","descripcion","codsunat","facturacion_electronica","ruc_obligatorio","dni_obligatorio"));
		
		if( isset($data["preventa"]["idtipodocumento"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idtipodocumento"]);
		}
		$data["tipodocumento"] = $this->combobox->getObject();
		
		//////////////////////////////////////////////////////////// combo sede
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"serie","name"=>"serie","class"=>"form-control input-xs","required"=>""));
		if( isset($data["preventa"]["idtipodocumento"]) ) {
			$sql = "SELECT serie, serie 
				FROM venta.serie_documento 
				WHERE idtipodocumento=? and idsucursal=?";
			$query = $this->db->query($sql, array($data["preventa"]["idtipodocumento"], $this->get_var_session("idsucursal")));
			$this->combobox->addItem($query->result_array());
		}
		if( isset($data["preventa"]["serie"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["serie"]);
		}
		$data["comboserie"] = $this->combobox->getObject();
		
		//////////////////////////////////////////////////////// combo almacen
		$query = $this->db->select('idalmacen, descripcion')->where("estado", "A")
			->where("idsucursal", $this->get_var_session("idsucursal"))
			->order_by("idalmacen", "desc")->get("almacen.almacen");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idalmacen","name"=>"idalmacen","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($query->result_array());
		if( isset($data["preventa"]["idalmacen"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idalmacen"]);
		}
		$data["almacen"] = $this->combobox->getObject();
		
		//////////////////////////////////////////////////////// combo moneda
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")
			->order_by("idmoneda", "asc")->get("general.moneda");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idmoneda","name"=>"idmoneda","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($query->result_array());
		if( isset($data["preventa"]["idmoneda"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idmoneda"]);
		}
		$data["moneda"] = $this->combobox->getObject();
		
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
		
		
		//$data["default_igv"] = $this->get_param("default_igvdos");
		
		///////////////////////////////////////////////////////// combo vendedor
		// $idperfil = 4; // id del perfil vendedor, tal vez deberia ser contante
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser constante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idvendedor","name"=>"idvendedor","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($datos);
		if( isset($data["preventa"]["idvendedor"]) ) {
			$this->combobox->setSelectedOption($data["preventa"]["idvendedor"]);
			if( ! $this->combobox->item_exists($data["preventa"]["idvendedor"])) {
				$this->combobox->addItem($this->usuario->get_empleado($data["preventa"]["idvendedor"]));
			}
		}
		else {
			$this->combobox->setSelectedOption($this->get_var_session("idusuario"));
		}
		$data["vendedor"] = $this->combobox->getObject();

		$data["controller"] = $this->controller;
		
		$igv = $this->get_param("igv");
		if(!is_numeric($igv)) {
			$igv = 18;
		}
		$data["valor_igv"] = $igv;
		$data["validar_ruc"] = $this->get_param("validar_ruc");
		$data["mostrar_precio_costo"] = $this->get_param("mostrar_precio_costo", "N");
		
		$nueva_venta = "true";
		if( isset($data["preventa"]["idpreventa"]) ) {
			$nueva_venta = "false";
		}
		$this->js("<script>var _es_nuevo_ = $nueva_venta;</script>", false);
		
		if( isset($data["detalle"]) ) {
			$this->js("<script>var data_detalle = ".json_encode($data["detalle"]).";</script>", false);
		}
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
		$this->css("plugins/datapicker/datepicker3");
		$this->css('plugins/iCheck/custom');
		
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('plugins/iCheck/icheck.min');
		$this->js('form/'.$this->controller.'/form');
		
		
		$this->load_controller("cliente");

		$data["form_cliente"] = $this->cliente_controller->form(null, "cli_", true);

		$this->js('form/cliente/modal');
		
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		$data["fixed"] = $fc;
		$this->js("<script>var _fixed_venta = $fc;</script>", false);
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function filtros_grilla($pendiente) {
		$this->load_library("combobox");
		
		$html = '<div class="row">';
		
		// div y combobox recepcionado
		$this->combobox->setAttr("filter", "pendiente");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->addItem("S", "PENDIENTE");
		$this->combobox->addItem("N", "ATENDIDO");
		
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">Atendido</label>';
		$html .= $this->combobox->getObject();
		$html .= '</div></div>';
		
		$html .= '</div>';
		
		$this->set_filter($html);
	}

	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("venta.preventa_view");
		
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->preventa_view);
		$this->datatables->setIndexColumn("idpreventa");
		
		$pendiente = 'S';
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('pendiente', '=', $pendiente);
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->setColumns(array('idpreventa','fecha','cliente','tipoventa','rampa',
			'moneda_abreviatura','total','tipodocumento'));
			// 'moneda_abreviatura','total','tipodocumento','serie'));
		
		$columnasName = array(
			array('Id','5%')
			,array('Fecha','12%')
			,array('Cliente','28%')
			,array('Tipo','12%')
			,array('Rampa','12%')
			,array('Moneda','8%')
			,array('Total','8%')
			// ,array('Vendedor','15')
			,array('CdP','18%')
			// ,array('Serie','8%')
		);
		
		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		$this->filtros_grilla($pendiente);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Preventa");
		$this->set_subtitle("");
		
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id, $tabkey="") {
		$this->load_model("venta.preventa_view");
		$this->preventa_view->set_column_pk("idpreventa");
		$data["tabkey"] = $tabkey;
		$data["preventa"] = $this->preventa_view->find($id);
		
		$this->load_model("detalle_preventa");
		$data["detalle"] = $this->detalle_preventa->get_items($id, $data["preventa"]["idsucursal"]);
		
		// echo "<pre>";var_dump($data);
		$this->set_title("Modificar Preventa");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */	
	public function guardar() {
		$this->load_model("venta.preventa");
		$this->load_model("venta.detalle_preventa");
		
		$fields = $this->input->post();
		$fields['idsucursal'] = $this->get_var_session("idsucursal");
		$fields['idusuario'] = $this->get_var_session("idusuario");
		$fields['fecha'] = date("Y-m-d H:i:s");
		$fields['estado'] = "A";
		$fields["pendiente"] = "S";
		if(empty($fields["descuento"]))
			$fields["descuento"] = 0;
		if(empty($fields["igv"]))
			$fields["igv"] = 0;
		if(empty($fields["idcliente"]))
			$fields["idcliente"] = 0;
		if(empty($fields["idrampa"]))
			$fields["idrampa"] = 0;
		if(empty($fields["idmecanico"]))
			$fields["idmecanico"] = 0;		
        if(empty($fields["idmodalidad"]))
			$fields["idmodalidad"] = 0;
		if(empty($fields["idvendedor"]))
			$fields["idvendedor"] = $this->get_var_session("idusuario");
		// print_r($fields);return;
		
		// verificamos datos necesarios segun el tipo comprobante
		//$valid = $this->is_valid_doc($fields["idtipodocumento"], $fields["serie"], $fields["idcliente"]);
			/*$valid = $this->is_valid_doc($fields["idtipodocumento"], $fields["idcliente"]);*/
		/*if($valid !== true) {
			$this->exception($valid);
			return;
		}*/

		$inicial = '';
		$this->load_model("venta.tipo_documento");
		$this->tipo_documento->find($fields["idtipodocumento"]);
		
		if($this->tipo_documento->get("codsunat") == "01")
			$inicial = 'F';
		else if($this->tipo_documento->get("codsunat") == "03")
			$inicial = 'B';
			
		$valid = $this->is_valid_doc($fields["idtipodocumento"], $inicial.'000', $fields["idcliente"],$fields["total"],$fields["idmoneda"]);

		/*$valid = $this->is_valid_doc($fields["idtipodocumento"], 'B000', $fields["idcliente"],$fields["total"],$fields["idmoneda"]);
		if($valid !== true) {
			$this->exception($valid);
			return;
		}
		comentamos para que qla preventa pueda pasar*/
		
		$this->db->trans_start(); // inciamos transaccion
		
		if(empty($fields["idpreventa"])) {
			$idpreventa = $this->preventa->insert($fields);
		} else {
			$this->preventa->update($fields);
			$idpreventa = $this->preventa->get("idpreventa");
			
			// eliminamos el detalle de la preventa
			$this->detalle_preventa->delete(array("idpreventa"=>$idpreventa));
		}
		
		$this->detalle_preventa->set("idpreventa", $idpreventa);
		$this->detalle_preventa->set("estado", "A");
		
		foreach($fields["deta_idproducto"] as $key=>$val) {
			if(empty($fields["deta_idalmacen"][$key]))
				$fields["deta_idalmacen"][$key] = $fields["idalmacen"];
			
			$this->detalle_preventa->set("iddetalle_preventa", ($key+1));
			$this->detalle_preventa->set("idproducto", $val);
			$this->detalle_preventa->set("idunidad", $fields["deta_idunidad"][$key]);
			$this->detalle_preventa->set("cantidad", $fields["deta_cantidad"][$key]);
			$this->detalle_preventa->set("precio", $fields["deta_precio"][$key]);
			$this->detalle_preventa->set("idalmacen", $fields["deta_idalmacen"][$key]);
			$this->detalle_preventa->set("serie", $fields["deta_series"][$key]);
			$this->detalle_preventa->set("oferta", $fields["deta_oferta"][$key]);
			$this->detalle_preventa->set("codgrupo_igv", $fields["deta_grupo_igv"][$key]);
			$this->detalle_preventa->set("codtipo_igv", $fields["deta_tipo_igv"][$key]);
			$this->detalle_preventa->insert(null, false);
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($this->preventa->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("venta.preventa");
		
		$fields['idpreventa'] = $id;
		$fields['estado'] = "I";
		$this->preventa->update($fields);
		
		$this->response($fields);
	}
	
	public function grilla_popup() {
		$this->load_model("venta.preventa_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->preventa_view);
		$this->datatables->setIndexColumn("idpreventa");
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('pendiente', '=', 'S');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->setPopup(true);
		$this->datatables->setColumns(array('idpreventa'
											,'fecha'
											,'cliente'
											,'td_documento'
											,'moneda_abreviatura'
											,'modalidad'
											,'rampa'
											,'mecanico_nombre'
											,'vendedor_nombre'
											,'total'
									));

		$this->datatables->order_by('fecha', 'desc');
		$this->datatables->setCallback('callbackPreventa');
		$table = $this->datatables->createTable(array(array('Id','10')
													,array('Fecha','10')
													,array('Cliente','55')
													,array('Doc','4')
													,array('Moneda','2')
													,array('Modalidad','10')
													,array('Rampa','2')
													,array('Mecanico','2')
													,array('Vendedor','2')
													,array('Total','10')
												));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_detalle($id) {
		$this->load_model("detalle_preventa");
		$res = $this->detalle_preventa->get_items($id, $this->get_var_session("idsucursal"));
		$this->response($res);
	}
	public function cabecera_detalle(){
		return array('item'=> array('ITEM',10,0,'L')
					,'producto' => array('PRODUCTO',120,0,'L')
					,'unidad' => array('UNIDAD',20,0,'L')
					,'cantidad' => array('CANTIDAD',20,0,'R')
					,'precio' => array('PRECIO',20,1,'R')
				);
	}
	
	public function detalle_body($id=0){
		$q = $this->db->query("SELECT prod.descripcion producto
								,u.descripcion unidad
								,d.cantidad 
								,d.precio
								FROM venta.detalle_preventa d
								JOIN venta.preventa p ON p.idpreventa=d.idpreventa
								JOIN compra.unidad u ON u.idunidad=d.idunidad
								JOIN compra.producto prod ON prod.idproducto=d.idproducto
								WHERE d.idpreventa=$id");
		return $q->result_array();
	}
	public function imprimir(){
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","venta.preventa_view","venta.tipo_venta","seguridad.cliente_view","venta.detalle_preventa","seguridad.view_usuario"));
		$this->empresa->find($this->get_var_session("idempresa"));
		$this->preventa_view->find($_REQUEST['id']);
		$this->cliente_view->find($this->preventa_view->get("idcliente"));
		$fechas = explode(" ",$this->preventa_view->get("fecha"));
		$fecha = '';
		if(!empty($fechas))
			$fecha = fecha_es($fechas[0])." ".date("g:i:s a",strtotime($fechas[1]));
		
		$this->view_usuario->find($this->detalle_preventa->get("idusuario"));
		// $this->tipo_pedido->find($this->pedido->get("idtipo_pedido"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		$this->pdf->SetTitle(utf8_decode("DETALLE DE PREVENTA N° {$_REQUEST['id']}"), 11, null, true);

		$this->pdf->AliasNbPages(); // para el conteo de paginas
		// $this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('TIPO VENTA'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->preventa_view->get("tipoventa")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('FECHA EMISION'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($fecha),0,0,'L');
		$this->pdf->Ln();
		
		// $this->pdf->SetFont('Arial','B',9);
		// $this->pdf->Cell(30,6,utf8_decode('ALMACEN'),0,0,'L');
		// $this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		// $this->pdf->SetFont('Arial','',9);
		// $this->pdf->Cell(130,6,utf8_decode('--'),0,0,'L');
		// $this->pdf->Ln();
		
		// $this->pdf->SetFont('Arial','B',9);
		// $this->pdf->Cell(30,6,utf8_decode('ATENDIDO'),0,0,'L');
		// $this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		// $this->pdf->SetFont('Arial','',9);
		// $this->pdf->Cell(130,6,utf8_decode($atendido),0,0,'L');
		// $this->pdf->Ln();
		
		// $this->pdf->SetFont('Arial','B',9);
		// $this->pdf->Cell(30,6,utf8_decode('APROBADO'),0,0,'L');
		// $this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		// $this->pdf->SetFont('Arial','',9);
		// $this->pdf->Cell(130,6,utf8_decode($aprobado),0,0,'L');
		// $this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('CLIENTE'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->cliente_view->get("cliente")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('VENDEDOR'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->preventa_view->get("vendedor")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('MODALIDAD'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->preventa_view->get("modalidad")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('MONEDA'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->preventa_view->get("moneda")),0,0,'L');
		$this->pdf->Ln();
		
		// $this->pdf->SetFont('Arial','B',9);
		// $this->pdf->Cell(30,6,utf8_decode('DESCRIPCION'),0,0,'L');
		// $this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		// $this->pdf->SetFont('Arial','',9);
		// $this->pdf->Cell(130,6,utf8_decode($this->preventa_view->get("descripcion")),0,0,'L');
		// $this->pdf->Ln(10);
		
		$this->pdf->SetFont('Arial','B',9);
		foreach ($this->cabecera_detalle() as $key => $val) {
			$this->pdf->Cell($val[1],6,$val[0],1,$val[2],$val[3]);
		}
		
		$this->pdf->SetFont('Arial','',8.5);
		$i = 1;
		foreach($this->detalle_body($_REQUEST['id']) as $k=>$v){
			$v["item"] = $i;
			foreach ($this->cabecera_detalle() as $key => $val) {
				$this->pdf->Cell($val[1],4,utf8_decode($v[$key]),1,$val[2],$val[3]);
			}
			$i++;
		}
		$this->pdf->Output();
	}

}
?>