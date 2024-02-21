<?php
include_once "Controller.php";

class Reporte_deudaproveedor extends Controller {
	public function init_controller() {
		// $this->set_title("Reporte cuentas a proveedor");
	}
	
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$this->load->library('combobox');

		$data["controller"]		= $this->controller;
		$data["sucursal"]		= $this->listsucursal();
		$data["all_sucursal"]	= $this->get_var_session("control_reporte")? $this->get_var_session("control_reporte"):'N';
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idproveedor");
		$this->combobox->setAttr("name","idproveedor");
		$this->combobox->setAttr("class","chosen-select form-control input-xs");
		// $this->combobox->setAttr("required","");
		$this->db->select("idproveedor,proveedor");
		$query = $this->db->order_by("proveedor")->get("compra.proveedores_deuda_view");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['proveedor'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idtipodocumento");
		$this->combobox->setAttr("name","idtipodocumento");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idtipodocumento,descripcion');
		$query = $this->db->where("mostrar_en_compra","S")->where("estado","A")->order_by("descripcion")->get("venta.tipo_documento");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data['comprobante'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idmoneda,descripcion');
		$query = $this->db->where("estado","A")->order_by("idmoneda")->get("general.moneda");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data['moneda'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		$this->css("plugins/datapicker/datepicker3");
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js("plugins/chosen/chosen.jquery");

		return $this->load->view($this->controller."/form", $data, true);
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
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return $this->form();
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
	
	public function query_master(){
		$sql = "SELECT*,to_char(fecha_deuda,'DD/MM/YYYY') fechadeuda,CASE WHEN pagado='S' THEN 'SI' ELSE 'NO' END cancelado FROM compra.deuda_view";
		return $sql;
	}
	
	public function dataresumido(){
		$sql = "SELECT * FROM ({$this->query_master()}) b WHERE estado='A' {$this->filtro()} ORDER BY proveedor,fecha_deuda,idmoneda ASC";
		// echo $sql;exit;
		$q=$this->db->query($sql);
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
				//echo $sql;exit; 
		$q = $this->db->query($sql);
		return $q->result_array();
	}
	
	public function get_monedas(){
		$sql = "SELECT DISTINCT idmoneda,moneda_corto moneda  FROM ({$this->query_master()}) b WHERE estado='A' {$this->filtro()} ORDER BY idmoneda ASC";
		/* echo $sql;exit; */
		$q=$this->db->query($sql);
		return $q->result_array();
	}
	
	public function get_totales(){
		$sql = "SELECT COALESCE(SUM(monto),0) total FROM ({$this->query_master()}) b WHERE estado='A' {$this->filtro()}";
		/* echo $sql;exit; */
		$q=$this->db->query($sql);
		return $q->row()->total;
	}
	
	public function proveedores(){
		$sql = "SELECT DISTINCT idproveedor,proveedor, ruc  FROM ({$this->query_master()}) b WHERE estado='A' {$this->filtro()} ORDER BY proveedor ASC";
		/* echo $sql;exit; */
		$q=$this->db->query($sql);
		return $q->result_array();
	}
	
	public function filtro(){
		$post	=	$_REQUEST;
		
		$where = "";
		if(!empty($post["fechainicio"])){
			if(!empty($post["fechafin"]))
				$where.=" AND fecha_deuda>='{$post["fechainicio"]}' AND fecha_deuda<='{$post["fechafin"]}' ";
			else
				$where.=" AND fecha_deuda='{$post["fechainicio"]}'";
		}
		if(!empty($post["idsucursal"]))
			$where.=" AND idsucursal='{$post["idsucursal"]}'";
		if(!empty($post["idproveedor"]))
			$where.=" AND idproveedor='{$post["idproveedor"]}'";
		if(!empty($post["idmoneda"]))
			$where.=" AND idmoneda='{$post["idmoneda"]}'";
		if(!empty($post["pagado"]))
			$where.=" AND pagado='{$post["pagado"]}'";
		if(!empty($post["idtipodocumento"]))
			$where.=" AND '{$post["idtipodocumento"]}'=ANY(regexp_split_to_array(idtipodocumento_compra, ','))";
		if(!empty($post["serie"]))
			$where.=" AND '{$post["serie"]}'=ANY(regexp_split_to_array(serie_compra, ','))";
		if(!empty($post["correlativo"]))
			$where.=" AND '{$post["correlativo"]}'=ANY(regexp_split_to_array(numero_compra, ','))";
		
		return $where;
	}
	
	public function head_detallado(){
		$whit_item		= 0;
		$whit_comprob	= 30;
		$whit_cred		= 22;
		$whit_fecha		= 18;
		$whit_let		= 13;
		$whit_moneda	= 34;
		$whit_importe	= 20;
		$whit_pagado	= 15;
		
		$total_ancho	= $whit_item + $whit_comprob +$whit_cred + $whit_fecha +$whit_let + $whit_moneda + $whit_importe*3 + $whit_pagado;

		$cabecera = array( //'item' 			=> array("columna"=>'ITEM'
													// ,"ancho"=>$whit_item
													// ,"porcentaje"=>$whit_item*100/$total_ancho
													// ,"salto"=>0
													// ,"alinear"=>'L')
							// ,
							'comprobante' 	=> array("columna"=>'COMPROBANTE'
													,"ancho"=>$whit_comprob
													,"porcentaje"=>$whit_comprob*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'L')
							,'nro_credito' 	=> array("columna"=>'NRO CREDITO'
													,"ancho"=>$whit_cred
													,"porcentaje"=>$whit_cred*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'L')
							,'fechadeuda' 	=> array("columna"=>'F. DEUDA'
													,"ancho"=>$whit_fecha
													,"porcentaje"=>$whit_fecha*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'C')
							,'cant_letras' 	=> array("columna"=>'LETRAS'
													,"ancho"=>$whit_let
													,"porcentaje"=>$whit_let*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'R')
							,'moneda' => array("columna"=>'MONEDA'
													,"ancho"=>$whit_moneda
													,"porcentaje"=>$whit_moneda*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'L')
							,'monto' 		=> array("columna"=>'IMPORTE'
													,"ancho"=>$whit_importe
													,"porcentaje"=>$whit_importe*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'R')
							,'monto_cancelado' => array("columna"=>'PAGADO'
													,"ancho"=>$whit_importe
													,"porcentaje"=>$whit_importe*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'R')
							,'monto_pendiente' => array("columna"=>'SALDO'
													,"ancho"=>$whit_importe
													,"porcentaje"=>$whit_importe*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'R')
							,'cancelado' => array("columna"=>'PAGADO'
													,"ancho"=>$whit_pagado
													,"porcentaje"=>$whit_pagado*100/$total_ancho
													,"salto"=>1
													,"alinear"=>'R')
						);
						
		return $cabecera;
	}
	
	public function arrary_cronograma(){
		return array("nro_letra"=>array("Letra",10,0,'C')
					,"forma_pago_compra"=>array("Forma Pago",26,0,'C')
					,"fecha_venc"=>array("Fec. Vencimiento",26,0,'C')
					,"fecha_pago"=>array("Fec. Pago",25,0,'C')
					,"moneda"=>array("Moneda",20,0,'C')
					,"tipopago"=>array("Pagado con",40,0,'L')
					,"monto_capital"=>array("Importe",25,1,'R')
		);
	}
	
	public function head_resumido(){
		$whit_item		= 10;
		$whit_proveed	= 65;
		$whit_ruc		= 20;
		$whit_comprob	= 30;
		$whit_cred		= 0;
		$whit_fecha		= 16;
		$whit_let		= 13;
		$whit_importe	= 19;
		
		$add_colum		= array();
		if(count($this->get_monedas())<2){
			$whit_proveed+=$whit_importe;
		}
		$total_ancho	= $whit_item + $whit_proveed + $whit_ruc + $whit_comprob +$whit_cred + $whit_fecha + $whit_let + $whit_importe*count($this->get_monedas());
		
		foreach($this->get_monedas() as $k=>$v){
			$x_salto = 0;
			if((count($this->get_monedas())-1)==$k)
				$x_salto = 1;
			$add_colum["moneda__".$v['idmoneda']]	= array("columna"=>"TOTALE ".$v["moneda"]
													,"ancho"=>$whit_importe
													,"porcentaje"=>$whit_importe*100/$total_ancho
													,"salto"=>$x_salto
													,"alinear"=>'R');
		}

		$cabecera = array( 'item' 			=> array("columna"=>'ITEME'
													,"ancho"=>$whit_item
													,"porcentaje"=>$whit_item*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'L')
							,'proveedor' => array("columna"=>'PROVEEDOR'
													,"ancho"=>$whit_proveed
													,"porcentaje"=>$whit_proveed*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'L')
							,'ruc' 		=> array("columna"=>'RUC'
													,"ancho"=>$whit_ruc
													,"porcentaje"=>$whit_ruc*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'C')
							,'comprobante' 	=> array("columna"=>'COMPROBANTE'
													,"ancho"=>$whit_comprob
													,"porcentaje"=>$whit_comprob*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'L')
							// ,'nro_credito' 	=> array("columna"=>'NRO CREDITO'
													// ,"ancho"=>$whit_cred
													// ,"porcentaje"=>$whit_cred*100/$total_ancho
													// ,"salto"=>0
													// ,"alinear"=>'L')
							,'fechadeuda' 	=> array("columna"=>'F. DEUDA'
													,"ancho"=>$whit_fecha
													,"porcentaje"=>$whit_fecha*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'C')
							,'cant_letras' 	=> array("columna"=>'LETRAS'
													,"ancho"=>$whit_let
													,"porcentaje"=>$whit_let*100/$total_ancho
													,"salto"=>0
													,"alinear"=>'R')
						);
						
		return array_merge($cabecera,$add_colum);
	}
	
	public function imprimir(){
		if($_REQUEST["ver"]=='R')
			$this->resumido();
		else
			$this->detallado();
	}
	
	public function resumido(){
		set_time_limit(0);
		$cabecera	= $this->head_resumido();
		$filas		= $this->dataresumido();
		

		$_REQUEST['type']	=	'server';
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","seguridad.sucursal","seguridad.view_usuario"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE DEUDA( RESUMIDO) A PROVEEDORES DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE DEUDA( RESUMIDO) A PROVEEDORES DE  ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		}else
			$this->pdf->SetTitle(utf8_decode("REPORTE DEUDA( RESUMIDO) A PROVEEDORES"), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$pagado = "PAGADO";
		if($_REQUEST['pagado']=="N")
			$pagado = "PENDIENTE";
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->Cell(30,3,"ESTADO",0,0,'L');
		$this->pdf->Cell(5,3,":",0,0,'C');
		$this->pdf->SetFont('Arial','',10);
		$this->pdf->Cell(5,3,utf8_decode($pagado),0,1,'L');
		$this->pdf->Ln(5);
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->sucursal->find($_REQUEST['idsucursal']);
			$this->pdf->Cell(5,3,utf8_decode($this->sucursal->get("descripcion")),0,1,'L');
		}
		$this->pdf->Ln(5);
		
		$lienzo = 0;
		$space	= 0;
		
		$key_numeric = array("importe");
		foreach($cabecera as $k=>$v){
			$lienzo+=$v['ancho'];
		}
		$lienzo = $lienzo + $space;
		$this->pdf->SetDrawColor(204, 204, 204);
		$this->pdf->setFillColor(240, 240, 240);
		
		/************************** CONTENIDO *****************************************/
		$this->pdf->SetFont('Arial','B',7.5);
		foreach($cabecera as $k=>$v){
			$this->pdf->Cell($v['ancho'],6,$v['columna'],1,$v['salto'],'C',true);
		}
		
		foreach($filas as $key=>$value){
			/*For file autosize*/
			$values	= array();
			$width	= array();
			$pos	= array();
			/*For file autosize*/
			foreach($cabecera as $k=>$v){
				$value['item'] = $key+1;
				if(!isset($value[$k])){
					$value[$k] = number_format(0,3,'.',',');
					if("moneda__".$value["idmoneda"]==$k){
						$value[$k] = number_format($value["monto"],3,'.',',');
					}
				}
				
				if($k=="comprobante")
					$value[$k]	=	str_replace(","," ",$value[$k]);

				$width[]	= $v["ancho"];
				$values[]	= utf8_decode($value[$k]);
				$pos[]		= $v["alinear"];
			}
			
			$this->pdf->SetFont('Arial','',7.5);
			$this->pdf->SetWidths($width);
			$this->pdf->Row($values, $pos, "Y", "Y");
		}
		
		/************************** CONTENIDO *****************************************/
		
		/************************** FOOT *****************************************/
		$this->pdf->Ln(10);
		foreach($this->get_monedas() as $k=>$v){
			$_REQUEST["idmoneda"]	=	$v["idmoneda"];
			$this->pdf->SetFont('Arial','B',7.5);
			$this->pdf->Cell(70,6,"",0,0,'C',false);
			$this->pdf->Cell(30,6,"TOTAL ".$v['moneda'],1,0,'C',true);
			$this->pdf->SetFont('Arial','',7.5);
			$this->pdf->Cell(25,6,number_format($this->get_totales(),3,'.',','),1,1,'R');
		}
		/************************** FOOT *****************************************/
		$this->pdf->Output();
	}
	
	public function detallado(){
		set_time_limit(0);
		$cabecera	= $this->head_detallado();
		$filas		= $this->dataresumido();
		$proveedor	= $this->proveedores();
		

		$_REQUEST['type']	=	'server';
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","seguridad.sucursal","seguridad.view_usuario"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE DEUDA( DETALLADO) A PROVEEDORES DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE DEUDA( DETALLADO) A PROVEEDORES DE  ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE DEUDA( DETALLADO) A PROVEEDORES"), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$lienzo = 0;
		$space	= 5;
		
		$key_numeric = array("monto","monto_pendiente","monto_cancelado");
		foreach($cabecera as $k=>$v){
			$lienzo+=$v['ancho'];
		}
		$lienzo = $lienzo + $space;
		$this->pdf->SetDrawColor(204, 204, 204);
		$this->pdf->setFillColor(240, 240, 240);
		
		$pagado = "PAGADO";
		if($_REQUEST['pagado']=="N")
			$pagado = "PENDIENTE";
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->Cell(30,3,"ESTADO",0,0,'L');
		$this->pdf->Cell(5,3,":",0,0,'C');
		$this->pdf->SetFont('Arial','',10);
		$this->pdf->Cell(5,3,utf8_decode($pagado),0,1,'L');
		$this->pdf->Ln(5);
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->sucursal->find($_REQUEST['idsucursal']);
			$this->pdf->Cell(5,3,utf8_decode($this->sucursal->get("descripcion")),0,1,'L');
		}
		$this->pdf->Ln(5);
		
		foreach($proveedor as $key=>$value){
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell($lienzo,6,$value['proveedor']."   -   ".$value["ruc"],0,1,'L');
			
			$new_fila = $this->seleccion($filas, array("idproveedor"=>$value["idproveedor"]));
			foreach($new_fila as $kk=>$val){
				/*For file autosize*/
				$values	= array();
				$width	= array();
				$pos	= array();
				/*For file autosize*/

				foreach($cabecera as $k=>$v){
					$val['item'] = $kk+1;
					$this->pdf->SetFont('Arial','B',7.5);
					$this->pdf->Cell($v['ancho'],4,$v['columna'],1,$v['salto'],'C',true);
					if(in_array($k,$key_numeric))
						$val[$k] = number_format($val[$k],3,'.',',');
					
					$width[]	= $v["ancho"];
					$values[]	= utf8_decode($val[$k]);
					$pos[]		= $v["alinear"];
				}
				$this->pdf->SetFont('Arial','',7.5);
				$this->pdf->SetWidths($width);
				$this->pdf->Row($values, $pos, "Y", "Y");
				
				$this->pdf->Cell($space,4,'',0,0);
				$this->pdf->Cell($lienzo-$space*2,6,"LETRAS",0,1,'L');
				
				$this->pdf->Cell($space,4,'',0,0);
				foreach($this->arrary_cronograma() as $x=>$y){
					$this->pdf->SetFont('Arial','B',7.5);
					$this->pdf->Cell($y[1],4,$y[0],1,$y[2],'C',true);
				}
				
				$letras_deuda = $this->detalle_cronograma($val["iddeuda"]);
				$this->pdf->Cell($space,4,'',0,0);
				foreach($letras_deuda as $i=>$j){
					foreach($this->arrary_cronograma() as $x=>$y){
						$this->pdf->SetFont('Arial','',7.5);
						$this->pdf->Cell($y[1],4,$j[$x],1,$y[2],$y[3]);
					}
				}
				$this->pdf->Ln(5);
			}
			$this->pdf->Ln(8);
		}
		
		$this->pdf->Output();
	}
}
?>