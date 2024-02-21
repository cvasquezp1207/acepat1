<?php

include_once "Controller.php";

class Reportecredito extends Controller {
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		// $this->set_title("Movimiento de Caja");
		//$this->set_subtitle("Lista de Caja");
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
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$this->load->library('combobox');

		$data["controller"] = $this->controller;
		$data["sucursal"] = $this->listsucursal();
		$data['idperfil'] = $this->get_var_session("idperfil");
		$data['control_reporte'] = $this->get_var_session("control_reporte")?$this->get_var_session("control_reporte"):'N';
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idcliente");
		$this->combobox->setAttr("name","idcliente");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select("idcliente,cliente");
		$query = $this->db->order_by("cliente")->get("venta.cliente_credito_view");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['cliente'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		// $idperfil = 4; // id del perfil vendedor, tal vez deberia ser contante
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil,'A', array('S','N'));
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idvendedor","name"=>"idvendedor","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($datos);
		// if( isset($data["venta"]["idvendedor"]) ) {
			// $this->combobox->setSelectedOption($data["venta"]["idvendedor"]);
		// }
		$data["vendedor"] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idmoneda,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.moneda");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['moneda'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idtipodocumento");
		$this->combobox->setAttr("name","idtipodocumento");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idtipodocumento,descripcion');
		$query = $this->db->where("mostrar_en_venta","S")->where("estado","A")->order_by("descripcion")->get("venta.tipo_documento");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['comprobante'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		$data["sucursal"] = $this->listsucursal();
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");

		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return $this->form();
	}

	public function listsucursal(){
		$idsucursal = $this->get_var_session("idsucursal");
		$whereAnd = '';
		if ($this->get_var_session("control_reporte")=='N') {
			$whereAnd.= ' AND s.idsucursal='.$idsucursal;
		}
		$sql = "SELECT
				s.idsucursal,s.descripcion, idempresa
				FROM seguridad.sucursal s 
				WHERE s.estado='A' AND idempresa IN (SELECT e.idempresa FROM seguridad.empresa e JOIN seguridad.sucursal ss ON ss.idempresa=e.idempresa WHERE ss.idsucursal=$idsucursal $whereAnd)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
		
	public function seleccion($datos,$comparar = array()){
		$data = array();
		foreach($datos as $kk=>$vv){
			if(!empty($comparar)){
				$band = false;
				foreach($comparar as $k=>$v){
					if($vv[$k]==$v)
						$band=true;
					else{
						$band=false;
						break;
					}
				}
				if($band){
					$data[]=$vv;					
				}
			}
		}	
		return $data;
	}
	
	public function dataresumido(){
		$sql = "SELECT 
				to_char(cr.fecha_credito, 'DD/MM/YYYY') fecha_creditos
				, c.zona
				,v.comprobante credito
				,trim(c.cliente) cliente 
				,cr.nro_letras
				,cr.monto_credito AS monto
				,SUM(l.monto_letra) cuota
				,SUM(l.descuento) descuento
				,SUM(l.mora) moras_letras
				,COALESCE(total_amortizacion,0.00) pago
				,COALESCE(moras_amrt,0.00) AS moras_amortizado 
				,(cr.monto_credito)-(SUM(l.descuento)) total
				,cr.monto_credito - COALESCE(total_amortizacion,0.00) saldo
				,c.limite_credito linea
				,c.limite_credito - (cr.monto_credito - COALESCE(total_amortizacion,0.00)) disponible 
				,to_char(MIN(l.fecha_vencimiento),'DD/MM/YYYY') fecha_vencimiento
				,v.vendedor
				,v.idventa,v.idsucursal,v.idcliente
				,suc.descripcion sucursal_venta
				,c.idzona
				,array_to_string(array_agg(q.cobrador),',') cobradores
				,fecha_pago
				FROM credito.credito cr
				JOIN venta.cliente_view c ON c.idcliente=cr.idcliente
				JOIN venta.venta_view v ON v.idventa=cr.idventa
				JOIN seguridad.view_sucursal suc ON suc.idsucursal=v.idsucursal
				JOIN credito.letra l ON l.idcredito=cr.idcredito AND l.estado<>'I' 
				LEFT JOIN(
					SELECT SUM(am.monto) total_amortizacion,am.idletra,am.idcredito,MAX(am.fecha_pago) fecha_pago,SUM(mora) moras_amrt FROM credito.amortizacion am WHERE am.estado<>'I' GROUP BY am.idletra,am.idcredito
				) amrt ON amrt.idletra=l.idletra AND amrt.idcredito=cr.idcredito
				LEFT JOIN (
					SELECT 
						h.idventa
						,h.idzona 
						,h.idsucursal
						,u.user_nombres cobrador
						FROM cobranza.hoja_ruta h
						JOIN seguridad.view_usuario u ON u.idusuario=h.idcobrador
						WHERE h.estado='A'

				) q ON q.idzona=c.idzona AND q.idventa=v.idventa AND q.idsucursal=v.idsucursal
				WHERE cr.estado<>'I' AND suc.idempresa='{$this->get_var_session('idempresa')}'
				{$this->condicion_resumido()}
				GROUP BY fecha_credito,credito,cliente,nro_letras,monto_credito, c.zona, c.limite_credito, v.comprobante
				,v.vendedor,v.idventa,v.idsucursal,v.idcliente, sucursal_venta, c.idzona
				,fecha_pago 
				,v.idventa
				,total_amortizacion,moras_amrt
				ORDER BY cliente , fecha_credito ASC;
				";
		$query      = $this->db->query($sql);
		echo $sql;

		$data = $query->result_array();
		return $data;
	}

	public function condicion_resumido($add_where=''){
		$where = "";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND date(cr.fecha_credito)>='{$_REQUEST['fechainicio']}' AND date(cr.fecha_credito)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND date(cr.fecha_credito)>='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['idcliente'])){
			$where.=" AND cr.idcliente='{$_REQUEST['idcliente']}' ";
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$where.=" AND idtipodocumento='{$_REQUEST['idtipodocumento']}' ";
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$where.=" AND idvendedor='{$_REQUEST['idvendedor']}' ";
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$where.=" AND cr.idmoneda='{$_REQUEST['idmoneda']}' ";
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND cr.idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		
		if(!empty($_REQUEST['pagado'])){
			$where.=" AND cr.pagado='{$_REQUEST['pagado']}' ";
		}
		
		$where.=$add_where;
		return $where;
	}

	public function imprimir(){
		// if($_REQUEST['ver']=='R'){
			$this->resumido();
		// }else if($_REQUEST['ver']=='D'){
			// $this->detallado();
		// }
	}
	
	public function array_head(){
		$whit_fecha=16;
		$whit_zona=40;
		$whit_credt=25;
		$whit_clien=100;
		
		
		$whit_monto=15;
		$whit_desct=15;
		$whit_total=15;
		
		$whit_cuota=15;
		$whit_moras=16;
		
		$whit_saldo=20;
		return array('fecha_creditos'=> array('FECHA',$whit_fecha)
							,'fecha_vencimiento' => array('FECHAVTO',$whit_fecha)
							,'zona' => array('ZONA',$whit_zona)
							,'cliente' => array('CLIENTE',$whit_clien)
							,'credito' => array('CREDITO',$whit_credt)
							,'credito' => array('DISP',$whit_credt)
						);
	}
	
	public function resumido(){
		set_time_limit(0);
		$datos      = $this->dataresumido();
		$whit_fecha=17;
		$whit_zona=39;
		$whit_credt=32;
		$whit_clien=100;
		
		
		$whit_monto=15;
		$whit_desct=15;
		$whit_total=15;
		
		$whit_cuota=16;
		$whit_moras=16;
		
		$whit_saldo=20;

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE CREDITO DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE CREDITO DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		}else
			$this->pdf->SetTitle(utf8_decode("REPORTE CREDITO "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(5);

		$this->pdf->AddPage('L','a4');
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(186,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','B',10);
		
		if(!empty($_REQUEST['idcliente'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"CLIENTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->cliente_view->find($_REQUEST['idcliente']);
			$this->pdf->Cell(5,3,utf8_decode($this->cliente_view->get("cliente")),0,1,'L');
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->sucursal->find($_REQUEST['idsucursal']);
			$this->pdf->Cell(5,3,$this->sucursal->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"MONEDA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->moneda->find($_REQUEST['idmoneda']);
			$this->pdf->Cell(5,3,$this->moneda->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"COMPROBANTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_documento->find($_REQUEST['idtipodocumento']);
			$this->pdf->Cell(5,3,$this->tipo_documento->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"VENDEDOR",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->view_usuario->find($_REQUEST['idvendedor']);
			$this->pdf->Cell(5,3,$this->view_usuario->get("user_nombres"),0,1,'L');
		}
		
		$this->pdf->Ln();
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/		
		$this->pdf->Cell($whit_fecha,10,'FECHA',1,0,'C');
		$this->pdf->Cell($whit_fecha,10,'FECHAVTO',1,0,'C');
		$this->pdf->Cell($whit_zona,10,'ZONA',1,0,'C');
		$this->pdf->Cell($whit_credt,10,'DOC',1,0,'C');
		$this->pdf->Cell($whit_clien,10,'CLIENTE',1,0,'C'); //63
		$this->pdf->Cell(60,5,'CREDITO',1,0,'C');
		
		$this->pdf->Cell(20,5,'','TRL',1,'C');
		
		// FILA 2

		$this->pdf->Cell(204,5,'',0,0,'C');
		
		$this->pdf->Cell($whit_monto,5,'IMPORTE','B',0,'C');
		$this->pdf->Cell($whit_desct,5,'PAGO',1,0,'C');
		$this->pdf->Cell($whit_total,5,'SALDO',1,0,'C');
		
		$this->pdf->Cell($whit_cuota,5,'LC',1,0,'C');
		
		
		$this->pdf->Cell($whit_saldo,5,'DISP','RBL',1,'C');
		/************************** CABECERA *****************************************/
		

		/************************** BODY *****************************************/
		$this->pdf->SetFont('Arial','',8);
		$width = array($whit_fecha,$whit_fecha, $whit_zona,$whit_credt, $whit_clien, $whit_monto, $whit_desct, $whit_total, $whit_cuota,  $whit_saldo);
		$cols  = array('fecha_creditos','fecha_vencimiento','zona','credito','cliente','monto','pago','saldo', 'linea',   'disponible');
		$pos   = array("L", "L","L", "L","L", "R", "R", "R",'R','R','R');
		$fill_ = array(false, false,false, false,false, false, false, false,false,false,false);
		$totaltotal = $totalmora = $totalcuota = $total_linea = $totalsaldo = $total = $amortizado = $saldo = $saldo_mora = 0;

		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($datos as $key => $val) {
			$total_linea = $total_linea;
			
			$this->pdf->setFillColor(249, 249, 249);
			
			$this->pdf->SetWidths($width);
			$values = array();
			
			foreach($cols as $f){
				$values[] = utf8_decode((($val[$f])));
			}
			
			$fill=false;
			
			$this->pdf->Row($values, $pos, "Y", "Y",$fill_);
			
			$total = $val['total'] - $val["descuento"];
			$amortizado = $val['pago'];
			$saldo = $total - $amortizado;
			$saldo_mora = $val["moras_letras"] - $val["moras_amortizado"];

			$totaltotal += $total;
			$totalcuota += $val['pago'];
			$totalmora += $val['saldo'];
			$totalsaldo += $saldo;
		}
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		$this->pdf->Ln(1);
		$this->pdf->SetDrawColor(0, 0, 0);
		$this->pdf->Cell(( 4+$whit_zona+$whit_credt+$whit_clien+$whit_monto+$whit_desct),5,'TOTAL',0,0,'R');
		$this->pdf->Cell(15,5,number_format($totaltotal, 2),1,0,'R');
		$this->pdf->Cell(15,5,number_format($totalcuota, 2),1,0,'R');
		$this->pdf->Cell(15,5,number_format($totalmora, 2),1,0,'R');
		// $this->pdf->Cell(15,5,number_format($total_linea, 2),1,0,'R');
		// $this->pdf->Cell(20,5,number_format($total_linea-$totalsaldo, 2),1,0,'R');
		// $this->pdf->SetFont('Arial','B',8);
		// $this->pdf->Cell(($whit_compr + $whit_fecha + $whit_prov),5,"TOTAL",0,0,'R');
		// foreach($monedas as $k=>$v){
			// $filtro = $this->totales_monedas(" AND idmoneda='$v[idmoneda]' ");
			// $this->pdf->Cell($whit_min,5,number_format($filtro,2,'.',','),0,0,'R');			
		// }
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
	
	public function exportar(){
		set_time_limit(0);
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE CREDITO",true);
		
		
		$col = 9;
		if(!empty($_REQUEST['idcliente'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.cliente_view");
			$this->cliente_view->find($_REQUEST['idcliente']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->cliente_view->get("cliente")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->sucursal->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("general.moneda");
			$this->moneda->find($_REQUEST['idmoneda']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->moneda->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipo_documento");
			$this->tipo_documento->find($_REQUEST['idtipodocumento']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->tipo_documento->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENDEDOR : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("seguridad.view_usuario");
			$this->view_usuario->find($_REQUEST['idvendedor']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->view_usuario->get("user_nombres")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		/************************** CABECERA *****************************************/
		$name = array("FECHA","FECHAVTO","ZONA","DOC","CLIENTE","SUCURSAL","VENDEDOR","COBRADOR(ES)","CREDITO","DISP");
		$subn = array("IMPORTE","PAGO","SALDO","LC");
		$keys = array('fecha_creditos','fecha_vencimiento','zona','credito','cliente','sucursal_venta', 'vendedor','cobradores','monto','pago','saldo', 'linea', 'disponible');
		$alfabeto = $yalfabeto = $zalfabeto = 65;
		$col++;
		
		$xalfabeto = $alfabeto;
		foreach ($name as $v) {
			$xalfabeto = $alfabeto;
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $v);
			
			if($v=='CREDITO'){
				$zalfabeto = $yalfabeto = $alfabeto;
				$xalfabeto = $xalfabeto + 3;
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($xalfabeto).$col);
				$alfabeto = $xalfabeto;
			}
			
			$alfabeto++;
		}
		
		for($i=65;$i<$alfabeto;$i++){
			$Oexcel->getActiveSheet()->getStyle(chr($i).$col)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
		}
		$col++;
		
		foreach($subn as $k=>$v){
			$Oexcel->getActiveSheet()->getStyle(chr($yalfabeto).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue(chr($yalfabeto).$col, $v);
			
			$Oexcel->getActiveSheet()->getStyle(chr($yalfabeto).$col)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			
			$yalfabeto++;			
		}
		$col++;
		/************************** CABECERA *****************************************/
		
		
		/************************** BODY *****************************************/
		$total_importe = $total_pago = $total_saldo = $total_lc = 0;
		$totaltotal = $totalmora = $totalcuota = $total_linea = $totalsaldo = $total = $amortizado = $saldo = $saldo_mora = 0;
		foreach($this->dataresumido() as $k=>$v){
			$alfabeto = 65;
			foreach($keys as $key){
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, (" ".$v[$key]));
				$alfabeto++;
			}
			
			$total_importe = $total_importe + $v['monto'];
			$total_pago = $total_pago + $v['pago'];
			$total_saldo = $total_saldo + $v['saldo'];
			$total_lc = $total_lc + $v['linea'];
			
			$total = $v['total'] - $v["descuento"];
			$amortizado = $v['pago'];
			$saldo = $total - $amortizado;
			$saldo_mora = $v["moras_letras"] - $v["moras_amortizado"];

			$totaltotal += $total;
			$totalcuota += $v['pago'];
			$totalmora += $v['saldo'];
			$totalsaldo += $saldo;
			$col++;
		}
		/************************** BODY *****************************************/
		$alfabeto = 65;
		foreach($this->array_head() as $k=>$v){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$alfabeto++;
		}
		
		/************************** PIE *****************************************/
		$zalfabeto = $zalfabeto -1;
		$Oexcel->getActiveSheet()->setCellValue(chr($zalfabeto).$col, (" TOTAL"));
		$Oexcel->getActiveSheet()->setCellValue(chr($zalfabeto+1).$col, number_format($totaltotal,2,'.',','));
		$Oexcel->getActiveSheet()->setCellValue(chr($zalfabeto+2).$col, number_format($totalcuota,2,'.',','));
		$Oexcel->getActiveSheet()->setCellValue(chr($zalfabeto+3).$col, number_format($totalmora,2,'.',','));
		/************************** PIE *****************************************/
		
		$filename='reportecredito'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
}
?>