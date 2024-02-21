<?php
include_once "Controller.php";
set_time_limit(90);
class Reporteinvper extends Controller {
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
		// $data["conceptos"] = $this->conceptos();
		// $data["monedaactiva"] = $this->moneda();
		//$data["iniciales"] = $this->apertura();
		$data["sucursal"] = $this->listsucursal();
		// $data["tipomov"] = $this->tipomovimiento();
		$data['idperfil'] = $this->get_var_session("idperfil");
		$data['control_reporte'] = $this->get_var_session("control_reporte")?$this->get_var_session("control_reporte"):'N';
		$data['es_superusuario']= $this->get_var_session("es_superusuario")?$this->get_var_session("es_superusuario"):'N';
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idproveedor");
		$this->combobox->setAttr("name","idproveedor");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select('idproveedor,nombre');
		$query = $this->db->where("estado","A")->order_by("nombre")->get("compra.proveedor");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['proveedor'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idcategoria");
		$this->combobox->setAttr("name","idcategoria");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select('idcategoria,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.categoria");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['categoria'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmarca");
		$this->combobox->setAttr("name","idmarca");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select('idmarca,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.marca");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['marca'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmodelo");
		$this->combobox->setAttr("name","idmodelo");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select('idmodelo,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.modelo");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['modelo'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idtipoventa");
		$this->combobox->setAttr("name","idtipoventa");
		$this->combobox->setAttr("class","form-control  input-xs");
		// $this->combobox->setAttr("required","");
		$this->db->select('idtipoventa,descripcion');
		$query = $this->db->where("estado","A")->where("mostrar_en_compra","S")->order_by("descripcion")->get("venta.tipo_venta");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['tipopago'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","form-control input-xs");
		// $this->combobox->setAttr("required","");
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
		$query = $this->db->where("mostrar_en_compra","S")->where("estado","A")->order_by("descripcion")->get("venta.tipo_documento");
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
		$sql = "SELECT d.idsucursal,s.descripcion sucursal, a.descripcion almacen ,d.idalmacen
						, l.descripcion as linea, m.descripcion as marca, c.descripcion as categoria, mo.descripcion as modelo
						, d.idproducto
						, p.descripcion producto, u.abreviatura as unidad, sum (d.cantidad*d.tipo_number) as saldoinicial
						, d2.salidas,d1.entradas
						, (sum (d.cantidad*d.tipo_number))-coalesce(d2.salidas,0)+coalesce(d1.entradas,0) as saldofinal
						, round(cast(d1.ppc as numeric),2)ppc 
						, round(cast((d3.sumaventa/d3.nroarticulosv)as numeric),2)ppv 
					FROM (SELECT DISTINCT idproducto FROM almacen.detalle_almacen WHERE estado = 'A' ) w
					LEFT JOIN almacen.detalle_almacen d on w.idproducto =d.idproducto
					LEFT JOIN (
							SELECT d.idsucursal,d.idalmacen,d.idproducto
								,sum(d.cantidad) as entradas,avg(COALESCE(d.precio_costo,0))ppc
							FROM almacen.detalle_almacen d
								WHERE d.estado <> 'I' AND d.tipo = 'E'  {$this->condicion_resumido()}
							group by d.idsucursal,d.idalmacen, d.idproducto
							) d1 on d1.idproducto = d.idproducto AND d1.idalmacen = d.idalmacen AND d1.idsucursal = d.idsucursal
					LEFT JOIN (
							SELECT d.idsucursal,d.idalmacen,d.idproducto
								,sum(d.cantidad) as salidas
							FROM almacen.detalle_almacen d
								WHERE d.estado <> 'I' AND  d.tipo = 'S' {$this->condicion_resumido()}
							group by d.idsucursal,d.idalmacen,d.idproducto 
							) d2 on d2.idproducto = d.idproducto AND d2.idalmacen = d.idalmacen AND d2.idsucursal = d.idsucursal
					LEFT JOIN ( SELECT d.idsucursal,d.idalmacen,d.idproducto, sum(COALESCE(d.cantidad,0)) nroarticulosv,sum(COALESCE(d.precio_venta*d.cantidad,0))as sumaventa
					FROM almacen.detalle_almacen d WHERE d.estado <> 'I' AND d.tabla = 'V' {$this->condicion_resumido()}
							group by d.idsucursal,d.idalmacen,d.idproducto 
							) d3 on d3.idproducto = d.idproducto AND d3.idalmacen = d.idalmacen AND d3.idsucursal = d.idsucursal
					
					INNER JOIN compra.producto p ON p.idproducto = d.idproducto
					INNER JOIN compra.unidad u ON u.idunidad = d.idunidad
					INNER JOIN seguridad.sucursal s ON s.idsucursal = d.idsucursal
					INNER JOIN almacen.almacen a ON a.idalmacen = d.idalmacen
					INNER JOIN general.linea l ON l.idlinea = p.idlinea
					INNER JOIN general.marca m on m.idmarca = p.idmarca
					INNER JOIN general.categoria c on c.idcategoria = p.idcategoria
					INNER JOIN general.modelo mo on mo.idmodelo = p.idmodelo
	
					WHERE d.estado <> 'I'  {$this->condicion_detallado()}
					group by d.idsucursal,s.descripcion,a.descripcion,d.idalmacen
					, l.descripcion , m.descripcion , c.descripcion , mo.descripcion 
					,d.idproducto,p.descripcion, u.abreviatura, d2.salidas,d1.entradas 
					,d3.sumaventa,d3.nroarticulosv
				,d1.ppc
				";
		 $query      = $this->db->query($sql);
		// echo "<pre>";echo $sql;echo "</pre>";exit;	
		$data = $query->result_array();
		//print_r($query->result_array());exit;
		return $data;
	}
		public function return_cajero(){
		$fields = $this->input->post();
		$idcajero = $this->get_param('idrol_cajero')?$this->get_param('idrol_cajero'):4;
		$idsucursal = $this->get_var_session("idsucursal");
		if(!empty($fields['idsucursal']))
			$idsucursal = $fields['idsucursal'];
		$codtipoempleado_cajero	= $this->get_param('idtipoempleado_cajero')?$this->get_param('idtipoempleado_cajero'):'0';
		$es_superadmin			= $this->get_var_session("es_superusuario")?$this->get_var_session("es_superusuario"):'N';
		$id_usuario				= $this->get_var_session("idusuario");
		
		$sql ="	SELECT u.idusuario,u.nombres,u.appat,u.apmat 
				FROM seguridad.acceso_empresa ae
				JOIN seguridad.usuario u ON u.idusuario=ae.idusuario
				WHERE ae.estado='A' 
				AND idtipoempleado='{$idcajero}' 
				AND ae.idsucursal='$idsucursal' ";
		if($es_superadmin=='N')
			$sql.=" AND ae.idusuario='{$id_usuario}'";

		$query = $this->db->query($sql);
		$this->response($query->result_array());
	}

	
	public function moneda_colum(){
		$sql = "SELECT  c.idmoneda
				,moneda
				,simbolo
				,abreviatura
				FROM compra.compra_detalle_view c
				WHERE estado='A'
			
				GROUP BY c.idmoneda
				,moneda
				,simbolo
				,abreviatura
				ORDER BY moneda;";
		$query= $this->db->query($sql);
		// echo $sql;exit;
		// print_r($query->result_array());exit;
		$data = $query->result_array();			
		if(empty($data)){
			$sql = "SELECT idmoneda, descripcion moneda, simbolo, abreviatura FROM general.moneda WHERE idmoneda='1';";
			$query= $this->db->query($sql);
			$data = $query->result_array();			
		}
		return $data;
	}
	
	public function totales_monedas($where_and = ''){
		$sql = "SELECT 
				COALESCE(SUM(subtotal_compra+igv_compra),0) monto
				FROM compra.compra_head_view 
				WHERE estado='A'
				
						";
		// echo $sql."<br>";
		$query= $this->db->query($sql);

		$data = $query->row('monto');			
		
		return $data;
	}
	

	public function condicion_resumido($add_where=''){
		$where = " ";

		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND (d.fecha)>='{$_REQUEST['fechainicio']}' AND (d.fecha)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND (d.fecha)='{$_REQUEST['fechainicio']}' ";
			}
		}
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND d.idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		if(!empty($_REQUEST['idcategoria'])){
			$where.=" AND p.idcategoria='{$_REQUEST['idcategoria']}' ";
		}
		
		if(!empty($_REQUEST['idmarca'])){
			$where.=" AND p.idmarca='{$_REQUEST['idmarca']}' ";
		}
		
		if(!empty($_REQUEST['idmodelo'])){
			$where.=" AND p.idmodelo='{$_REQUEST['idmodelo']}' ";
		}
		$where.=$add_where;
		return $where;
	}
	
	public function condicion_detallado(){
	   $where2 = " AND s.idempresa='{$this->get_var_session('idempresa')}'";

		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where2.=" AND (d.fecha)<'{$_REQUEST['fechainicio']}'";
			}else{
				$where2.=" AND (d.fecha)<'{$_REQUEST['fechainicio']}' ";
			}
		}
		if(!empty($_REQUEST['idsucursal'])){
			$where2.=" AND d.idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		if(!empty($_REQUEST['idcategoria'])){
			$where2.=" AND p.idcategoria='{$_REQUEST['idcategoria']}' ";
		}
		
		if(!empty($_REQUEST['idmarca'])){
			$where2.=" AND p.idmarca='{$_REQUEST['idmarca']}' ";
		}
		
		if(!empty($_REQUEST['idmodelo'])){
			$where2.=" AND p.idmodelo='{$_REQUEST['idmodelo']}' ";
		}
		//$where2.=$add_where;
		return $where2;
	}
	
	public function imprimir(){
		if($_REQUEST['ver']=='R'){
			$this->resumido();
		}else if($_REQUEST['ver']=='D'){
			$this->detallado();
		}
	}
	
	public function resumido(){
		set_time_limit(0);
		$datos      = $this->dataresumido();
		$monedas = $this->moneda_colum();
		$whit_sucursal	=30;
		$whit_almacen 	=45;
		$whit_idproducto=10;
		$whit_producto 	=90;
		$whit_saldo 	=18;
		$whit_pp 		=60;
		$whit_unidad	= 10;//PARA EL WHIT DE LAS MONEDAS
		$whit_min	= 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_prov	= 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}

		$cabecera = array('sucursal'=> array('SUCURSAL',$whit_sucursal)
							,'almacen'=> array('ALMACEN',$whit_almacen)
							,'idproducto' => array('COD PROD.',$whit_idproducto)
							,'producto' => array('PRODUCTO',$whit_producto)
							,'unidad' => array('U.M',$whit_unidad)
							,'saldoinicial' => array('SAL. INICIAL',$whit_saldo,'R')
							,'entradas' => array('ENT. COMP',$whit_saldo,'R')
							,'entradas' => array('ENT. OTROS',$whit_saldo,'R')
							,'salidas' => array('SAL. VENTAS',$whit_saldo,'R')
							,'salidas' => array('SAL. OTROS',$whit_saldo,'R')
							,'saldofinal' => array('SAL. FINAL',$whit_saldo,'R')
							,'ppc' => array('P.P.C',$whit_saldo,'R')
							,'ppv' => array('P.P.V',$whit_saldo,'R')
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","compra.proveedor","venta.tipo_venta","general.moneda","seguridad.sucursal","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE INVENTARIO PERMANENTE DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE INVENTARIO PERMANENTE DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE COMINVENTARIO PERMANENTE "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage('L');
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
	
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,0,'C',true);
			$total_lienzo = $total_lienzo + $val[1];
		}
	
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
	

	
	public function exportar(){
		if($_REQUEST['ver']=='R'){
			$this->exportar_resumido();
		}else if($_REQUEST['ver']=='D'){
			$this->exportar_detallado();
		}
	}
	
	public function array_head(){
		
		$whit_sucursal	=30;

		$whit_item		= 10;
		$whit_almacen 	=45;
		$whit_idproducto=10;
		$whit_producto 	=90;
		$whit_saldo 	=18;
		$whit_pp 		=60;
		$whit_unidad	= 10;//PARA EL WHIT DE LAS MONEDAS
		$whit_min	= 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_prov	= 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		$total_ancho	= $whit_item	+$whit_sucursal+$whit_almacen+	$whit_idproducto+$whit_producto+$whit_saldo+$whit_pp+$whit_unidad;

		$cabecera = array( 'item' => array('ITEM',$whit_item,$whit_item*100/$total_ancho,0,'L')
							,'sucursal'=> array('SUCURSAL',$whit_sucursal,$whit_sucursal*100/$total_ancho,0,'L')
							,'almacen'=> array('ALMACEN',$whit_almacen,$whit_almacen*100/$total_ancho,0,'L')
							,'linea'=> array('LINEA',$whit_sucursal,$whit_sucursal*100/$total_ancho,0,'L')
							,'marca'=> array('MARCA',$whit_sucursal,$whit_sucursal*100/$total_ancho,0,'L')
							,'modelo'=> array('MODELO',$whit_sucursal,$whit_sucursal*100/$total_ancho,0,'L')
							,'categoria'=> array('CATEGORIA',$whit_sucursal,$whit_sucursal*100/$total_ancho,0,'L')
							,'idproducto' => array('COD PROD.',$whit_idproducto,$whit_idproducto*100/$total_ancho,0,'L')
							,'producto' => array('PRODUCTO',$whit_producto,$whit_producto*100/$total_ancho,0,'L')
							,'unidad' => array('U.M',$whit_unidad,$whit_unidad*100/$total_ancho,0,'L')
							,'saldoinicial' => array('SAL. INICIAL',$whit_saldo,$whit_saldo*100/$total_ancho,0,'L')
							,'salidas' => array('SALIDAS',$whit_saldo,$whit_saldo*100/$total_ancho,0,'L')
							,'entradas' => array('ENTRADAS',$whit_saldo,$whit_saldo*100/$total_ancho,0,'L')
							,'saldofinal' => array('SAL. FINAL',$whit_saldo,$whit_saldo*100/$total_ancho,0,'L')
							,'ppc' => array('P.P.C',$whit_saldo,$whit_saldo*100/$total_ancho,0,'L')
							,'ppv' => array('P.P.V',$whit_saldo,$whit_saldo*100/$total_ancho,0,'L')
						);
						
		return $cabecera;
	}
	

	
	public function exportar_resumido(){
		set_time_limit(0);
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('CSV');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE INVENTARIO PERMANENTE",true);
		$Oexcel->getActiveSheet()->setCellValue("H4", "F.INICIO:");
		$Oexcel->getActiveSheet()->setCellValue("I4", "{$_REQUEST['fechainicio']}");
		$Oexcel->getActiveSheet()->setCellValue("H5", "F.FIN:");
		$Oexcel->getActiveSheet()->setCellValue("I5", "{$_REQUEST['fechafin']}");
		$Oexcel->getActiveSheet()->setCellValue("H6", "F.ACTUAL:");
		$Oexcel->getActiveSheet()->setCellValue("H7", "HORA:");
		
		
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
		

		$col++;

		/************************** CABECERA *****************************************/
		
		
		/************************** CUERPO *****************************************/
		$rows 		= $this->dataresumido();
		
		foreach($rows as $key=>$val){
			$val['item'] = (int) $key + 1;

			$alfabeto = 65;
			foreach($this->array_head() as $k=>$v){
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, ($val[$k]));
				$alfabeto++;
			}
		
			$col++;
		}
		/************************** CUERPO *****************************************/
		
		
		$alfabeto = 65;
		foreach($this->array_head() as $k=>$v){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$alfabeto++;
		}
	
		
		$filename='reporteinvpermanente'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
	
	
}
?>