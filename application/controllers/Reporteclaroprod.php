<?php include_once "Controller.php";

class Reporteclaroprod extends Controller {

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
		$sql ="SELECT d.idproducto codigo,
SUBSTRING(producto.descripcion from 1 for 50) nombre,
    COALESCE(sum(d.cantidad * u.cantidad_unidad_min * d.tipo_number), 0) AS stock,
   COALESCE(venta.fn_precio(d.idproducto,d.idsucursal),0) AS precio,
    COALESCE(venta.fn_precio(d.idproducto,d.idsucursal),0) AS precio_sugerido,
    unidad.abreviatura AS unidad,
    d.idsucursal sucursal,
    0  undadpqt 
    
   FROM compra.producto
     LEFT JOIN almacen.detalle_almacen d ON producto.idproducto = d.idproducto AND d.estado = 'A'::bpchar
     LEFT JOIN compra.producto_unidad u ON u.idproducto = d.idproducto AND u.idunidad = d.idunidad
     LEFT JOIN compra.unidad ON unidad.idunidad = producto.idunidad
    -- LEFT JOIN compra.producto_precio_venta e ON d.idproducto = e.idproducto  
      WHERE producto.estado = 'A'::bpchar and d.idalmacen in ('1','3','5','7','8','9') --and d.idproducto = '3' --and idprecio = '5'
      
  GROUP BY d.idproducto, producto.descripcion,    d.idalmacen, unidad.abreviatura, d.idsucursal, unidad.idunidad
  order by  d.idproducto,d.idsucursal
 ";
		
		$query      = $this->db->query($sql);
		 //echo $sql;exit;
		$datos      = $query->result_array();
		$whit_cod=10;
		$whit_nombre=120;
		$whit_stock=15;
		$whit_precio=25;
							

		$cabecera = array('codigo'=> array('COD',$whit_cod)
							,'nombre' => array('NOMBRE',$whit_nombre)
							,'stock' => array('STOCK',$whit_stock)
							,'precio' => array('PRECIO',$whit_precio)
							,'precio_sugerido' => array('PRECIO_SUGE',$whit_precio)
							,'unidad' => array('UNIDAD',$whit_precio)
							,'sucursal' => array('COD_SUCUR',$whit_precio)
							,'undadpqt' => array('UND_X_PQTE',$whit_precio)
						);

		$this->load->library("pdf");		
		$this->load_model(array( "seguridad.empresa","venta.cliente","venta.venta","seguridad.sucursal"));		
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(("REPORTE SISTEMA CLARO PRODUCTOS".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(("REPORTE SISTEMA CLARO PRODUCTOS ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(("REPORTE TOP VENTA CLIENTES "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage('H');
		$this->pdf->SetFont('Arial','',10);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(176,3,date('d/m/Y'),0,0,'R');
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
        //name the worksheet
        /*$this->excel->getActiveSheet()->setTitle('Top Ventas');
        if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$this->excel->getActiveSheet()->setCellValue('B1', 'Top Ventas por Ciente '.$_REQUEST['fechainicio'].' al '.$_REQUEST['fechafin']);
			}
			else{
				$this->excel->getActiveSheet()->setCellValue('B1', 'co Ventas por Ciente');
			}
		}	
		
		if(!empty($_REQUEST['idtipoventa'])){
			$this->excel->getActiveSheet()->getStyle('B2:C2')->getFont()->setSize(10);
			$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B2', 'Tipo Venta: ');
			if($_REQUEST['idtipoventa']= 1){				
				$this->excel->getActiveSheet()->setCellValue('C2', 'Contado');
			}
			else{
				$this->excel->getActiveSheet()->setCellValue('C2', 'Credito');
			}			
		}

		if(!empty($_REQUEST['idsucursal'])){
			$this->excel->getActiveSheet()->getStyle('B3:C3')->getFont()->setSize(10);
			$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B3', 'Sucursal: ');
			switch ($_REQUEST['idsucursal']) {
			    case 1:
			        $this->excel->getActiveSheet()->setCellValue('C3', 'SUMI - TARAPOTO');
			        break;
			    case 2:
			        $this->excel->getActiveSheet()->setCellValue('C3', 'SUMI - PUCALLPA');
			        break;
			    case 3:
			        $this->excel->getActiveSheet()->setCellValue('C3', 'SUMI - IQUITOS');
			        break;
			}
	
		}   */     
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'CODIGO');
        $this->excel->getActiveSheet()->setCellValue('B1', 'NOMBRE');
        $this->excel->getActiveSheet()->setCellValue('C1', 'STOCK');
		$this->excel->getActiveSheet()->setCellValue('D1', 'PRECIO');
		$this->excel->getActiveSheet()->setCellValue('E1', 'PRECIO_SUGERIDO');
		$this->excel->getActiveSheet()->setCellValue('F1', 'UNIDAD');
		$this->excel->getActiveSheet()->setCellValue('G1', 'CODIGO_SUCURSAL');
		$this->excel->getActiveSheet()->setCellValue('H1', 'UNIDAD_X_PAQUETE');
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
		$sql ="SELECT d.idproducto codigo,
SUBSTRING(producto.descripcion from 1 for 50) nombre,
    COALESCE(sum(d.cantidad * u.cantidad_unidad_min * d.tipo_number), 0) AS stock,
   COALESCE(venta.fn_precio(d.idproducto,d.idsucursal),0) AS precio,
    COALESCE(venta.fn_precio(d.idproducto,d.idsucursal),0) AS precio_sugerido,
    unidad.abreviatura AS unidad,
    d.idsucursal sucursal,
    0  undadpqt 
    
   FROM compra.producto
     LEFT JOIN almacen.detalle_almacen d ON producto.idproducto = d.idproducto AND d.estado = 'A'::bpchar
     LEFT JOIN compra.producto_unidad u ON u.idproducto = d.idproducto AND u.idunidad = d.idunidad
     LEFT JOIN compra.unidad ON unidad.idunidad = producto.idunidad
    -- LEFT JOIN compra.producto_precio_venta e ON d.idproducto = e.idproducto  
      WHERE producto.estado = 'A'::bpchar and d.idalmacen in ('1','3','5','7','8','9') --and d.idproducto = '3' --and idprecio = '5'
      
  GROUP BY d.idproducto, producto.descripcion,    d.idalmacen, unidad.abreviatura, d.idsucursal, unidad.idunidad
  order by  d.idproducto,d.idsucursal
  ";
		$rs   = $this->db->query($sql);
        $exceldata="";
		foreach ($rs->result_array() as $row){
        	$exceldata[] = $row;
		}
        //Fill data 
        $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A2');         
        $this->excel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        /*$this->excel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);*/
         
        $filename='ProductosClaro.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
}	