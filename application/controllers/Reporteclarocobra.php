<?php include_once "Controller.php";

class Reporteclarocobra extends Controller {

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

	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$this->load->library('combobox');
		$data["controller"] = $this->controller;
		$data["sucursal"] = $this->listsucursal();
		$data['idperfil'] = $this->get_var_session("idperfil");
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idcliente");
		$this->combobox->setAttr("name","idcliente");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select("idcliente,cliente");
		$query = $this->db->order_by("cliente")->get("venta.cliente_venta_view");
		//var_dump($query); exit;
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());		
		$data['cliente'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/		
		$this->combobox->init();
		$this->combobox->setAttr("id","idtipoventa");
		$this->combobox->setAttr("name","idtipoventa");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idtipoventa,descripcion');
		$query = $this->db->where("estado","A")->where("mostrar_en_venta","S")->order_by("descripcion")->get("venta.tipo_venta");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['tipopago'] = $this->combobox->getObject();
		// $idperfil = 4; // id del perfil vendedor, tal vez deberia ser contante
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		/*$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);*/
		
		$data["sucursal"] = $this->listsucursal();
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");

		return $this->load->view($this->controller."/form", $data, true);
	}

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

	public function seleccion($datos,$id,$key){
		$data = array();
		foreach($datos as $kk=>$vv){
			if($vv[$key]==$id){
				$data[]=$vv;
			}
		}	
		return $data;
	}
	
	public function filtro_reporte($post,$and_where=''){
		$where = "WHERE cliente.idcliente = venta.idcliente and venta.estado = 'A'";
		//$where.= " AND idempresa='{$this->get_var_session('idempresa')}'";
		if(!empty($_REQUEST['idtipoventa'])){
			$where.=" AND venta.idtipoventa='{$post['idtipoventa']}' ";
		}

		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND venta.fecha_venta BETWEEN '{$post['fechainicio']}' AND '{$post['fechafin']}'";
			}
		}
		
		if(!empty($_REQUEST['idsucursal']))
			$where.=" AND venta.idsucursal='{$post['idsucursal']}' ";

		$where.=$and_where;
		return $where;
		
	}

	public function filtro_limite($post,$and_limit=''){
		$limit = "";
		if(!empty($_REQUEST['limite'])){
			$limit.="LIMIT '{$post['limite']}'";
		}

		$limit.=$and_limit;
		return $limit;
		
	}

	public function imprimir(){
		$sql ="SELECT cr.nro_credito nrocredito,
		v.idsucursal sucursal,
		v.idcliente cliente,
		idvendedor vendedor,
		case  when v.idtipodocumento = 1 or v.idtipodocumento = 14  then 'FAC' else 'BOL' END serie, 
		COALESCE(total_amortizacion,0.00) pago ,
		cr.monto_credito - COALESCE(total_amortizacion,0.00) saldo ,
		to_char(MIN(l.fecha_vencimiento),'YYYY-MM-DD') fecha_vencimiento , 
		to_char(cr.fecha_credito, 'YYYY-MM-DD') fecha_creditos , 
		v.comprobante credito 
		FROM credito.credito cr 
		JOIN venta.cliente_view c ON c.idcliente=cr.idcliente 
		JOIN venta.venta_view v ON v.idventa=cr.idventa 
		JOIN seguridad.view_sucursal suc ON suc.idsucursal=v.idsucursal 
		JOIN credito.letra l ON l.idcredito=cr.idcredito 
		AND l.estado<>'I' 
		LEFT JOIN( SELECT SUM(am.monto) total_amortizacion,am.idletra,am.idcredito,MAX(am.fecha_pago) fecha_pago,SUM(mora) moras_amrt 
		FROM credito.amortizacion am 
		WHERE am.estado<>'I' 
		GROUP BY am.idletra,am.idcredito ) amrt ON amrt.idletra=l.idletra 
		AND amrt.idcredito=cr.idcredito LEFT JOIN ( SELECT h.idventa ,h.idzona ,h.idsucursal ,u.user_nombres cobrador FROM cobranza.hoja_ruta h JOIN seguridad.view_usuario u ON u.idusuario=h.idcobrador WHERE h.estado='A' ) q ON q.idzona=c.idzona 
		AND q.idventa=v.idventa 
		AND q.idsucursal=v.idsucursal 
		WHERE cr.estado<>'I'  AND cr.pagado='N' 
		GROUP BY idvendedor, fecha_credito,v.idtipodocumento,credito, cliente,nro_letras,monto_credito, c.zona, c.limite_credito, v.comprobante ,v.vendedor,v.idventa,v.idsucursal,v.idcliente,  c.idzona ,fecha_pago ,v.idventa ,total_amortizacion,moras_amrt, cr.nro_credito 
		ORDER BY cliente , fecha_credito ASC
";		
		$query      = $this->db->query($sql);
		 //echo $sql;exit;
		$datos      = $query->result_array();
		$whit_cod=12;
		
		$whit_nombre=120;
		$whit_stock=15;
		$whit_precio=25;
							
									

		$cabecera = array('nrocredito'=> array('COD',$whit_stock)
							,'sucursal' => array('SUCUR',$whit_stock)
							,'cliente' => array('COD_CLI',$whit_stock)
							,'vendedor' => array('COD_USU',$whit_stock)
							,'serie' => array('TDOC',$whit_cod)
							,'pago' => array('MONTO',$whit_precio)
							,'saldo' => array('DEUDA',$whit_precio)
							,'fecha_vencimiento' => array('FECH_VENC',$whit_precio)
							,'fecha_creditos' => array('FECH_EMIS',$whit_precio)
							,'credito' => array('SERIE',$whit_precio)
						);

		$this->load->library("pdf");		
		$this->load_model(array( "seguridad.empresa","venta.cliente","venta.venta","seguridad.sucursal"));		
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(("REPORTE SISTEMA CLARO COBRANZAS ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(("REPORTE SISTEMA CLARO COBRANZAS ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(("REPORTE SISTEMA CLARO COBRANZAS "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage('P');
		$this->pdf->SetFont('Arial','',10);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(100,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);

		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->sucursal->find($_REQUEST['idsucursal']);
			$this->pdf->Cell(5,3,$this->sucursal->get("descripcion"),0,1,'L');
		}
			
		/*if(!empty($_REQUEST['idtipoventa'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO OPERACION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$this->pdf->Cell(5,3,$this->tipo_venta->get("descripcion"),0,1,'L');
		}*/
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,0,'C',true);
			$total_lienzo = $total_lienzo + $val[1];
		}
		//$this->pdf->Cell($whit_total_m,5,"TOTAL",1,1,'C');
		$this->pdf->Cell($total_lienzo,5,"",0,0,'C');
		$this->pdf->Ln(10); 
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		/************************** BODY *****************************************/
		foreach ($datos as $key => $val) {
			foreach ($cabecera as $k => $v) {
				if(isset($v[2])){
					$this->pdf->Cell($v[1],5,$val[$k],1,0,$v[2]);
				}else{
					$this->pdf->Cell($v[1],5,$val[$k],1,0);
				}
			}
			
			$this->pdf->Ln(); 
		}
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		$this->pdf->SetFont('Arial','B',8);
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
	
	public function exportarExcel()
    {
        $this->load->library("excel");
        $this->excel->setActiveSheetIndex(0);
        									

        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'CODIGO');
        $this->excel->getActiveSheet()->setCellValue('B1', 'CODIGO_SUCURSAL');
        $this->excel->getActiveSheet()->setCellValue('C1', 'CODIGO_CLIENTE');
		$this->excel->getActiveSheet()->setCellValue('D1', 'CODIGO_USUARIO');
		$this->excel->getActiveSheet()->setCellValue('E1', 'TIPO_DOCUMENTO');
		$this->excel->getActiveSheet()->setCellValue('F1', 'MONTO_PAGADO');
		$this->excel->getActiveSheet()->setCellValue('G1', 'DEUDA_TOTAL');
		$this->excel->getActiveSheet()->setCellValue('H1', 'FECHA_VENCIMIENTO');
		$this->excel->getActiveSheet()->setCellValue('I1', 'FECHA_EMISION');
		$this->excel->getActiveSheet()->setCellValue('J1', 'SERIE');
        //merge cell A1 until C1
        //$this->excel->getActiveSheet()->mergeCells('B1:D1');
        //set aligment to center for that merged cell (A1 to C1) ALINEACION DE LAS CABECERAS DE LAS CELDAS
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$this->excel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$this->excel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        // Set column widths Tamaño de las columnas
		//$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);   
		$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);		
		$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        //make the font become bold        
        //$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12);
        //$this->excel->getActiveSheet()->getStyle('B1')->getFill()->getStartColor()->setARGB('#333');

        //$this->excel->getActiveSheet()->getStyle('B1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		//$this->excel->getActiveSheet()->getStyle('B1:D1')->getFill()->getStartColor()->setARGB('FFA0A0A0'); //FF808080
		//$this->excel->getActiveSheet()->getStyle('B3:D3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		//$this->excel->getActiveSheet()->getStyle('B4:D4')->getFill()->getStartColor()->setARGB('FF993300');

		//$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		//$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		//$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);		
		// Merge cells
		/*$this->excel->getActiveSheet()->mergeCells('B1:D3');
		$this->excel->getActiveSheet()->mergeCells('B2:D3');
		$this->excel->getActiveSheet()->unmergeCells('B2:D3');*/	
		// Set style for header row using alternative method
		//echo date('H:i:s') , " Set style for header row using alternative method" , EOL;	
		for($col = ord('B'); $col <= ord('D'); $col++){ //set column dimension $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
        //change the font size
        $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(10);         
        //$this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		}
        //retrive contries table data
		/*$sql ="SELECT cr.nro_credito,
		v.idsucursal,
		v.idcliente ,
		idvendedor,
		substring(v.comprobante from 1 for 2) serie, 
		COALESCE(total_amortizacion,0.00) pago ,
		cr.monto_credito - COALESCE(total_amortizacion,0.00) saldo ,
		to_char(MIN(l.fecha_vencimiento),'DD/MM/YYYY') fecha_vencimiento , 
		to_char(cr.fecha_credito, 'DD/MM/YYYY') fecha_creditos , 
		v.comprobante credito 
		FROM credito.credito cr 
		JOIN venta.cliente_view c ON c.idcliente=cr.idcliente 
		JOIN venta.venta_view v ON v.idventa=cr.idventa 
		JOIN seguridad.view_sucursal suc ON suc.idsucursal=v.idsucursal 
		JOIN credito.letra l ON l.idcredito=cr.idcredito 
		AND l.estado<>'I' 
		LEFT JOIN( SELECT SUM(am.monto) total_amortizacion,am.idletra,am.idcredito,MAX(am.fecha_pago) fecha_pago,SUM(mora) moras_amrt 
		FROM credito.amortizacion am 
		WHERE am.estado<>'I' 
		GROUP BY am.idletra,am.idcredito ) amrt ON amrt.idletra=l.idletra 
		AND amrt.idcredito=cr.idcredito LEFT JOIN ( SELECT h.idventa ,h.idzona ,h.idsucursal ,u.user_nombres cobrador FROM cobranza.hoja_ruta h JOIN seguridad.view_usuario u ON u.idusuario=h.idcobrador WHERE h.estado='A' ) q ON q.idzona=c.idzona 
		AND q.idventa=v.idventa 
		AND q.idsucursal=v.idsucursal 
		WHERE cr.estado<>'I'  AND cr.pagado='N' 
		GROUP BY idvendedor, fecha_credito,serie,credito, cliente,nro_letras,monto_credito, c.zona, c.limite_credito, v.comprobante ,v.vendedor,v.idventa,v.idsucursal,v.idcliente,  c.idzona ,fecha_pago ,v.idventa ,total_amortizacion,moras_amrt, cr.nro_credito ORDER BY cliente , fecha_credito ASC";
		*/
		$sql ="SELECT cr.nro_credito nrocredito,
		v.idsucursal sucursal,
		v.idcliente cliente,
		idvendedor vendedor,
		case  when v.idtipodocumento = 1 or v.idtipodocumento = 14  then 'FAC' else 'BOL' END serie, 
		COALESCE(total_amortizacion,0.00) pago ,
		cr.monto_credito - COALESCE(total_amortizacion,0.00) saldo ,
		to_char(MIN(l.fecha_vencimiento),'YYYY-MM-DD') fecha_vencimiento , 
		to_char(cr.fecha_credito, 'YYYY-MM-DD') fecha_creditos , 
		v.comprobante credito 
		FROM credito.credito cr 
		JOIN venta.cliente_view c ON c.idcliente=cr.idcliente 
		JOIN venta.venta_view v ON v.idventa=cr.idventa 
		JOIN seguridad.view_sucursal suc ON suc.idsucursal=v.idsucursal 
		JOIN credito.letra l ON l.idcredito=cr.idcredito 
		AND l.estado<>'I' 
		LEFT JOIN( SELECT SUM(am.monto) total_amortizacion,am.idletra,am.idcredito,MAX(am.fecha_pago) fecha_pago,SUM(mora) moras_amrt 
		FROM credito.amortizacion am 
		WHERE am.estado<>'I' 
		GROUP BY am.idletra,am.idcredito ) amrt ON amrt.idletra=l.idletra 
		AND amrt.idcredito=cr.idcredito LEFT JOIN ( SELECT h.idventa ,h.idzona ,h.idsucursal ,u.user_nombres cobrador FROM cobranza.hoja_ruta h JOIN seguridad.view_usuario u ON u.idusuario=h.idcobrador WHERE h.estado='A' ) q ON q.idzona=c.idzona 
		AND q.idventa=v.idventa 
		AND q.idsucursal=v.idsucursal 
		WHERE cr.estado<>'I'  AND cr.pagado='N' 
		GROUP BY idvendedor, fecha_credito,v.idtipodocumento,credito, cliente,nro_letras,monto_credito, c.zona, c.limite_credito, v.comprobante ,v.vendedor,v.idventa,v.idsucursal,v.idcliente,  c.idzona ,fecha_pago ,v.idventa ,total_amortizacion,moras_amrt, cr.nro_credito 
		ORDER BY cliente , fecha_credito ASC";
		$rs   = $this->db->query($sql);
        $exceldata="";
		foreach ($rs->result_array() as $row){
        	$exceldata[] = $row;
		}
        //Fill data 
        $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A2');         
        $this->excel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        /*$this->excel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);*/
         
        $filename='CobranzaClaro.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
}	