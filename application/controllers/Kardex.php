<?php

include_once "Controller.php";

class Kardex extends Controller {
	protected $excel = '';
	protected $col_position = '';
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Kardex de Almacen");
		$this->set_subtitle("Movimientos de los productos");
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
		
	}

	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load->library('combobox');
		
		// combo AÑO
		// $anio = intval(date("Y"));
		// $year = array();
		// for($y=2010; $y <= $anio; $y++) {
			// $year[] = $y;
		// }
		// rsort($year);
		// $this->combobox->init();
		// $this->combobox->setAttr("id", "anio");
		// $this->combobox->setAttr("name", "anio");
		// $this->combobox->setAttr("class", "form-control");
		// $this->combobox->addItem($year);
		// $this->combobox->setSelectedOption($anio);
		// $data["annio"] = $this->combobox->getObject();
		
		//combo periodo
		// $periodo = array();
		// for($i=0;$i<12;$i++){
			// $periodo[] = str_pad($i+1,2,'0', STR_PAD_LEFT);
		// }
		// $this->combobox->init();
		// $this->combobox->setAttr("id", "periodo");
		// $this->combobox->setAttr("name", "periodo");
		// $this->combobox->setAttr("class", "form-control");
		// $this->combobox->addItem($periodo);
		// $this->combobox->setSelectedOption(date("m"));
		// $data["periodo"] = $this->combobox->getObject();
		
		// combo almacen inicio
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idalmacen_i"
				,"name"=>"idalmacen_i"
				,"class"=>"form-control"
			)
		);
		$query = $this->db->select('idalmacen, descripcion')->where("estado", "A")
			->where("idsucursal", $this->get_var_session("idsucursal"))->get("almacen.almacen");
		$this->combobox->addItem("", "TODOS");
		$this->combobox->addItem($query->result_array());
		if( isset($data["compra"]["idalmacen"]) ) {
			$this->combobox->setSelectedOption($data["compra"]["idalmacen"]);
		}
		$data["almacen_i"] = $this->combobox->getObject();
		
		// combo almacen fin.
		// $this->combobox->setAttr(
			// array(
				// "id"=>"idalmacen_f"
				// ,"name"=>"idalmacen_f"
				// ,"class"=>"form-control"
			// )
		// );
		// $data["almacen_f"] = $this->combobox->getObject();
		
		$data["controller"] = $this->controller;
		
		$this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		// $this->css('plugins/iCheck/custom');
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		// $this->js('plugins/iCheck/icheck.min');
		$this->js('form/'.$this->controller.'/form');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	protected function print_total($entradas, $salidas, $total,$tipo="pdf") {
		if($tipo=='pdf'){
			if(isset($this->pdf)) {
				$this->pdf->SetFont('','B',7);
				$this->pdf->Cell(222,5,'TOTALES',0,0,'R');
				$this->pdf->Cell(18,5,number_format($entradas,2,'.',''),'LRBT',0,'R');
				$this->pdf->Cell(18,5,number_format($salidas,2,'.',''),'LRBT',0,'R');
				$this->pdf->Cell(20,5,number_format($total,2,'.',''),'LRBT',0,'R');
				$this->pdf->Cell(13,5,'',0,0,'R');
				$this->pdf->Ln();
			}			
		}else if($tipo=='excel'){
			if(isset($this->excel)){
				$char = 73;
				$this->excel->getActiveSheet()->setCellValue(chr(++$char).($this->col_position), 'TOTALES');
				$this->excel->getActiveSheet()->setCellValue(chr(++$char).($this->col_position), ' '.number_format($entradas,2,'.',''));
				$this->excel->getActiveSheet()->setCellValue(chr(++$char).($this->col_position), ' '.number_format($salidas,2,'.',''));
				$this->excel->getActiveSheet()->setCellValue(chr(++$char).($this->col_position), ' '.number_format($total,2,'.',''));
				$char = 73;
				$this->excel->getActiveSheet()->getStyle(chr(++$char).($this->col_position))->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle(chr(++$char).($this->col_position))->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle(chr(++$char).($this->col_position))->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle(chr(++$char).($this->col_position))->getFont()->setBold(true);
			}
		}
	}
	
	protected function print_almacen($id, $almacen, $type='pdf') {
		if($type=='pdf'){
			if(isset($this->pdf)) {
				$this->pdf->SetFont('','B',7);
				$this->pdf->Cell(288,5,'Almacen : '.$id.' '.$almacen,1,1);
			}			
		}else if($type=='excel'){
			if(isset($this->excel)){
				$this->excel->getActiveSheet()->setCellValue(chr(65).($this->col_position), 'Almacen : '.$id.' '.$almacen);
				$this->excel->getActiveSheet()->getStyle(chr(65).($this->col_position))->getFont()->setBold(true);
			}
		}
	}
	
	protected function print_saldo_inicial($row, $type="pdf") {
		if($type == "pdf") {
			if(isset($this->pdf)) {
				$this->pdf->SetFont('','',7);
				$this->pdf->Cell(14,5,"",'LRBT',0,'C'); // fecha
				$this->pdf->Cell(11,5,"",'LRBT',0,'C'); // hora
				$this->pdf->Cell(35,5,"SALDO INICIAL",'LRBT',0,'L'); // tipo movimiento
				$this->pdf->SetFont('','',6);
				$this->pdf->Cell(37,5,"",'LRBT',0,'L'); // referencia
				$this->pdf->Cell(55,5,"SALDO INICIAL AL ".$row->fecha_emi,'LRBT',0,'L'); // observacion
				$this->pdf->SetFont('','',7);
				$this->pdf->Cell(17,5,"",'LRBT',0,'C'); // nro kardex
				$this->pdf->Cell(16,5,"",'LRBT',0,'C'); // id referencia
				$this->pdf->Cell(10,5,"",'LRBT',0,'C'); // tipo
				$this->pdf->Cell(12,5,"",'LRBT',0,'C'); // serie
				$this->pdf->Cell(15,5,"",'LRBT',0,'L'); // numero
				$this->pdf->Cell(18,5,'','LRBT',0,'R'); // entradas
				$this->pdf->Cell(18,5,"",'LRBT',0,'R'); // salidas
				$this->pdf->Cell(20,5,number_format($row->saldo_inicial,4,'.',''),'LRBT',0,'R');
				$this->pdf->Cell(10,5,$row->u_medida,'LRBT',0,'C'); // um
				$this->pdf->Ln();
			}
		}
		else if($type == "excel") {
			if(isset($this->excel)) {
				$alfabeto = 65;
				foreach($this->head_kardex() as $k=>$v) {
					$val = "";
					if($k == "desc_tipo")
						$val = "SALDO INICIAL";
					else if($k == "observacion")
						$val = "SALDO INICIAL AL ".$row["fecha_emi"];
					else if($k == "suma_cant")
						$val = " ".number_format($row["saldo_inicial"],4,'.','');
					else if($k == "u_medida")
						$val = $row["u_medida"];
					$this->excel->getActiveSheet()->setCellValue(chr($alfabeto).$this->col_position, $val);
					$alfabeto++;
				}
			}
		}
	}
	
	public function condicion($vars=array()){
		$where = $join = "";
		
		if( ! empty($vars["idproveedor"]) && ! empty($vars["proveedor"])) {
			$join .= " and k.idtercero=".intval($vars["idproveedor"]);
			$join .= " and k.tabla='compra'";
		}
		if( ! empty($vars["idproducto"]) && ! empty($vars["producto"])) {
			$where .= " and t.idproducto=".intval($vars["idproducto"]);
		}
		if( ! empty($vars["anio"])) {
			$join .= " and k.annio=".intval($vars["anio"]);
		}
		if( ! empty($vars["periodo"])) {
			$join .= " and k.periodo::integer=".intval($vars["periodo"]);
		}
		if( ! empty($vars["fecha"])) {
			$join .= " and k.fecha_emision='".$vars["fecha"]."'";
		}
		if( ! empty($vars["fecha_i"])) {
			$join .= " and k.fecha_emision>='".$vars["fecha_i"]."'";
		}
		if( ! empty($vars["fecha_f"])) {
			$join .= " and k.fecha_emision<='".$vars["fecha_f"]."'";
		}
		if( ! empty($vars["idalmacen_i"])) {
			$where .= " and t.idalmacen=".intval($vars["idalmacen_i"]);
		}
		
		return array($join, $where);
	}
	
	protected function get_data() {
		$vars = $this->input->get();
		
		list($join, $where) = $this->condicion($vars);
		
		$fecha = "1900-01-01";
		if( ! empty($vars["fecha_i"]))
			$fecha = $vars["fecha_i"];
		
		$sql = "SELECT coalesce(k.correlativo,-1) as correlativo, t.idproducto, p.descripcion_detallada as producto, 
			u.abreviatura as u_medida, a.idalmacen, a.descripcion as almacen, k.tipo_movimiento, k.tipo, k.desc_tipo, 
			k.tabla, k.fecha_emision, coalesce(k.fecha_emi, '".fecha_es($fecha)."') as fecha_emi,
			k.hora, k.observacion, k.idreferencia, k.tipo_docu, k.serie, k.numero, k.cantidad, k.costo_unit_s, 
			k.importe_s, k.idtercero, k.referencia, k.estado,
			almacen.get_saldo_inicial(t.idproducto, t.idalmacen, coalesce(k.fecha_emision,'{$fecha}'::date)) as saldo_inicial
			FROM (
				SELECT DISTINCT idproducto, idalmacen FROM almacen.kardex WHERE estado <> 'I'
			) as t
			JOIN compra.producto p ON p.idproducto = t.idproducto
			JOIN compra.unidad u ON u.idunidad = p.idunidad
			JOIN almacen.almacen a ON a.idalmacen = t.idalmacen
			LEFT JOIN (
				SELECT k.correlativo, k.idproducto, k.idalmacen, 
					t.tipo_movimiento, k.tipo, t.descripcion desc_tipo, k.tabla, k.fecha_emision, 
					to_char(k.fecha_emision,'DD/MM/YYYY') fecha_emi, k.hora, k.observacion, k.idreferencia, k.tipo_docu, 
					k.serie, k.numero, k.cantidad*coalesce(k.cantidad_um,1) as cantidad, k.costo_unit_s, k.importe_s
					,k.idtercero,refe.cliente referencia,k.estado
					FROM almacen.kardex k
					INNER JOIN almacen.tipo_movimiento t ON t.tipo_movimiento = k.tipo_movimiento
					JOIN venta.cliente_view refe ON refe.idcliente=COALESCE(k.idtercero,0) AND tabla IN('venta','notacredito')
				UNION
				SELECT k.correlativo, k.idproducto, k.idalmacen, 
					t.tipo_movimiento, k.tipo, t.descripcion desc_tipo, k.tabla, k.fecha_emision, 
					to_char(k.fecha_emision,'DD/MM/YYYY') fecha_emi, k.hora, k.observacion, k.idreferencia, k.tipo_docu, 
					k.serie, k.numero, k.cantidad*coalesce(k.cantidad_um,1) as cantidad, k.costo_unit_s, k.importe_s
					,k.idtercero,refe.nombre referencia,k.estado
					FROM almacen.kardex k
					INNER JOIN almacen.tipo_movimiento t ON t.tipo_movimiento = k.tipo_movimiento
					JOIN compra.proveedor refe ON refe.idproveedor=COALESCE(k.idtercero,0) AND tabla IN('compra')
				UNION
				SELECT k.correlativo, k.idproducto, k.idalmacen, 
					t.tipo_movimiento, k.tipo, t.descripcion desc_tipo, k.tabla, k.fecha_emision, 
					to_char(k.fecha_emision,'DD/MM/YYYY') fecha_emi, k.hora, k.observacion, k.idreferencia, k.tipo_docu, 
					k.serie, k.numero, k.cantidad*coalesce(k.cantidad_um,1) as cantidad, k.costo_unit_s, k.importe_s
					,k.idtercero,'' referencia,k.estado
					FROM almacen.kardex k
					INNER JOIN almacen.tipo_movimiento t ON t.tipo_movimiento = k.tipo_movimiento
					WHERE tabla IN ('despacho','guia_remision','inventario','recepcion')
			) k ON k.idproducto = t.idproducto AND k.idalmacen = t.idalmacen AND k.estado <> 'I' {$join}
			WHERE 1=1 {$where} 
			ORDER BY idproducto, idalmacen, fecha_emision, hora";
		// echo $sql;exit;
		return $this->db->query($sql);
	}
	
	public function head_kardex() {
		return array('fecha_emi'=>array('FECHA',14,'L',0)
					,'hora'=>array('HORA',11,'L',0)
					,'desc_tipo'=>array('TIPO MOVIMIENTO',35,'L',0)
					,'referencia'=>array('REFERENCIA',37,'L',0)
					,'observacion'=>array('OBSERVACIONES',55,'L',0)
					,'correlativo'=>array('Nro. KARDEX',17,'L',0)
					,'idreferencia'=>array('ID.REFER.',16,'L',0)
					,'tipo_docu'=>array('TIPO',10,'L',0)
					,'serie'=>array('SERIE',12,'L',0)
					,'numero'=>array('NUMERO',15,'L',0)
					,'entradas'=>array('ENTRADAS',18,'L',0)		//entradas
					,'salidas'=>array('SALIDAS',18,'L',0)		//salidas
					,'suma_cant'=>array('SALDO FINAL',20,'L',0)		//suma_cant
					,'u_medida'=>array('U.M.',12,'L',1)
					);
	}
	
	public function reporte_kardex() {
		$this->unlimit();
		if($_REQUEST['opc_tipo']=='1')
			$this->formatopdf();
		else if($_REQUEST['opc_tipo']=='2')
			$this->formatoexcel();
	}
	
	public function formatopdf_1(){
		set_time_limit(0);
		$this->load_model(array("almacen.kardex", "seguridad.sucursal", "seguridad.empresa"));
		
		$this->load->library("pdf");
		$this->pdf->SetTitle(utf8_decode("HISTORIAL DE MOVIMIENTOS DE STOCK DE PRODUCTOS"), 11, null, true);
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage("L");
		
		$query2 = $this->get_data();
		
		/* DETALLE */
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->SetLeftMargin(4);		
		$a = 6;
		
		$this->pdf->SetXY(4,25);
		$width = array();
		$pos = array();
		foreach($this->head_kardex() as $k=>$val){
			$this->pdf->Cell($val[1],$a,$val[0],1,0,'C');
			$width[]	= $val[1];
			$pos[]		= $val[2];
		}
		
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Ln();
		
		$item = $dsit = $alma = $dsal = '';
		$this->pdf->SetDrawColor(204, 204, 204);
		$suma_cant = 0;
		
		foreach ($query2->result_array() as $key=>$row){
			if($item != $row["idproducto"]){
				if($item!=''){
					$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
				}
				$item = $row["idproducto"];
				$dsit = $row["producto"];
		  
				$this->pdf->Ln();
				$this->pdf->SetFont('','B',7);
				$this->pdf->Cell(273,5,'ITEM : '.strtoupper($item.' '.$dsit),'',0,'L');
				
				$alma = $row["idalmacen"];
				$dsal = $row["almacen"];
				$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
				$this->pdf->Ln();
				$this->print_almacen($alma, $dsal);
			}else{
				$item = $row["idproducto"];
				$dsit = $row["producto"];
		  
				if($alma != $row["idalmacen"]){
					if($alma != ''){
						$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
					}
					$alma = $row["idalmacen"];
					$dsal = $row["almacen"];
				
					$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
					$this->pdf->Ln();
					$this->print_almacen($alma, $dsal);
				}else{
					$alma = $row["idalmacen"];
					$dsal = $row["almacen"];
				}
			}
			
			/*For file autosize*/
			$values = array();
			/*For file autosize*/
			
			$this->pdf->SetFont('Arial','',6);
			if($row["tipo"] == 'ENT'){
				$row["entradas"] = number_format($row["cantidad"],2,'.','');
				$row["salidas"] = '';
				$varsum = (float) $row["cantidad"];
				$suma_cant_ent = $suma_cant_ent + $varsum;		  
				$suma_cant = $suma_cant + $varsum;
			}elseif($row["tipo"] == 'SAL'){
				$row["entradas"]	= '';
				$row["salidas"]		= $row["cantidad"];
				$varsum = (float) $row["cantidad"];
				$suma_cant_sal = $suma_cant_sal + $varsum;
				$suma_cant = $suma_cant - $varsum;
			}
			$row["suma_cant"] = number_format($suma_cant,4,'.','');
			
			foreach($this->head_kardex() as $k=>$val){
				$values[] = utf8_decode($row[$k]);
			}
			
			
			$this->pdf->SetWidths($width);
			$this->pdf->Row($values, $pos, "Y", "Y");
			// $this->pdf->Cell(14,5,$row->fecha_emi,'LRBT',0,'C');
			// $this->pdf->Cell(12,5,$row->hora,'LRBT',0,'C');
			// $this->pdf->Cell(50,5,$row->desc_tipo,'LRBT',0,'L');
			// $this->pdf->SetFont('','',6);
			// $this->pdf->Cell(65,5,substr($row->observacion,0,45),'LRBT',0,'L');
			// $this->pdf->SetFont('','',7);
			// $this->pdf->Cell(17,5,$row->correlativo,'LRBT',0,'C');
			// $this->pdf->Cell(16,5,$row->idreferencia,'LRBT',0,'C');
			// $this->pdf->Cell(10,5,$row->tipo_docu,'LRBT',0,'C');
			// $this->pdf->Cell(12,5,$row->serie,'LRBT',0,'C');
			// $this->pdf->Cell(15,5,$row->numero,'LRBT',0,'L');
		
			// if($row->tipo == 'ENT'){
				// $this->pdf->Cell(18,5,number_format($row->cantidad,2,'.',''),'LRBT',0,'R');
				// $this->pdf->Cell(18,5,'','LRBT',0,'L');
				// $varsum = (float) $row->cantidad;
				// $suma_cant_ent = $suma_cant_ent + $varsum;		  
				// $suma_cant = $suma_cant + $varsum;
			// }elseif($row->tipo == 'SAL'){
				// $this->pdf->Cell(18,5,'','LRBT',0,'L');
				// $this->pdf->Cell(18,5,$row->cantidad,'LRBT',0,'R');
				// $varsum = (float) $row->cantidad;
				// $suma_cant_sal = $suma_cant_sal + $varsum;
				// $suma_cant = $suma_cant - $varsum;
			// }
			// $this->pdf->Cell(20,5,number_format($suma_cant,4,'.',''),'LRBT',0,'R');
			// $this->pdf->Cell(12,5,$row->u_medida,'LRBT',0,'C');
			// $this->pdf->Ln();
		}
		
		if ($query2->num_rows() > 0){
			$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
		}
		
		$this->pdf->Output();
	}
	
	public function formatopdf_0(){
		set_time_limit(0);
		$this->load_model(array("almacen.kardex", "seguridad.sucursal", "seguridad.empresa"));
		
		$this->load->library("pdf");
		// $this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode("HISTORIAL DE MOVIMIENTOS DE STOCK DE PRODUCTOS"), 11, null, true);
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage("L");
		
		$query2 = $this->get_data();
		
		//DETALLE
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->SetLeftMargin(4);		
		$a = 6;
		
		$this->pdf->SetXY(4,25);
		foreach($this->head_kardex() as $k=>$val){
			$this->pdf->Cell($val[1],$a,$val[0],1,0,'C');
		}
		
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Ln();
		// $this->pdf->SetX(8);
		
		$item = $dsit = $alma = $dsal = '';
		// $this->SetFillColor(255,0,0);
		// $this->SetTextColor(0);
		// $this->SetDrawColor(128,0,0);
		// $this->SetLineWidth(.3);
		$this->pdf->SetDrawColor(204, 204, 204);
		$suma_cant = 0;
		
		foreach ($query2->result() as $row){
			if($item != $row->idproducto){
				if($item!=''){
					$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
				}
				$item = $row->idproducto;
				$dsit = $row->producto;
		  
				$this->pdf->Ln();
				$this->pdf->SetFont('','B',7);
				$this->pdf->Cell(273,5,'ITEM : '.strtoupper($item.' '.$dsit),'',0,'L');
				
				$alma = $row->idalmacen;
				$dsal = $row->almacen;
				$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
				$this->pdf->Ln();
				$this->print_almacen($alma, $dsal);
			}else{
				$item = $row->idproducto;
				$dsit = $row->producto;
		  
				if($alma != $row->idalmacen){
					if($alma != ''){
						$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
					}
					$alma = $row->idalmacen;
					$dsal = $row->almacen;
				
					$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
					$this->pdf->Ln();
					$this->print_almacen($alma, $dsal);
				}else{
					$alma = $row->idalmacen;
					$dsal = $row->almacen;
				}
			}
			$this->pdf->SetFont('','',7);
			$this->pdf->Cell(14,5,$row->fecha_emi,'LRBT',0,'C');
			$this->pdf->Cell(12,5,$row->hora,'LRBT',0,'C');
			$this->pdf->Cell(50,5,$row->desc_tipo,'LRBT',0,'L');
			$this->pdf->SetFont('','',6);
			$this->pdf->Cell(65,5,substr($row->observacion,0,45),'LRBT',0,'L');
			$this->pdf->SetFont('','',7);
			$this->pdf->Cell(17,5,$row->correlativo,'LRBT',0,'C');
			$this->pdf->Cell(16,5,$row->idreferencia,'LRBT',0,'C');
			$this->pdf->Cell(10,5,$row->tipo_docu,'LRBT',0,'C');
			$this->pdf->Cell(12,5,$row->serie,'LRBT',0,'C');
			$this->pdf->Cell(15,5,$row->numero,'LRBT',0,'L');
		
			if($row->tipo == 'ENT'){
				$this->pdf->Cell(18,5,number_format($row->cantidad,2,'.',''),'LRBT',0,'R');
				$this->pdf->Cell(18,5,'','LRBT',0,'L');
				$varsum = (float) $row->cantidad;
				$suma_cant_ent = $suma_cant_ent + $varsum;		  
				$suma_cant = $suma_cant + $varsum;
			}elseif($row->tipo == 'SAL'){
				$this->pdf->Cell(18,5,'','LRBT',0,'L');
				$this->pdf->Cell(18,5,$row->cantidad,'LRBT',0,'R');
				$varsum = (float) $row->cantidad;
				$suma_cant_sal = $suma_cant_sal + $varsum;
				$suma_cant = $suma_cant - $varsum;
			}
			$this->pdf->Cell(20,5,number_format($suma_cant,4,'.',''),'LRBT',0,'R');
			$this->pdf->Cell(12,5,$row->u_medida,'LRBT',0,'C');
			$this->pdf->Ln();
		}
		
		if ($query2->num_rows() > 0){
			$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
		}
		
		$this->pdf->Output();
	}
	
	public function formatopdf(){
		set_time_limit(0);
		$this->load_model(array("almacen.kardex", "seguridad.sucursal", "seguridad.empresa"));
		
		$this->load->library("pdf");
		// $this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode("HISTORIAL DE MOVIMIENTOS DE STOCK DE PRODUCTOS"), 11, null, true);
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage("L");
		
		$query2 = $this->get_data();
		
		//DETALLE
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->SetLeftMargin(4);		
		$a = 6;
		
		$this->pdf->SetXY(4,25);
		foreach($this->head_kardex() as $k=>$val){
			$this->pdf->Cell($val[1],$a,$val[0],1,0,'C');
		}
		
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Ln();
		// $this->pdf->SetX(8);
		
		$item = $dsit = $alma = $dsal = '';
		// $this->SetFillColor(255,0,0);
		// $this->SetTextColor(0);
		// $this->SetDrawColor(128,0,0);
		// $this->SetLineWidth(.3);
		$this->pdf->SetDrawColor(204, 204, 204);
		$suma_cant = 0;
		
		foreach ($query2->result() as $row){
			if($row->correlativo == -1 && $row->saldo_inicial <= 0) // saldo inicial
				continue;
			
			if($item != $row->idproducto){
				if($item!=''){
					$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
				}
				$item = $row->idproducto;
				$dsit = $row->producto;
		  
				$this->pdf->Ln();
				$this->pdf->SetFont('','B',7);
				$this->pdf->Cell(273,5,'ITEM : '.strtoupper($item.' '.$dsit),'',0,'L');
				
				$alma = $row->idalmacen;
				$dsal = $row->almacen;
				$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
				$this->pdf->Ln();
				$this->print_almacen($alma, $dsal);
				$this->print_saldo_inicial($row);
				$suma_cant = $row->saldo_inicial;
			}else{
				$item = $row->idproducto;
				$dsit = $row->producto;
		  
				if($alma != $row->idalmacen){
					if($alma != ''){
						$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
					}
					$alma = $row->idalmacen;
					$dsal = $row->almacen;
				
					$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
					$this->pdf->Ln();
					$this->print_almacen($alma, $dsal);
					$this->print_saldo_inicial($row);
					$suma_cant = $row->saldo_inicial;
				}else{
					$alma = $row->idalmacen;
					$dsal = $row->almacen;
				}
			}
			
			if($row->correlativo == -1) // saldo inicial
				continue;
			
			$this->pdf->SetFont('','',7);
			$this->pdf->Cell(14,5,$row->fecha_emi,'LRBT',0,'C');
			$this->pdf->Cell(11,5,$row->hora,'LRBT',0,'C');
			$this->pdf->Cell(35,5,$row->desc_tipo,'LRBT',0,'L');
			$this->pdf->SetFont('','',6);
			$this->pdf->Cell(37,5,substr($row->referencia,0,45),'LRBT',0,'L');
			$this->pdf->Cell(55,5,substr($row->observacion,0,45),'LRBT',0,'L');
			$this->pdf->SetFont('','',7);
			$this->pdf->Cell(17,5,$row->correlativo,'LRBT',0,'C');
			$this->pdf->Cell(16,5,$row->idreferencia,'LRBT',0,'C');
			$this->pdf->Cell(10,5,$row->tipo_docu,'LRBT',0,'C');
			$this->pdf->Cell(12,5,$row->serie,'LRBT',0,'C');
			$this->pdf->Cell(15,5,$row->numero,'LRBT',0,'L');
		
			if($row->tipo == 'ENT'){
				$this->pdf->Cell(18,5,number_format($row->cantidad,2,'.',''),'LRBT',0,'R');
				$this->pdf->Cell(18,5,'','LRBT',0,'L');
				$varsum = (float) $row->cantidad;
				$suma_cant_ent = $suma_cant_ent + $varsum;		  
				$suma_cant = $suma_cant + $varsum;
			}elseif($row->tipo == 'SAL'){
				$this->pdf->Cell(18,5,'','LRBT',0,'L');
				$this->pdf->Cell(18,5,$row->cantidad,'LRBT',0,'R');
				$varsum = (float) $row->cantidad;
				$suma_cant_sal = $suma_cant_sal + $varsum;
				$suma_cant = $suma_cant - $varsum;
			}
			$this->pdf->Cell(20,5,number_format($suma_cant,4,'.',''),'LRBT',0,'R');
			$this->pdf->Cell(10,5,$row->u_medida,'LRBT',0,'C');
			$this->pdf->Ln();
		}
		
		if ($query2->num_rows() > 0){
			$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant);
		}
		
		$this->pdf->Output();
	}
	
	public function formatoexcel(){
		set_time_limit(0);
		$query2 = $this->get_data();
		$this->load_model(array("almacen.kardex", "seguridad.sucursal", "seguridad.empresa"));
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->excel = $Oexcel;
		$this->insert_logoExcel($Oexcel,"HISTORIAL DE MOVIMIENTOS DE STOCK DE PRODUCTOS",true);
		
		$col = 9;
		/************************** CABECERA *****************************************/
		$alfabeto = 65;
		$col++;
		$styleHead = array(
			  'borders' => array(
				 'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF000000'),
				 ),
			),
		);
		
		foreach($this->head_kardex() as $k=>$v){
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $v[0]);			
			$alfabeto++;
		}
		$col++;
		/************************** CABECERA *****************************************/
		
		
		/************************** BODY *****************************************/
		$item = $dsit = $alma = $dsal = '';
		$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
		$tipo='excel';
		foreach ($query2->result_array() as $key=>$row){
			if($row["correlativo"] == -1 && $row["saldo_inicial"] <= 0) // saldo inicial
				continue;
			
			$alfabeto = 65;
			$row["item"] = '';
			
			if($item != $row["idproducto"]){
					if($item!=''){
						$this->col_position = $col;
						$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant, $tipo);
						$col ++;
					}
					$item = $row["idproducto"];
					$dsit = $row["producto"];

					$col++;
					$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
					$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'ITEM : '.strtoupper($item.' '.$dsit));
					
					$alma = $row["idalmacen"];
					$dsal = $row["almacen"];
					$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
					$col ++;
					
					$this->col_position = $col;
					$this->print_almacen($alma, $dsal,$tipo);
					$this->col_position = ++$col;
					$this->print_saldo_inicial($row, $tipo);
					$col ++;
					$suma_cant = $row["saldo_inicial"];
			}else{
				$item = $row["idproducto"];
				$dsit = $row["producto"];
					
				if($alma != $row["idalmacen"]){
					if($alma != ''){
						$this->col_position = $col;
						$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant, $tipo);
						$col ++;
					}
					$alma = $row["idalmacen"];
					$dsal = $row["almacen"];
				
					$suma_cant = $suma_cant_ent = $suma_cant_sal = 0;
					$col ++;
					$this->col_position = $col;
					$this->print_almacen($alma, $dsal, $tipo);
					$this->col_position = ++$col;
					$this->print_saldo_inicial($row, $tipo);
					$suma_cant = $row["saldo_inicial"];
					$col ++;
				}else{
					$alma = $row["idalmacen"];
					$dsal = $row["almacen"];
				}
			}
			
			if($row["correlativo"] == -1) // saldo inicial
				continue;
			
			if($row["tipo"] == 'ENT'){
				$row["entradas"] = number_format($row["cantidad"],2,'.','');
				$row["salidas"] = '';
				
				$varsum = (float) $row["cantidad"];
				$suma_cant_ent = $suma_cant_ent + $varsum;
				$suma_cant = $suma_cant + $varsum;
			}elseif($row["tipo"] == 'SAL'){
				$row["entradas"] = '';
				$row["salidas"] = number_format($row["cantidad"],2,'.','');

				$varsum = (float) $row["cantidad"];
				$suma_cant_sal = $suma_cant_sal + $varsum;
				$suma_cant = $suma_cant - $varsum;
			}
			$row["suma_cant"] = number_format($suma_cant,4,'.','');
			
			foreach($this->head_kardex() as $k=>$v){
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, (" ".$row[$k]));
				$alfabeto++;
			}
			$col++;
		}
		if ($query2->num_rows() > 0){
			$this->col_position = $col;
			$this->print_total($suma_cant_ent, $suma_cant_sal, $suma_cant, $tipo);
		}
		/************************** BODY *****************************************/
		$Oexcel->getActiveSheet()->getColumnDimension(chr(68))->setWidth('35');
		$Oexcel->getActiveSheet()->getColumnDimension(chr(69))->setWidth('40');
		// $Oexcel->getActiveSheet()->getColumnDimension(chr(69))->setAutoSize(true);
		
		$filename='kardex'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
}
?>