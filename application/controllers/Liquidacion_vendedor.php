<?php
include_once "Controller.php";

class Liquidacion_vendedor extends Controller {
	public function init_controller() {
		$this->set_title("Cobranza por vendedor");
		// $this->set_subtitle("");
	}

	public function end_controller() {
		$this->js('form/'.$this->controller.'/index_');
	}

	public function form() {
		$data["controller"] = $this->controller;
		$data["array_head"] = $this->array_head();
		$data["head_resumen"] = $this->array_head_resumen();
		
		$this->load->library('combobox');
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idvendedor","name"=>"idvendedor","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($datos);
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
	
	public function condicion_vendedor(){
		$post = $_REQUEST;
		$where = " AND idtipoventa='2'";//Venta al credito
		$where.= " AND idsucursal='{$this->get_var_session('idsucursal')}'";
		$where.= " AND fila_afectada='S' ";
		if(!empty($post['idvendedor']))
			$where.=" AND idvendedor={$post['idvendedor']}";
		if(!empty($post['comprobante']))
			$where.=" AND comprobante ILIKE '{$post['comprobante']}%'";
		if(!empty($post['ruc']))
			$where.=" AND ruc ILIKE '{$post['ruc']}%'";
		if(!empty($post['dni']))
			$where.=" AND dni ILIKE '{$post['dni']}%'";
		if(!empty($post['idmoneda']))
			$where.=" AND idmoneda={$post['idmoneda']}";
		if(!empty($post['cliente']))
			$where.=" AND nombre_cliente ILIKE '{$post['cliente']}%'";
		// if(!empty($post['fecha_inicio'])){
			// if(!empty($post['fecha_fin']))
				// $where.=" AND fecha_venta>='{$post['fecha_inicio']}' AND fecha_venta<='{$post['fecha_fin']}'";
			// else
				// $where.=" AND fecha_venta='{$post['fecha_inicio']}' ";
		// }

		return $where;
	}
	
	public function query_master(){
		/*Filtro para el calculo de las amortizaciones*/
		$filter_date = "";
		if(!empty($_REQUEST['fecha_inicio'])){
			if(!empty($_REQUEST['fecha_fin']))
				$filter_date.=" AND a.fecha_pago>='{$_REQUEST['fecha_inicio']}' AND a.fecha_pago<='{$_REQUEST['fecha_fin']}'";
			else
				$filter_date.=" AND a.fecha_pago='{$_REQUEST['fecha_inicio']}' ";
		}
		/*Filtro para el calculo de las amortizaciones*/
		$sql = "SELECT
				v.idventa
				,comprobante
				,fecha_venta_format f_venta
				,full_nombres nombre_cliente
				,direccion
				,zona
				,total importe_venta
				,COALESCE(monto_pagado,0) importe_cobrado
				,total - COALESCE(monto_pagado,0) saldo
				,moneda_corto
				--,array_to_string(array_agg(q.cobrador),',') cobradores
				,v.vendedor
				,v.idsucursal
				,v.idvendedor
				,v.idtipoventa
				,v.idcliente
				,v.idtipodocumento
				,v.idmoneda
				,v.fecha_venta
				,v.estado
				,v.ruc
				,v.dni
				,to_char(ultimo_pago,'DD/MM/YYYY') ultimo_pago
				,CASE WHEN p.idventa IS NOT NULL THEN 'S' ELSE 'N' END fila_afectada
				FROM venta.venta_view v
				LEFT JOIN(
					SELECT SUM(a.monto)+SUM(a.mora) monto_pagado,a.idcredito,c.idventa,MAX(a.fecha_pago) ultimo_pago FROM credito.amortizacion a 
					JOIN credito.credito c ON c.idcredito=a.idcredito AND c.estado='A'
					WHERE a.estado='A' {$filter_date}
					GROUP BY a.idcredito,c.idventa
				) p ON p.idventa=v.idventa
				/*
				LEFT JOIN (
					SELECT 
					h.idventa
					,h.idzona 
					,h.idsucursal
					,u.user_nombres cobrador
					FROM cobranza.hoja_ruta h
					JOIN seguridad.view_usuario u ON u.idusuario=h.idcobrador
					WHERE h.estado='A'

				) q ON q.idzona=v.idzona AND q.idventa=v.idventa AND q.idsucursal=v.idsucursal

				GROUP BY v.idventa,v.comprobante,f_venta,nombre_cliente, v.direccion,v.zona,importe_venta, moneda_corto,v.vendedor
				,v.idsucursal
				,v.idvendedor
				,v.idtipoventa
				,v.idcliente
				,v.idtipodocumento
				,importe_cobrado
				,v.fecha_venta
				,v.ruc
				,v.dni
				*/";
		return $sql;
	}
	
	public function get_vendedor($server=false){
		$sql = "SELECT idvendedor cod
				, vendedor
				,COALESCE(SUM(importe_venta),0) cuota_cobranza
				,COALESCE(SUM(importe_cobrado),0) monto_cobrado
				,CAST(COALESCE(SUM(importe_cobrado),0)*100/COALESCE(SUM(importe_venta),0) AS numeric(10,2)) avance
				FROM ({$this->query_master()} ) qq
				WHERE estado='A'
				{$this->condicion_vendedor()} ";
		$sql.=" GROUP BY idvendedor,vendedor";
		$sql.=" ORDER BY vendedor";
		// echo $sql;exit;
		$q=$this->db->query($sql);
		
		if(!$server)
			$this->response($q->result_array());
		else return $q->result_array();
	}
	
	public function query_credito(){
		$sql = "SELECT idventa
				,f_venta fecha_venta
				,comprobante
				,nombre_cliente cliente
				,ruc
				,dni
				,moneda_corto moneda
				,importe_venta
				,importe_cobrado
				,saldo
				FROM ({$this->query_master()} ) qq
				WHERE estado='A'
				{$this->condicion_vendedor()}
				ORDER BY f_venta,cliente";
		return $sql;
	}
	
	public function get_creditos($server=false){
		$sql = "SELECT
				idventa
				,f_venta fecha_venta
				,ultimo_pago
				,comprobante
				,nombre_cliente cliente
				,COALESCE(ruc,'') ruc
				,COALESCE(dni,'') dni
				,moneda_corto moneda
				,importe_venta
				,importe_cobrado
				,saldo
				FROM ({$this->query_master()} ) qq
				WHERE estado='A' 
				{$this->condicion_vendedor()}";
		// echo $sql;exit;
		$q=$this->db->query($sql);
		
		if(!$server)
			$this->response($q->result_array());
		else
			return $q->result_array();
	}
	
	public function array_head_resumen(){
		return array('cod'=>array("Código",'','8',false)
					,'vendedor'=>array("Vendedor",'','40',false)
					,'cuota_cobranza'=>array("Cuota Cobranza",'','15',false)
					,'monto_cobrado'=>array("Monto Cobrado",'','15',false)
					,'avance'=>array("% Avance",'','15',false)
		);
	}
	
	public function array_head(){
		return array('fecha_venta'=>array("Fecha",'','4',false)
					,'ultimo_pago'=>array("U Pago",'','4',false)
					,'comprobante'=>array("Documento",'','9',false)
					,'cliente'=>array("Cliente",'','30',true)
					,'ruc'=>array("RUC",'','6',true)
					,'dni'=>array("DNI",'','6',true)
					,'moneda'=>array("Moneda",'','4',false)
					,'importe_venta'=>array("Imp. DOC",'','7',false)
					,'importe_cobrado'=>array("M. cobrado",'','7',false)
					,'saldo'=>array("Saldo",'','6',false)
					// ,'cobrador'=>array("Cobrador",'','8')
		);
	}
	
	public function exportar_head(){
		set_time_limit(0);
		
		$data		= $this->get_vendedor(true);
		$head		= $this->array_head_resumen();
		
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"COBRANZA POR VENDEDOR",true);
		
		$alfabeto = 65;
		$col=9;
		
		$this->load_model("seguridad.sucursal");
		$this->sucursal->find(array("idsucursal"=>$this->get_var_session("idsucursal")));

		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SUCURSAL : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, $this->sucursal->get("descripcion"));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
		$col++;
		
		
		if(!empty($_REQUEST['idvendedor'])){
			$this->load_model("seguridad.view_usuario");
			$this->view_usuario->find(array("idusuario"=>$_REQUEST['idvendedor']));
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENDEDOR : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, $this->view_usuario->get("full_nombres"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$this->load_model("general.moneda");
			$this->moneda->find(array("idmoneda"=>$_REQUEST['idmoneda']));
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MONEDA : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, $this->moneda->get("descripcion"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['fecha_inicio'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENTA DE : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, fecha_es($_REQUEST['fecha_inicio']));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['fecha_fin'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENTA HASTA : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, fecha_es($_REQUEST['fecha_fin']));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		/*++++++++++++++++++++++++++++++++++++++++++ HEAD ++++++++++++++++++++++++++++++++++++++++++*/
		foreach($head as $key=>$v){
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
		/*++++++++++++++++++++++++++++++++++++++++++ HEAD ++++++++++++++++++++++++++++++++++++++++++*/
		
		
		/*++++++++++++++++++++++++++++++++++++++++++ BODY ++++++++++++++++++++++++++++++++++++++++++*/
		$aalfabeto=65;
		$total_cobranza = $total_cobrado = 0;
		$col++;
		foreach($data as $key => $vv) {
			$aalfabeto=65;
			foreach ($head as $key => $v) {
				$Oexcel->getActiveSheet()->setCellValue(chr($aalfabeto).$col, $vv[$key]);
				$aalfabeto++;
			}
			$col++;
			$total_cobranza+= $vv["cuota_cobranza"];
			$total_cobrado+= $vv["monto_cobrado"];
		}
		/*++++++++++++++++++++++++++++++++++++++++++ BODY ++++++++++++++++++++++++++++++++++++++++++*/
		
		
		/*++++++++++++++++++++++++++++++++++++++++++ FOOT ++++++++++++++++++++++++++++++++++++++++++*/
		$Oexcel->getActiveSheet()->getStyle(chr(67).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(67).$col)->applyFromArray(
					array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
		$Oexcel->getActiveSheet()->setCellValue(chr(67).$col, number_format($total_cobranza,2,'.',''));
		
		$Oexcel->getActiveSheet()->getStyle(chr(68).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(68).$col)->applyFromArray(
					array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
		$Oexcel->getActiveSheet()->setCellValue(chr(68).$col, number_format($total_cobrado,2,'.',''));
		/*++++++++++++++++++++++++++++++++++++++++++ FOOT ++++++++++++++++++++++++++++++++++++++++++*/
		for($i=65;$i<=$aalfabeto;$i++){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
		}
		
		$filename='liquidacion_vendedor'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');
		
		$objWriter->save('php://output');
	}
	
	public function exportar_ventas(){
		set_time_limit(0);
		
		$data		= $this->get_creditos(true);
		$head		= $this->array_head();
		
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"COBRANZA POR VENDEDOR",true);
		
		$alfabeto = 65;
		$col=9;
		
		$this->load_model("seguridad.sucursal");
		$this->sucursal->find(array("idsucursal"=>$this->get_var_session("idsucursal")));

		$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SUCURSAL : ');
		$Oexcel->getActiveSheet()->setCellValue('B'.$col, $this->sucursal->get("descripcion"));
		$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
		$col++;
		
		
		if(!empty($_REQUEST['idvendedor'])){
			$this->load_model("seguridad.view_usuario");
			$this->view_usuario->find(array("idusuario"=>$_REQUEST['idvendedor']));
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENDEDOR : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, $this->view_usuario->get("full_nombres"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$this->load_model("general.moneda");
			$this->moneda->find(array("idmoneda"=>$_REQUEST['idmoneda']));
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MONEDA : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, $this->moneda->get("descripcion"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['fecha_inicio'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENTA DE : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, fecha_es($_REQUEST['fecha_inicio']));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['fecha_fin'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENTA HASTA : ');
			$Oexcel->getActiveSheet()->setCellValue('B'.$col, fecha_es($_REQUEST['fecha_fin']));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('B'.$col.':J'.$col);
			$col++;
		}
		
		/*++++++++++++++++++++++++++++++++++++++++++ HEAD ++++++++++++++++++++++++++++++++++++++++++*/
		foreach($head as $key=>$v){
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
		/*++++++++++++++++++++++++++++++++++++++++++ HEAD ++++++++++++++++++++++++++++++++++++++++++*/
		
		
		/*++++++++++++++++++++++++++++++++++++++++++ BODY ++++++++++++++++++++++++++++++++++++++++++*/
		$aalfabeto=65;
		$total_cobranza = $total_cobrado = 0;
		$col++;
		foreach($data as $key => $vv) {
			$aalfabeto=65;
			foreach ($head as $key => $v) {
				$Oexcel->getActiveSheet()->setCellValue(chr($aalfabeto).$col, $vv[$key]);
				$aalfabeto++;
			}
			$col++;
			$total_cobranza+= $vv["importe_venta"];
			$total_cobrado+= $vv["importe_cobrado"];
		}
		/*++++++++++++++++++++++++++++++++++++++++++ BODY ++++++++++++++++++++++++++++++++++++++++++*/
		
		
		/*++++++++++++++++++++++++++++++++++++++++++ FOOT ++++++++++++++++++++++++++++++++++++++++++*/
		$Oexcel->getActiveSheet()->getStyle(chr(71).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(71).$col)->applyFromArray(
					array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
		$Oexcel->getActiveSheet()->setCellValue(chr(71).$col, number_format($total_cobranza,2,'.',''));
		
		$Oexcel->getActiveSheet()->getStyle(chr(72).$col)->getFont()->setBold(true);
		$Oexcel->getActiveSheet()->getStyle(chr(72).$col)->applyFromArray(
					array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
		$Oexcel->getActiveSheet()->setCellValue(chr(72).$col, number_format($total_cobrado,2,'.',''));
		/*++++++++++++++++++++++++++++++++++++++++++ FOOT ++++++++++++++++++++++++++++++++++++++++++*/
		for($i=65;$i<=$aalfabeto;$i++){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
		}
		
		$filename='liquidacion_ventas'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');
		
		$objWriter->save('php://output');
	}
}
?>