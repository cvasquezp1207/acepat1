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
				nrodocumento doc_cliente
				,comprobante_modifica comprobante
				,date(fecha) fecha_operacion --fechanota
				,date(fecha_venta_format) fechaventa
				,vendedor vendedor
				,cliente 
				,motivo tipopago
				,total  total_compra
				from venta.notacredito_view
				WHERE estado<>'I' 
				{$this->condicion_resumido()}";
		// echo "<pre>";echo $sql;exit;
		$query      = $this->db->query($sql);
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
	
			$this->resumido();
	
	}
	
	public function resumido(){
		set_time_limit(0);
		$datos      = $this->dataresumido();
		
		$whit_compr=30;
		$whit_fecha=18;
		$whit_prov=60;
		$whit_vend=60;
		$whit_min = 20;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 20;//PARA EL WHIT DE LAS MONEDAS
		
		
		$cabecera = $this->array_head();

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","venta.tipopago","general.zona","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTES NOTAS DE CREDITO DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE NOTAS DE CREDITO DE  ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE NOTAS DE CREDITO  "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage('L');
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(200,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','B',10);
		
										
		$this->pdf->Ln();
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,0);
			$total_lienzo = $total_lienzo + $val[1];
		}
		$this->pdf->Cell($whit_total_m,5,"TOTAL",1,1,'C');
		$this->pdf->Cell($total_lienzo,5,"",0,0,'C');
		
		$this->pdf->Ln(10); 
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		/************************** BODY *****************************************/
		$item = 1;
		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($datos as $key => $val) {
			/*For file autosize*/
			$values = array();
			$width = array();
			$pos = array();
			/*For file autosize*/
			
			foreach ($cabecera as $k => $v) {
				$val['item']=$item;
				if(isset($v[2])){
				}else{
					$v[2]='L';
				}
				$width[] = $v[1];
				$values[] = utf8_decode($val[$k]);
				$pos[] = $v[2];
			}
			
				$subt = 0;
					$subt = $val['total_compra'];
		
				$width[] = $whit_min;
				$values[] = number_format($subt,2);
				$pos[] = 'R';
					
			$this->pdf->SetWidths($width);
			$this->pdf->Row($values, $pos, "Y", "Y");
		
			$item++;
		}
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(($whit_compr + $whit_fecha + $whit_prov),5,"TOTAL",0,0,'R');
		
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}	
	public function exportar(){
	
			$this->exportar_resumido();
	
	}
	
	public function array_head(){
		$whit_item		= 9;
		$whit_compr		= 21;
		$whit_fecha		= 17;
		$whit_cli		= 75;
		$whit_vend		= 45;
		$whit_tpgo		= 80;
		
		$total_ancho	= $whit_compr+$whit_fecha+$whit_cli+$whit_vend + $whit_tpgo;

		$cabecera = array( 'item' => array('ITEM',$whit_item,'L')
							,'comprobante' => array('COMPROB',$whit_compr,'L')
							,'fecha_operacion' => array('FECHA',$whit_fecha,'L')
							,'doc_cliente' => array('NRO NC',$whit_fecha,'L')
							,'cliente' => array('CLIENTE',$whit_cli,'L')
							,'vendedor' => array('VENDEDOR',$whit_vend,'L')
							,'tipopago' => array('MOTIVO',$whit_tpgo,'L')
						);
						
		return $cabecera;
	}

	public function array_detalle(){
		$whit_fecha		=10;
		$whit_prov		=115;
		$whit_cant		=10;
		$whit_um		=20;
		$whit_fac		=20;
		$whit_pu		=20;
		$whit_igv		=15;
		$whit_min 		= 20;//PARA EL WHIT DE LAS MONEDAS

		$cabecera = array('item' => array('ITEM',$whit_fecha,'L')
							,'producto' => array('PRODUCTO',$whit_prov,'L')
							,'cantidad_detalle' => array('CANT',$whit_cant,'L')
							,'unidadmedida' => array('UM',$whit_um,'L')
							,'factor' => array('FACTOR',$whit_fac,'R')
							,'precio_venta_detalle' => array('P.U',$whit_pu,'R')
							,'subtotal' => array('TOTAL',$whit_min,'R')
						);
		return $cabecera;
	}
	
	
	public function exportar_resumido(){
		set_time_limit(0);
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('CSV');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE NOTAS DE CREDITO",true);
		
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
		
		foreach ($this->array_head() as $key => $v) {
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
		
		//$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
		$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'TOTAL');
		
		$col++;
		
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
	
			
			$alfabeto++;
		
		$col++;
		/************************** CABECERA *****************************************/
		
		
		/************************** CUERPO *****************************************/
		$rows 		= $this->dataresumido();
		
		foreach($rows as $key=>$val){
			$val['item'] = (int) $key + 1;

			$alfabeto = 65;
			foreach($this->array_head() as $k=>$v){
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, (" ".$val[$k]));
				$alfabeto++;
			}
				$subt = 0;
	
					$subt = $val['total_compra'];
					$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, (number_format($subt,2,'.','')));
				$Oexcel->getActiveSheet()->getStyle(chr($alfabeto))->getNumberFormat()->applyFromArray(
					array(
						'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
					)
				);
				$alfabeto++;
			//}
			$col++;
		}
		/************************** CUERPO *****************************************/
	
		$alfabeto = 65;

		foreach($this->array_head() as $k=>$v){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$alfabeto++;
		}
	
		$filename='reportenotas'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
	
		
	/* Primera version del reporte, agrupado por comprobante y cliente */
	public function exportarDetallado_agrupado(){
		set_time_limit(0);
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		$data_detallado = $this->dataDetallado();
		$data_cliente	= $this->cliente_agrupado();
		$head_cliente	= $this->informacion_cliente();

		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('CSV');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE VENTAS DETALLADOS",true);
		
		$filename='reporteventadetallado_total'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        /************************** CABECERA *****************************************/
		$alfabeto = 65;
		$col=9;
		$styleHead = array(
			  'borders' => array(
				 'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF000000'),
				 ),
			),
		);

		foreach ($data_cliente as $key => $val) {
			$alfabeto=65;

			// $informativo = $this->seleccion($head_cliente,array('idcliente'=>$val['idcliente']));
			
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'CLIENTE');
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto+1).$col)->getFont()->setBold(true)->getColor()->setRGB('5abce8');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$val['cliente']);
			$col++;
			
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'DIRECCION: ');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$val['direccion']);
			$col++;
				
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val['documento_cliente']);
			$col++;

			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'ZONA');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$val['zona']);
			$col++;

			// $aalfabeto = 66;
			
			$data_head_detealle = $this->seleccion($this->comprobante_agrupado(), array("idcliente"=>$val['idcliente']));
			foreach($data_head_detealle as $kk=>$vv){
				$aalfabeto=66;
				
				// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('1676a0');
				// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $vv['tipodocumento']." ".$vv['comprobante']);
				// $Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($alfabeto+3).$col);
				
				// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto+4).$col)->getFont()->setBold(true)->getColor()->setRGB('1676a0');
				// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+4).$col, $vv['fecha_operacion']);
				// $Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto+4).$col.':'.chr($alfabeto+5).$col);

				// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto+6).$col)->getFont()->setBold(true)->getColor()->setRGB('1676a0');
				// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+6).$col, $vv['moneda']." TC: ".number_format($vv['cambio_moneda'],2));
				// $Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto+6).$col.':'.chr($alfabeto+7).$col);

				// $col++;
				
				// Aqui poner la demas informacion
				// $informativo = $this->seleccion($informativo,array('idventa'=>$vv['idventa']));
				// $informativo = $this->seleccion($head_cliente,array('idcliente'=>$val['idcliente'],'idventa'=>$vv['idventa']));
				// foreach($informativo as $xk=>$yv){
					// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('1676a0');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'SUC. VENTA');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ' : '.$yv['sucursal_venta']);
					// $col++;

					// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('1676a0');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'MONEDA');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$yv['moneda']);
					// $col++;

					// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('1676a0');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'TIPO VENTA');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$yv['tipoventa']);
					// $col++;

					// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('1676a0');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'VENDEDOR');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$yv['vendedor']);
					// $col++;

					// $Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'CAJERO');
					// $Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$yv['cajero']);					
				// }

				$col++;
				$data_body_detealle = $this->seleccion($data_detallado, array("idcliente"=>$val['idcliente'],'idventa'=>$vv['idventa']));
				$x_cant_aux=0;
				$total_pu= $total_igv=$total_total_c=0;
				foreach ($this->array_detalle_more() as $key => $v) {
					$Oexcel->getActiveSheet()->getStyle(chr($aalfabeto).$col)->getFont()->setBold(true);
					$Oexcel->getActiveSheet()->getStyle(chr($aalfabeto).$col)->applyFromArray(
						array('borders' => array(
										'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
										'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
										'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
										'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
									)
						)
					);
					$Oexcel->getActiveSheet()->setCellValue(chr($aalfabeto).$col, $v[0]);
					
					$aalfabeto++;
				}
				$col++;
				
				$alfabeto=66;
				foreach($data_body_detealle as $kkk=>$vvv){
					$x_cant_aux++;
					$vvv['item']= $x_cant_aux.")";
					
					$alfabeto=66;
					foreach ($this->array_detalle_more() as $ke => $va) {
						$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $vvv[$ke]);
						$alfabeto++;
					}
					
					$total_pu 		= $total_pu + $vvv['precio_venta_detalle'];
					$total_total_c 	= $total_total_c + $vvv['subtotal'];
					$col++;
				}
				$Oexcel->getActiveSheet()->setCellValue(chr(66+count($this->array_detalle_more())-2).$col, $total_pu);
				$Oexcel->getActiveSheet()->getStyle(chr(66+count($this->array_detalle_more())-2).$col)->applyFromArray(
					array('borders' => array(
									'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
								)
					)
				);
				$Oexcel->getActiveSheet()->setCellValue(chr(66+count($this->array_detalle_more())-1).$col, $total_total_c);
				$Oexcel->getActiveSheet()->getStyle(chr(66+count($this->array_detalle_more())-1).$col)->applyFromArray(
					array('borders' => array(
									'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
								)
					)
				);
				$col=$col+2;
			}
		}
		/************************** CABECERA *****************************************/
		
		$objWriter->save('php://output');
	}
	
	public function exportarDetallado(){
		set_time_limit(0);
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		$data_detallado = $this->dataDetallado(false);
		
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE VENTAS DETALLADOS",true);
		
		$filename='reporteventadetallado_total'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');

        /************************** CABECERA *****************************************/
		$alfabeto = 65;
		$col=9;
		
		$aalfabeto=65;
		foreach ($this->array_detalle_more() as $key => $v) {
			$Oexcel->getActiveSheet()->getStyle(chr($aalfabeto).$col)->getFont()->setBold(true);
			
			$Oexcel->getActiveSheet()->getStyle(chr($aalfabeto).$col)->applyFromArray(
					array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			$Oexcel->getActiveSheet()->setCellValue(chr($aalfabeto).$col, $v[0]);
			
			$aalfabeto++;
		}
        /************************** CABECERA *****************************************/

		/************************** CUERPO *****************************************/
		$x_cant_aux = 0;
		$col++;
		$total_pu= $total_igv=$total_total_c=0;
		
		foreach ($data_detallado as $key => $vv) {
			$aalfabeto=65;
			$x_cant_aux++;
			$vv['item']= $x_cant_aux;
			$vv['subtotal_sunat'] = redondeosunat($vv['subtotal']);
			foreach ($this->array_detalle_more() as $key => $v) {
				if($v[2]=='R')
					$vv[$key] = number_format($vv[$key],$fc,'.','');
				
					$Oexcel->getActiveSheet()->setCellValue(chr($aalfabeto).$col,$vv[$key]);
					$aalfabeto++;					
				
				
			}
			$total_pu 		= $total_pu + $vv['precio_venta_detalle'];
			$total_total_c 	= $total_total_c + $vv['subtotal'];
			$col++;
		}
		/************************** CUERPO *****************************************/
		
		/************************** PIE *****************************************/
		$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle_more())-2).$col, $total_pu);
		$Oexcel->getActiveSheet()->getStyle(chr(65+count($this->array_detalle_more())-2).$col)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
		);
		$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle_more())-1).$col, $total_total_c);
		$Oexcel->getActiveSheet()->getStyle(chr(65+count($this->array_detalle_more())-1).$col)->applyFromArray(
				array('borders' => array(
							'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
							'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
							'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
							'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
						)
			)
		);
		/************************** PIE *****************************************/
		// echo $Oexcel; exit;
		$objWriter->save('php://output');
	}
}
?>