<?php

include_once "Controller.php";

class Deuda extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Creditos");
		$this->set_subtitle("Lista de creditos");
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
		
		$this->load->library('combobox');
		$data["controller"] = $this->controller;
		$data["cabecera"]	= $this->arrary_head();
		// forma pago compra
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idforma_pago_compra"
				,"name"=>"idforma_pago"
				,"class"=>"form-control input-sm"
			)
		);
		$query = $this->db->select('idforma_pago_compra, descripcion,nrodias')->where("estado", "A")->order_by("nrodias", "asc")->get("compra.forma_pago_compra");
		$this->combobox->addItem($query->result_array(), array('idforma_pago_compra', 'descripcion', 'nrodias'));
		if( isset($data["deuda"]["idforma_pago_compra"]) ) {
			$this->combobox->setSelectedOption($data["deuda"]["idforma_pago_compra"]);
		}
		$data["forma_pago_compra"] = $this->combobox->getObject();
		
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
		$query = $this->db->select('idmoneda, descripcion, abreviatura')->where("estado", "A")->order_by("idmoneda", "asc")->get("general.moneda");
		$this->combobox->addItem($query->result_array(), array('idmoneda', 'descripcion', 'abreviatura'));
		if( isset($data["deuda"]["idmoneda"]) ) {
			$this->combobox->setSelectedOption($data["deuda"]["idmoneda"]);
		}
		$data["moneda"] = $this->combobox->getObject();
		
		// combo moneda
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"id_moneda_deuda"
				,"name"=>"id_moneda_deuda"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idmoneda, descripcion, abreviatura')->where("estado", "A")->order_by("idmoneda", "asc")->get("general.moneda");
		$this->combobox->addItem($query->result_array(), array('idmoneda', 'descripcion', 'abreviatura'));
		if( isset($data["deuda"]["id_moneda_deuda"]) ) {
			$this->combobox->setSelectedOption($data["deuda"]["id_moneda_deuda"]);
		}
		$data["moneda_deuda"] = $this->combobox->getObject();
		
		$nuevo = "true";
		if( isset($data["deuda"]["iddeuda"]) ) {
			$nuevo = "false";
		}
		$this->js("<script>var _es_nuevo_credito_ = $nuevo;</script>", false);
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('form/'.$this->controller.'/form');

		return $this->load->view($this->controller."/form1", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("compra.deuda_view");
		$this->load->library('datatables');
		
		$columnasName = array(
			'iddeuda'=>'Id'
			,'fecha_deuda'=>'Fecha'
			,'comprobante'=>'Comprobante'
			,'nro_credito'=>'Credito'
			,'proveedor'=>'Proveedor'
			,'cant_letras'=>'Letras'
			,'monto'=>'Importe'
			,'monto_cancelado'=>'Pagado'
			,'monto_deuda'=>'Saldo'
			// ,'pagado'=>'Estado'
		);
		
		$this->datatables->setModel($this->deuda_view);
		$this->datatables->setIndexColumn("iddeuda");
		
		$this->datatables->setColumns(array_keys($columnasName));
		
		$this->datatables->where('estado', '=', 'A');
		// $this->datatables->where('pagado', '=', 'N');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->order_by('fecha_deuda', 'desc');
		$this->datatables->setCallback("formatoGrilla");
		
		$table = $this->datatables->createTable(array_values($columnasName));
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->js($script, false);
		$this->filtros_grilla();
		return $table;
	}
	
	public function filtros_grilla() {
		$this->load_library("combobox");
		
		$html = '<div class="row">';
		
		// div y combobox recepcionado
		$this->combobox->setAttr("filter", "pagado");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->addItem("", "TODOS");
		$this->combobox->addItem("N", "NO");
		$this->combobox->addItem("S", "SI");
		
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">PAGADO</label>';
		$html .= $this->combobox->getObject();
		$html .= '</div></div>';
		
		$html .= '</div>';
		
		$this->set_filter($html);
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo($idventa = 0) {
		$this->set_title("Registrar Deuda");
		$this->set_subtitle("");
		
		$data = array();
		if(!empty($idventa)) {
			$this->load_model("venta.venta_view");
			$this->venta_view->set_column_pk("idventa");
			$data["venta"] = $this->venta_view->find($idventa);
			$data["credito"]["idventa"] = $this->venta_view->get("idventa");
			$data["credito"]["idcliente"] = $this->venta_view->get("idcliente");
			$data["credito"]["idmoneda"] = $this->venta_view->get("idmoneda");
			$data["credito"]["monto_facturado"] = $this->venta_view->get("total");
			$data["credito"]["inicial"] = "0.00";
			$data["credito"]["capital"] = $this->venta_view->get("total");
			$data["credito_view"]["cliente"] = $this->venta_view->get("nombres");
			$data["redirect"] = "venta"; // redireccionar a venta
		}
		
		$data["has_amortizacion"] = false;
		
		$this->set_content($this->form($data));
		$this->index("content");
		
		// $this->set_content($this->form($data));
		// $this->index("content_empty");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model(array("compra.deuda_view"));
		$data["deuda"] = $this->deuda_view->find($id);
		$data["has_amortizacion"] = $this->deuda_view->get("has_amortizado");
		
		$data["letras"] = $this->detalle($id);
		$this->set_title("Modificar Credito");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
		// $this->index("content_empty");
	}
	
	public function verificar_compra($idcompra=0,$iddeuda=0){
		if(!isset($iddeuda))
			$iddeuda=0;
		if(empty($iddeuda))
			$iddeuda = 0;
		$q = $this->db->query("SELECT count(*) has_exist FROM compra.compra_deuda WHERE iddeuda<>$iddeuda AND idcompra='{$idcompra}'");
		return $q->row()->has_exist;
	}
	
	public function get_compra_credito() {
		$post = $this->input->post();
		$post["idsucursal"] = $this->get_var_session("idsucursal");
		
		if($post["es_nuevo"]=='true')
			$sql="SELECT
				idcompra
				,comprobante
				,total monto
				,'N'::text usado
				,idsucursal
				FROM compra.compra_view 
				WHERE idtipoventa=2 
				AND idproveedor='{$post['idproveedor']}' 
				AND estado='A'
				AND idmoneda='{$post['idmoneda']}'
				AND idsucursal='{$post['idsucursal']}' 
				AND idcompra NOT IN(SELECT d.idcompra 
									FROM compra.deuda 
									JOIN compra.compra_deuda d ON d.iddeuda=deuda.iddeuda 
									WHERE deuda.idproveedor='{$post['idproveedor']}'  
									AND deuda.estado='A'
									AND idsucursal='{$post['idsucursal']}' 
									)
				ORDER BY comprobante;";
		else
			$sql=" SELECT
					compra_view.idcompra
					,comprobante
					,total monto
					,'S'::text usado
					,idsucursal
					FROM compra.compra_view 
					WHERE idtipoventa=2 
					AND idproveedor='{$post['idproveedor']}' 
					AND estado='A'
					AND idmoneda='{$post['idmoneda']}'
					AND idsucursal='{$post['idsucursal']}' 
					AND idcompra IN(SELECT d.idcompra 
									FROM compra.compra_deuda d 
									WHERE d.iddeuda='{$post['iddeuda']}'  
									AND idsucursal='{$post['idsucursal']}' 
								) 
					UNION
					SELECT
					compra_view.idcompra
					,comprobante
					,total monto
					,'N'::text usado
					,idsucursal
					FROM compra.compra_view 
					WHERE idtipoventa=2 
					AND idproveedor='{$post['idproveedor']}' 
					AND estado='A'
					AND idmoneda='{$post['idmoneda']}'
					AND idsucursal='{$post['idsucursal']}' 
					AND idcompra NOT IN(SELECT d.idcompra 
										FROM compra.deuda 
										JOIN compra.compra_deuda d ON d.iddeuda=deuda.iddeuda 
										WHERE deuda.idproveedor='{$post['idproveedor']}'   
										AND deuda.estado='A'
										AND idsucursal='{$post['idsucursal']}' 
									)
					ORDER BY comprobante";
		// echo $sql;exit;
		$q = $this->db->query($sql);
		$this->response($q->result_array());
	}
	
	public function proveedor_proveedor(){
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idproveedor, nombre, ruc
			FROM compra.proveedor
			WHERE estado='A' and (nombre ILIKE ? OR ruc ILIKE ?)
			AND idproveedor IN (SELECT idproveedor 
								FROM compra.compra WHERE idtipoventa=2 AND estado='A' AND idsucursal={$this->get_var_session('idsucursal')}
								AND idcompra NOT IN (SELECT idcompra FROM compra.compra_deuda )
							)
			ORDER BY nombre
			LIMIT ?";
			// echo $sql;
		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model(array("compra.deuda","compra.deuda_view","compra.letra","compra.compra_deuda","compra.compra_deuda_view"));
		$post = $this->input->post();
		
		$datosdeuda['iddeuda']			= $post["iddeuda"];
		$datosdeuda['idproveedor']		= $post["idproveedor"];
		$datosdeuda['idmoneda']			= $post["idmoneda"];
		$datosdeuda['idsucursal']		= $this->get_var_session("idsucursal");
		$datosdeuda['idusuario']		= $this->get_var_session("idusuario");
		$datosdeuda['cant_letras']		= $post["cant_letras"];
		$datosdeuda['monto']			= $post["monto"];
		$datosdeuda['gastos']			= $post["gastos"];
		$datosdeuda['descuento']		= $post["descuento"];
		$datosdeuda['cambio_moneda']	= $post["cambio_moneda"];
		$datosdeuda['fecha_deuda']		= $post["fecha_deuda"];
		$datosdeuda['fecha_registro']	= date("Y-m-d");
		$datosdeuda['hora_registro']	= date("H:i:s");
		$datosdeuda['estado']			= 'A';
		
		$crearLetras = true;
		
		
		if(!isset($post['id_compras'])){
			$this->exception("Debe seleccionar una compra al credito para guardar la operacion");
			return;
		}
		
		$sms = "";
		$pre_nro_credito = "";
		foreach($post['id_compras'] as $v){
			$datosdeuda_compra = $this->verificar_compra($v,$post['iddeuda']);
			if($datosdeuda_compra>0){
				$this->compra_deuda_view->find(array("idcompra"=>$v));
				$sms.="El comprobante <span style='color:#ed5565;'>".$this->compra_deuda_view->get("comprobante")."</span> ya fue usado en otro credito<br>";
			}
			$pre_nro_credito.=$v;
		}
			
		if(!empty($sms)){
			$this->exception($sms);
			return;
		}
		
		if(empty($datosdeuda["iddeuda"])) {
			$datosdeuda['pagado']	= "N";

			$nro_credito = str_pad($pre_nro_credito, 10, "0", STR_PAD_LEFT);
		
			$datosdeuda['nro_credito'] = $nro_credito;
			$iddeuda = $this->deuda->insert($datosdeuda);
		}else{
			$this->deuda->update($datosdeuda);
			$iddeuda = $datosdeuda["iddeuda"];
			
			$this->deuda_view->find($iddeuda);
			$has_amort = $this->deuda_view->get("has_amortizado");
			if($has_amort) {
				$crearLetras = false;
				$this->exception("Ya no puede editar la deuda, por que existe amortizaciones...");
				return;
			} else {
				$this->letra->delete(array("iddeuda"=>$iddeuda));
			}
		}
		
		/* Guardamos las compras de la deuda */
		$this->compra_deuda->delete(array("iddeuda"=>$iddeuda));			
		foreach($post['id_compras'] as $v){
			$datoscompra_d['idcompra']	= $v;
			$datoscompra_d['iddeuda']	= $iddeuda;
			
			$this->compra_deuda->insert($datoscompra_d,false);			
		}
		
		if($crearLetras) {
			if(!empty($post["nro_letra"])) {
				$datosletra["iddeuda"] = $iddeuda;
				// $datosletra["idusuario"] = $this->get_var_session("idusuario");
				$datosletra["tipo_letra"] = "L";
				$datosletra["descripcion"] = "LETRA";
				$datosletra["mora"] = 0;
				$datosletra["interes"] = 0;
				$datosletra["estado"] = "A";
				$datosletra["pagado"] = "N";
				$datosletra["fecha_actualizacion"] = date("Y-m-d");
				
				foreach($post["nro_letra"] as $k => $idletra) {
					$datosletra["idletra"] = $idletra;
					// $datosletra["idtipo_pago"] = null;
					// $datosletra["fecha_cancelado"] = null;
					$datosletra["idforma_pago_compra"]	= (!empty($post["idforma_pago_compra"][$k])) ? $post["idforma_pago_compra"]: null;
					$datosletra["nro_dias_formapago"]	= (!empty($post["nro_dias_formapago"][$k])) ? $post['nro_dias_formapago'][$k]: null;
					$datosletra["fecha_vencimiento"]	= $post["fecha_vencimiento"][$k];
					$datosletra["monto_capital"]		= $post["monto_capital"][$k];
					$datosletra["monto_letra"]			= $post["monto_letra"][$k];
					$datosletra["descuento"]			= $post["descuento_letra"][$k];
					$datosletra["gastos"]				= $post["gasto"][$k];
					$datosletra["nro_letra"]			= $idletra;
					$datosletra["id_referencia"]		= (!empty($post["id_referencia"][$k]))?$post["id_referencia"][$k]:null;
					$this->letra->insert($datosletra, false);
				}
			}
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($this->deuda->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model(array("credito.deuda","credito.deuda_view"));
		
		$this->deuda_view->find($id);
		if($this->deuda_view->get("has_amortizado")) {
			$this->exception("Antes de eliminar el credito primero elimine las amortizaciones realizadas.");
			return;
		}
		
		$this->db->trans_start(); // inciamos transaccion
		$fields['iddeuda'] = $id;
		$fields['estado'] = "I";
		$this->deuda->update($fields);
		$this->db->query("UPDATE compra.letra SET estado='I' WHERE iddeuda='{$id}';");
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($fields);
	}
	
	public function arrary_head(){
		return array("nro_letra"=>array("Letra",5,0,'L')
					,"forma_pago_compra"=>array("Forma Pago",15,0,'L')
					,"fecha_vencimiento"=>array("Fec. Vencimiento",15,0,'L')
					,"monto_letra"=>array("Couta",10,0,'L')
					,"gastos"=>array("Gastos",10,0,'L')
					,"descuento"=>array("Descuento",10,0,'L')
					,"monto_capital"=>array("Total",12,1,'L')
		);
	}
	
	public function arrary_cronograma(){
		return array("nro_letra"=>array("Letra",15,0,'C')
					,"forma_pago_compra"=>array("Forma Pago",30,0,'C')
					,"fecha_venc"=>array("Fec. Vencimiento",30,0,'C')
					,"fecha_pago"=>array("Fec. Pago",25,0,'C')
					,"moneda"=>array("Moneda",30,0,'C')
					,"tipopago"=>array("Pagado con",30,0,'L')
					,"monto_capital"=>array("Importe",32,1,'R')
		);
	}
	
	public function detalle($id){
		$q = $this->db->query("SELECT idletra
								,to_char(fecha_vencimiento,'DD/MM/YYYY')fecha_vencimiento
								,nro_letra
								,gastos
								,descuento
								,monto_letra
								,monto_capital
								,letra.idforma_pago_compra 
								,fp.descripcion forma_pago
								,COALESCE(fp.nrodias,nro_dias_formapago) nrodias
								,'F/'||COALESCE(fp.nrodias,nro_dias_formapago)||' DIAS' forma_pago_compra
								FROM compra.letra 
								LEFT JOIN compra.forma_pago_compra fp ON fp.idforma_pago_compra=letra.idforma_pago_compra
								WHERE letra.estado='A' 
								AND iddeuda='$id' 
								ORDER BY nro_letra");
		
		return $q->result_array();
	}
	
	public function detalle_cronograma($id){
		$sql = "SELECT l.*
				,d.moneda_corto moneda
				,COALESCE(tp.descripcion,'') tipopago 
				,to_char(l.fecha_vencimiento,'DD/MM/YYYY') fecha_venc
				,to_char(fecha_deuda,'DD/MM/YYYY') fecha_credito
				,COALESCE(to_char(fecha_cancelado,'DD/MM/YYYY'),'') fecha_pago
				,comprobante
				,d.nro_credito
				,CASE WHEN l.idletra=qq.id_letra THEN true ELSE false END last_pago
				,'F/'||COALESCE(nro_dias_formapago)||' DIAS' forma_pago_compra
				FROM compra.letra l
				JOIN compra.deuda_view d ON d.iddeuda=l.iddeuda
				LEFT JOIN venta.tipopago tp ON tp.idtipopago=l.idtipo_pago
				LEFT JOIN(
					SELECT MAX(idletra) id_letra, iddeuda idcredito FROM compra.letra ll WHERE ll.estado='A' AND ll.pagado='S' AND pagado='S' GROUP BY idcredito
				) qq ON qq.idcredito=l.iddeuda
				WHERE l.estado='A' 
				AND l.iddeuda='{$id}'
				ORDER BY l.nro_letra";
		$q = $this->db->query($sql);
		return $q->result_array();
	}
	
	public function imprimir($id){
		$data = $this->detalle_cronograma($id);
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","compra.proveedor","seguridad.sucursal","compra.deuda_view"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		$this->pdf->SetTitle(utf8_decode("DETALLE DEUDA"), 11, null, true);

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
		
		$this->deuda_view->find($id);
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('PROVEEDOR'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->deuda_view->get("proveedor")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('RUC'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->deuda_view->get("ruc")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('FECHA DEUDA'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,fecha_es($this->deuda_view->get("fecha_deuda")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('Nro DEUDA'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->deuda_view->get("nro_credito")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('MONEDA'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->deuda_view->get("moneda")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('LETRAS'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->deuda_view->get("cant_letras")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('COMPROBANTE(S)'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->deuda_view->get("comprobante")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('PAGADO'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$pagado='NO';
		if($this->deuda_view->get("pagado")=='S')
			$pagado='SI';
		$this->pdf->Cell(130,6,utf8_decode($pagado),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		foreach($this->arrary_cronograma() as $k=>$val){
			$this->pdf->Cell($val[1],6,$val[0],1,$val[2],'C',true);
		}
		
		$this->pdf->SetFont('Arial','',9);
		$monto_pagado = $monto_pendiente = 0;
		foreach($data as $key=>$v){
			foreach($this->arrary_cronograma() as $k=>$val){
				$this->pdf->Cell($val[1],6,$v[$k],1,$val[2],$val[3]);
			}
			if($v['pagado']=='S')
				$monto_pagado+= $v['monto_capital'];
			else if($v['pagado']=='N')
				$monto_pendiente+= $v['monto_capital'];
		}
		
		$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(90,6,'TOTALES',1,1,'C',true);
		$this->pdf->Cell(40,6,'PAGADO',1,0,'L');$this->pdf->Cell(50,6,number_format($monto_pagado,2,'.',','),1,1,'R');
		$this->pdf->Cell(40,6,'PENDIENTE',1,0,'L');$this->pdf->Cell(50,6,number_format($monto_pendiente,2,'.',','),1,1,'R');
		
		$this->pdf->Output();
	}
}
?>