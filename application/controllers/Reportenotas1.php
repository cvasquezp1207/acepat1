<?php

include_once "Controller.php";

class Reportenotas extends Controller {
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
		
				$sql = "Select 
				nrodocumento nrodoc
				,comprobante_modifica comprobante
				,date(fecha) fechanota
				,date(fecha_venta_format) fechaventa
				,vendedor vendedor
				,cliente
				,motivo
				,total 
				from venta.notacredito_view
				WHERE estado<>'I' 
				{$this->condicion_resumido()}";
				
		$query      = $this->db->query($sql);
		// echo $sql;exit;
		$data = $query->result_array();
		return $data;
	}

	public function condicion_resumido($add_where=''){
		$where = "";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND date(fecha)>='{$_REQUEST['fechainicio']}' AND date(fecha)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND date(fecha)>='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$where.=" AND idvendedor='{$_REQUEST['idvendedor']}' ";
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND idsucursal='{$_REQUEST['idsucursal']}' ";
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
		$whit_fecha=20;
		$whit_zona=40;
		$whit_credt=25;
		$whit_clien=100;
				
		$whit_monto=15;
		$whit_desct=15;
		$whit_total=15;
		
		$whit_cuota=15;
		$whit_moras=16;
		
		$whit_saldo=20;
		return array('nrodoc'=> array('FECHA',$whit_fecha)
							,'comprobante' => array('FECHAVTO',$whit_fecha)
							,'fechanota' => array('ZONA',$whit_zona)
							,'fechaventa' => array('CLIENTE',$whit_clien)
							,'vendedor' => array('CREDITO',$whit_credt)
							,'cliente' => array('DISP',$whit_credt)
						);
	}
	
	public function resumido(){
		set_time_limit(0);
		$datos      = $this->dataresumido();
		$whit_fecha=20;
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
				$this->pdf->SetTitle(utf8_decode("REPORTE DE NOTAS DE CREDITO DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE DE NOTAS DE CREDITO DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		}else
			$this->pdf->SetTitle(utf8_decode("REPORTE DE NOTAS DE CREDITO "), 11, null, true);
			
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
		$this->pdf->Cell(15,10,'NC',1,0,'C');
		$this->pdf->Cell(22,10,'DOC VENTA',1,0,'C');
		$this->pdf->Cell(17,10,'FEC NC',1,0,'C');
		$this->pdf->Cell(17,10,'FEC VTA',1,0,'C');
		$this->pdf->Cell(50,10,'VENDEDOR',1,0,'C'); //63
		$this->pdf->Cell(60,10,'CLIENTE',1,0,'C');
		$this->pdf->Cell(80,10,'MOTIVO',1,0,'C');
		$this->pdf->Cell($whit_saldo,10,'TOTAL','RBL',1,'C');
				
		/************************** CABECERA *****************************************/
		

		/************************** BODY *****************************************/
		$this->pdf->SetFont('Arial','',8);
		$width = array(15,22, 17,17, 50, 60, 80, $whit_saldo);
		$cols  = array('nrodoc','comprobante','fechanota','fechaventa','vendedor','cliente','motivo','total');
		$pos   = array("L", "L","L", "L","L", "L", "L", "R");
		$fill_ = array(false, false,false, false,false, false, false, false,false);
		//$totaltotal = $totalmora = $totalcuota = $total_linea = $totalsaldo = $total = $amortizado = $saldo = $saldo_mora = 0;

		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($datos as $key => $val) {
			//$total_linea = $total_linea;
			
			$this->pdf->setFillColor(249, 249, 249);
			
			$this->pdf->SetWidths($width);
			$values = array();
			
			foreach($cols as $f){
				$values[] = utf8_decode((($val[$f])));
			}
			
			$fill=false;
			
			$this->pdf->Row($values, $pos, "Y", "Y",$fill_);
			
		
		}
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		$this->pdf->Ln(1);
		$this->pdf->SetDrawColor(0, 0, 0);
		
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
	
	public function exportar(){
		set_time_limit(0);
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE NOTA DE CREDITO",true);
				
		$col = 9;		
		if(!empty($_REQUEST['idsucursal'])){
			
			
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->sucursal->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
	
		
		
		/************************** CABECERA *****************************************/
		$name = array("NC","DOC VENTA","FECHA NC","FECHA VENTA","VENDEDOR","CLIENTE","MOTIVO","TOTAL");
		//$subn = array("IMPORTE","PAGO","SALDO","LC");
		$keys = array('nrodoc','comprobante','fechanota','fechaventa','vendedor','cliente', 'motivo','total');
		$alfabeto = $yalfabeto = $zalfabeto = 65;
		$col++;
		
		$xalfabeto = $alfabeto;
		
		foreach ($name as $v) {
			$xalfabeto = $alfabeto;
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $v);
			
			if($v=='TOTAL'){
				$zalfabeto = $yalfabeto = $alfabeto;
				$xalfabeto = $xalfabeto;
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
			
			
			
			$total = $v['total'];
			

			$totaltotal += $v['total'];
			
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
		/************************** PIE *****************************************/
		
		$filename='reportenotacredito'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
}
?>