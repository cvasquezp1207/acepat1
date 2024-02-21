<?php
include_once "Controller.php";

class Historial_credito extends Controller {
	public function init_controller() {
		$this->set_title("Historial de creditos");
		$this->set_subtitle("Lista de creditos por cliente");
	}

	public function end_controller() {
		$this->js('form/'.$this->controller.'/index_');
	}

	public function form() {
		$data["controller"] = $this->controller;
		$data["array_head"] = $this->array_head();
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function grilla() {return null;}
	
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
	
	public function condicion_cliente(){
		$post = $_REQUEST;
		$where = " AND idsucursal='{$this->get_var_session('idsucursal')}'";
		$where.= " AND idempresa='{$this->get_var_session('idempresa')}'";
		if(!empty($post['idcliente']))
			$where.=" AND cod={$post['idcliente']}";
		if(!empty($post['cliente']))
			$where.=" AND cliente ILIKE '%{$post['cliente']}%'";
		if(!empty($post['limite_credito']))
			$where.=" AND limite_credito={$post['limite_credito']}";
		if(!empty($post['deuda']))
			$where.=" AND deuda={$post['deuda']}";
		if(!empty($post['disponible']))
			$where.=" AND disponible={$post['disponible']}";
		if(!empty($post['telefono']))
			$where.=" AND telefono ILIKE '{$post['telefono']}%'";
		if(!empty($post['zona']))
			$where.=" AND zona ILIKE '{$post['zona']}%'";
		if(!empty($post['direccion']))
			$where.=" AND direccion ILIKE '{$post['direccion']}%'";
		
		return $where;
	}
	
	public function query_master(){
	/*	$sql = "SELECT 
				DISTINCT c.idcliente cod
				,cliente
				,limite_credito
				,'' deuda
				,'' disponible
				,COALESCE(c.telefono,'') telefono
				,zona
				,c.direccion
				,idzona
				,(limite_credito - COALESCE(monto_deuda,0)) disponible
				,COALESCE(monto_deuda,0.00) deuda
				,c.estado
				,c.idsucursal
				,suc.idempresa
				FROM credito.credito_view c
				JOIN seguridad.sucursal suc ON suc.idsucursal=c.idsucursal
				LEFT JOIN(
					SELECT SUM(monto_credito) monto_deuda,idcliente FROM credito.credito WHERE estado='A' AND pagado='N' GROUP BY idcliente
				) q ON q.idcliente=c.idcliente
				ORDER BY cliente";*/
				
				$sql = "SELECT c.idcliente cod
				,cliente
				,limite_credito
				,COALESCE(c.telefono,'') telefono
				,zona
				,c.direccion
				,idzona
				,limite_credito - SUM(c.monto_credito - COALESCE(monto_pagado,0) - COALESCE(monto_nc,0.00)) disponible
				,SUM(c.monto_credito - COALESCE(monto_pagado,0) - COALESCE(monto_nc,0.00)) deuda
				,c.estado
				,c.idsucursal idsucursal
				,suc.idempresa idempresa
				 				
				FROM credito.credito_view c
				JOIN seguridad.sucursal suc ON suc.idsucursal=c.idsucursal
				
				LEFT JOIN(
					SELECT SUM(monto) monto_pagado,idcredito FROM credito.amortizacion 
					WHERE estado='A' AND idnotacredito IS NULL GROUP BY idcredito
				) a ON a.idcredito=c.idcredito

				LEFT JOIN(
					SELECT SUM(monto) monto_nc,idcredito FROM credito.amortizacion WHERE estado='A' AND idnotacredito IS NOT NULL GROUP BY idcredito
				) n ON n.idcredito=c.idcredito
				
				group by c.idcliente
				,cliente
				,limite_credito
				,c.telefono
				,zona
				,c.direccion
				,idzona
				,c.estado
				,c.idsucursal
				,suc.idempresa
				ORDER BY cliente ";
				
				
		return $sql;
	}
	
	public function get_clientes($server=false){
		$sql = "SELECT * FROM ({$this->query_master()} ) qq
				WHERE estado='A'
				{$this->condicion_cliente()} ";
		// echo $sql;
		$q=$this->db->query($sql);
		
		if(!$server)
			$this->response($q->result_array());
		else return $q->row_array();
	}
	
	public function query_credito(){
		$sql = "SELECT comprobante
				,nombre_vendedor
				,to_char(fecha_credito,'DD/MM/YYYY') fecha_emision
				,COALESCE(to_char(fecha_venc,'DD/MM/YYYY'),'') fecha_vencimiento
				,m_corto moneda
				--,COALESCE(fecha_pago-fecha_venc,COALESCE(CURRENT_DATE-fecha_venc,0)) dias
				--,COALESCE(fecha_pago-fecha_venc,COALESCE(CURRENT_DATE-fecha_venc,0)) dias
				--,CASE WHEN fecha_pago>fecha_venc THEN fecha_pago-fecha_venc ELSE CURRENT_DATE-fecha_venc END dias
				--,CASE WHEN fecha_pago>fecha_venc THEN CURRENT_DATE - fecha_pago ELSE CURRENT_DATE-fecha_venc END dias
				,CASE WHEN c.pagado='N' THEN CURRENT_DATE-fecha_venc ELSE COALESCE(fecha_pago-fecha_venc,0) END dias
				,monto_credito importe
				,COALESCE(mora,0.00) mora
				,COALESCE(monto_pagado,0) total_pagado
				,COALESCE(monto_nc,0.00) total_nc
				,COALESCE(monto_pagado,0) + COALESCE(monto_nc,0.00) total_pagos
				,c.monto_credito - COALESCE(monto_pagado,0) - COALESCE(monto_nc,0.00) saldo
				,date(fecha_credito) fecha_credito
				,c.idsucursal
				,c.idcliente
				,c.idcredito
				,c.idventa
				,c.estado
				,c.pagado
				,to_char(fecha_pago,'DD/MM/YYYY') fecha_pago
				,suc.idempresa
				FROM credito.credito_view c
				JOIN seguridad.sucursal suc ON suc.idsucursal=c.idsucursal
				LEFT JOIN(
					SELECT MIN(fecha_vencimiento) fecha_venc,SUM(mora) mora,SUM(monto_letra) saldo,idcredito FROM credito.letra WHERE estado='A' GROUP BY idcredito
				) l ON l.idcredito=c.idcredito
				LEFT JOIN(
					SELECT SUM(monto) monto_pagado,idcredito FROM credito.amortizacion WHERE estado='A' AND idnotacredito IS NULL GROUP BY idcredito
				) a ON a.idcredito=c.idcredito

				LEFT JOIN(
					SELECT SUM(monto) monto_nc,idcredito FROM credito.amortizacion WHERE estado='A' AND idnotacredito IS NOT NULL GROUP BY idcredito
				) n ON n.idcredito=c.idcredito
				LEFT JOIN(
					SELECT MAX(fecha_pago) fecha_pago,idcredito FROM credito.amortizacion WHERE estado='A' GROUP BY idcredito
				) p ON p.idcredito=c.idcredito
				ORDER BY cliente,fecha_emision";
		return $sql;
	}
	
	public function condicion_credito(){
		$post = $_REQUEST;
		$where = " AND idempresa='{$this->get_var_session('idempresa')}'";
		
		if(!empty($post['idcliente']))
			$where.=" AND idcliente='{$post['idcliente']}' ";
		if($post['pagado']!='T')
			$where.=" AND pagado='{$post['pagado']}' ";
		
		return $where;
	}
	
	public function get_creditos($server=false){
		$sql = "SELECT * FROM ({$this->query_credito()} ) qq
				WHERE estado='A' 
				{$this->condicion_credito()}";
		// echo $sql;exit;
		$q=$this->db->query($sql);
		if(!$server)
			$this->response($q->result_array());
		else
			return $q->result_array();
	}
	
	public function array_head(){
		return array('comprobante'=>array("Documento",'','8')
					,'nombre_vendedor'=>array("Vendedor",'','14')
					,'fecha_emision'=>array("Fecha emision",'','8')
					,'fecha_vencimiento'=>array("Fecha Vto",'','8')
					,'fecha_pago'=>array("U pago",'','8')
					,'moneda'=>array("Moneda",'','4')
					,'dias'=>array("Dias",'','4')
					,'importe'=>array("Importe",'','6')
					,'total_nc'=>array("Total NC",'','6')
					,'mora'=>array("Mora",'','6')
					,'total_pagos'=>array("Pagos",'','6')
					,'saldo'=>array("Saldo",'','8')
		);
	}
	
	public function exportar(){
		set_time_limit(0);
		$data		= $this->get_creditos(true);
		$cliente	= $this->get_clientes(true);
		
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"HISTORIAL DE CREDITO",true);
		
		$alfabeto = 65;
		$col=9;
		
		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, ($cliente['cliente']));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
		$col++;
		
		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'DIRECCION : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, ($cliente['direccion']));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
		$col++;
		
		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'ZONA : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, ($cliente['zona']));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
		$col++;
		
		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TELEFONO : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, ($cliente['telefono']));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':C'.$col);
		$col++;
		
		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'LINEA CREDITO : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, ($cliente['limite_credito']));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':C'.$col);
		$col++;
		
		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'DEUDA : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, ($cliente['deuda']));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':C'.$col);
		$col++;
		
		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'DISPONIBLE : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, ($cliente['disponible']));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':C'.$col);
		$col++;
		/************************** CABECERA *****************************************/
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
		/************************** CABECERA *****************************************/
		
		
		
		/************************** CUERPO *****************************************/
		$x_cant_aux = 0;
		$aalfabeto=65;
		$col++;
		$total_importe = $total_nc = $total_pagos = $total_saldo = $cant_creditos = 0;
		foreach ($data as $key => $vv) {
			$aalfabeto=65;
			foreach ($this->array_head() as $key => $v) {
				$Oexcel->getActiveSheet()->setCellValue(chr($aalfabeto).$col, $vv[$key]);
				$aalfabeto++;
			}
			$col++;
			$cant_creditos++;
			
			$total_importe	= $total_importe + $vv['importe'];
			$total_nc		= $total_nc + $vv['total_nc'];
			$total_pagos	= $total_pagos + $vv['total_pagos'];
			$total_saldo	= $total_saldo + $vv['saldo'];
		}
		/************************** CUERPO *****************************************/
		
		/************************** PIE *****************************************/
		$Oexcel->getActiveSheet()->setCellValue(chr(66).$col, $cant_creditos." CREDITOS");
		$Oexcel->getActiveSheet()->setCellValue(chr(72).$col, $total_importe);
		$Oexcel->getActiveSheet()->setCellValue(chr(73).$col, $total_nc);
		$Oexcel->getActiveSheet()->setCellValue(chr(75).$col, $total_pagos);
		$Oexcel->getActiveSheet()->setCellValue(chr(76).$col, $total_saldo);
		
		$Oexcel->getActiveSheet()->getStyle(chr(66).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(72).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(73).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(75).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(76).$col)->getFont()->setBold(true);
		/************************** PIE *****************************************/
		
		for($i=65;$i<=$aalfabeto;$i++){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
		}
		
		$filename='historial_credito'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');
		
		$objWriter->save('php://output');
	}
}
?>