<?php

include_once "Controller.php";

class Reportecompra extends Controller {
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
		$sql = "SELECT tipodocumento
				,(simbolo_tipodoc||' '||serie||'-'||numero) comprobante
				,to_char(fecha_operacion, 'DD/MM/YYYY'::text) fecha_operacion
				,tipoventa
				,moneda
				,abreviatura
				,proveedor
				,almacen
				,subtotal_compra
				,igv_compra
				,descuento_compra
				,(subtotal_compra+igv_compra-descuento_compra) total_compra
				,idmoneda
				FROM compra.compra_detalle_view
				WHERE estado='A'
				{$this->condicion_resumido()}
				{$this->condicion_detallado()}
				GROUP BY fecha_operacion
				,tipoventa
				,moneda
				,abreviatura
				,proveedor
				,almacen
				,subtotal_compra
				,igv_compra
				,descuento_compra
				,comprobante
				,tipodocumento
				,idmoneda;
				";
		$query      = $this->db->query($sql);
		// echo $sql;exit;
		$data = $query->result_array();
		// print_r($query->result_array());exit;
		return $data;
	}
	
	public function proveedor_agrupado(){
		$sql = "SELECT proveedor,idproveedor
				FROM compra.compra_detalle_view 
				WHERE estado='A'
				{$this->condicion_resumido()}
				AND idcompra IN (SELECT idcompra FROM compra.compra_detalle_view WHERE estado='A' {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY proveedor,idproveedor
				ORDER BY proveedor;";
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}
	
	public function comprobante_agrupado(){
		$sql = "SELECT (serie||'-'||numero) comprobante
				,to_char(fecha_operacion, 'DD/MM/YYYY'::text) fecha_operacion
				,idproveedor 
				,idcompra
				,tipodocumento
				,moneda
				,cambio_moneda
				FROM compra.compra_detalle_view 
				WHERE estado='A' 
				{$this->condicion_resumido()}
				AND idcompra IN (SELECT idcompra FROM compra.compra_detalle_view WHERE estado='A' {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY comprobante ,fecha_operacion,idproveedor 
				,moneda,abreviatura,idcompra,tipodocumento,cambio_moneda
				ORDER BY fecha_operacion;";
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}
	
	public function moneda_colum(){
		$sql = "SELECT  c.idmoneda
				,moneda
				,simbolo
				,abreviatura
				FROM compra.compra_detalle_view c
				WHERE estado='A'
				{$this->condicion_resumido()}
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
				{$this->condicion_resumido($where_and)}
				AND idcompra IN (SELECT idcompra FROM compra.compra_detalle_view WHERE estado='A' {$this->condicion_resumido($where_and)} {$this->condicion_detallado()})
				";
		// echo $sql."<br>";
		$query= $this->db->query($sql);

		$data = $query->row('monto');			
		
		return $data;
	}
	
	public function dataDetallado(){
		$sql="	SELECT
				idcompra
				,producto
				,cantidad_detalle
				,unidadmedida
				,precio_detalle
				,igv_detalle
				,flete_detalle
				,idproveedor
				,precio_detalle*cantidad_detalle subtotal 
				FROM compra.compra_detalle_view
				WHERE estado='A'
				{$this->condicion_resumido()}
				AND idcompra IN (SELECT idcompra FROM compra.compra_detalle_view WHERE estado='A' {$this->condicion_resumido()} {$this->condicion_detallado()})
				";
		// echo $sql;exit;
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}
	
	public function condicion_resumido($add_where=''){
		$where = " AND idempresa='{$this->get_var_session('idempresa')}'";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND (fecha_operacion)>='{$_REQUEST['fechainicio']}' AND (fecha_operacion)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND (fecha_operacion)='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['idproveedor'])){
			$where.=" AND idproveedor='{$_REQUEST['idproveedor']}' ";
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$where.=" AND idtipoventa='{$_REQUEST['idtipoventa']}' ";
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$where.=" AND idmoneda='{$_REQUEST['idmoneda']}' ";
		}
		if(!empty($_REQUEST['idtipodocumento'])){
			$where.=" AND idtipodocumento='{$_REQUEST['idtipodocumento']}' ";
		}
		$where.=$add_where;
		return $where;
	}
	
	public function condicion_detallado(){
		$where = '';

		if(!empty($_REQUEST['idcategoria'])){
			$where.=" AND idcategoria='{$_REQUEST['idcategoria']}' ";
		}
		
		if(!empty($_REQUEST['idmarca'])){
			$where.=" AND idmarca='{$_REQUEST['idmarca']}' ";
		}
		
		if(!empty($_REQUEST['idmodelo'])){
			$where.=" AND idmodelo='{$_REQUEST['idmodelo']}' ";
		}
		
		if(!empty($_REQUEST['serie'])){
			// $where.=" AND compra.idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		
		return $where;
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
		$whit_compr=30;
		$whit_fecha=18;
		$whit_prov=127;
		$whit_min = 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}

		$cabecera = array('comprobante'=> array('Nro DOC',$whit_compr)
							,'fecha_operacion' => array('FECHA',$whit_fecha)
							,'proveedor' => array('PROVEEDOR',$whit_prov)
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","compra.proveedor","venta.tipo_venta","general.moneda","seguridad.sucursal","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE COMPRA DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE COMPRA DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE COMPRA "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		if(!empty($_REQUEST['idproveedor'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"PROVEEDOR",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->proveedor->find($_REQUEST['idproveedor']);
			$this->pdf->Cell(5,3,$this->proveedor->get("nombre"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->sucursal->find($_REQUEST['idsucursal']);
			$this->pdf->Cell(5,3,$this->sucursal->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"COMPROBANTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_documento->find($_REQUEST['idtipodocumento']);
			$this->pdf->Cell(5,3,$this->tipo_documento->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"MONEDA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->moneda->find($_REQUEST['idmoneda']);
			$this->pdf->Cell(5,3,$this->moneda->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO OPERACION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$this->pdf->Cell(5,3,$this->tipo_venta->get("descripcion"),0,1,'L');
		}
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,0,'C',true);
			$total_lienzo = $total_lienzo + $val[1];
		}
		$this->pdf->Cell($whit_total_m,5,"TOTAL",1,1,'C');
		$this->pdf->Cell($total_lienzo,5,"",0,0,'C');
		foreach($monedas as $key=>$val){
			$this->pdf->Cell($whit_min,4,$val['abreviatura']." ".$val['simbolo'],1,0,'C',true);			
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
			
			foreach($monedas as $k=>$v){
				$subt = 0;
				if($v['idmoneda']==$val['idmoneda']){
					$subt = $val['total_compra'];
				}
				$this->pdf->Cell($whit_min,5,number_format($subt,2,'.',','),1,0,'R');			
			}
			$this->pdf->Ln(); 
		}
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(($whit_compr + $whit_fecha + $whit_prov),5,"TOTAL",0,0,'R');
		foreach($monedas as $k=>$v){
			$filtro = $this->totales_monedas(" AND idmoneda='$v[idmoneda]' ");
			$this->pdf->Cell($whit_min,5,number_format($filtro,2,'.',','),0,0,'R');			
		}
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
	
	public function detallado(){
		set_time_limit(0);
		$datos      	= $this->dataDetallado();
		$monedas 		= $this->moneda_colum();
		$comprobantes 	= $this->comprobante_agrupado();
		$proveedor 		= $this->proveedor_agrupado();
		$whit_compr		=30;
		$whit_fecha		=10;
		$whit_cant		=10;
		$whit_um		=20;
		$whit_pu		=20;
		$whit_igv		=15;
		$whit_prov		=105;
		$whit_prod		=100;
		$whit_provd		=105;
		$whit_min 		= 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m 	= 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}
		$total_lienzo= $whit_fecha+$whit_prod+$whit_cant+$whit_pu+$whit_igv+$whit_min+$whit_um;

		$cabecera = array('' => array('ITEM',$whit_fecha)
							,'producto' => array('PRODUCTO',$whit_prod)
							,'unidadmedida' => array('UM',$whit_um)
							,'cantidad_detalle' => array('CANT',$whit_cant)
							,'precio_detalle' => array('P.U',$whit_pu,'R')
							,'igv_detalle' => array('IGV',$whit_igv,'R')
							,'subtotal' => array('TOTAL',$whit_min,'R')
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","compra.proveedor","venta.tipo_venta","general.moneda","seguridad.sucursal"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE COMPRA DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE COMPRA DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE COMPRA "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);

		if(!empty($_REQUEST['idproveedor'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"PROVEEDOR",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->proveedor->find($_REQUEST['idproveedor']);
			$this->pdf->Cell(5,3,$this->proveedor->get("nombre"),0,1,'L');
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
		
		if(!empty($_REQUEST['idtipoventa'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO OPERACION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$this->pdf->Cell(5,3,$this->tipo_venta->get("descripcion"),0,1,'L');
		}
		$this->pdf->Ln();
		

		foreach ($proveedor as $key => $val) {
			$cant_aux=0;
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->SetTextColor(22,160,133);
			$this->pdf->Cell($whit_provd,5,trim($val['proveedor']),0,1);
			$this->pdf->SetFont('Arial','',8);
			foreach ($cabecera as $k => $v) {
				$cant_aux++;
				$this->pdf->SetTextColor(0,0,0);
				$salto=0;
				if(count($cabecera)==($cant_aux)){
					$salto=1;
				}
				
				$this->pdf->Cell($v[1],5,($v[0]),1,$salto);
			}
			$cant_aux=0;
			$data_head_detealle = $this->seleccion($comprobantes, array("idproveedor"=>$val['idproveedor']));
			foreach($data_head_detealle as $kk=>$vv){
				$cant_aux++;
				$salto=0;
				if(count($data_head_detealle)==($cant_aux)){
					$salto=1;
				}
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(60,5,$vv['tipodocumento']." ".$vv['comprobante'],0,0);
				$this->pdf->Cell(20,5,$vv['fecha_operacion'],0,0);
				$this->pdf->Cell(40,5,$vv['moneda']." TC: ".number_format($vv['cambio_moneda'],2),0,1);
				$this->pdf->SetFont('Arial','',8);
				$cant_aux=0;
				$x_cant_aux=0;
				$total_pu= $total_igv=$total_total_c=0;
				$data_body_detealle = $this->seleccion($datos, array("idproveedor"=>$val['idproveedor'],'idcompra'=>$vv['idcompra']));
				foreach($data_body_detealle as $kkk=>$vvv){
					$cant_aux++;
					$this->pdf->SetFont('Arial','',8);
					foreach ($cabecera as $ke => $va) {
						$x_cant_aux++;
						if(count($cabecera)==($x_cant_aux)){
							$salto=1;
						}
						$x_col= $cant_aux.")";
						if(!empty($ke)){
							$x_col = $vvv[$ke];
						}
						// if(is_numeric($x_col)){
							// $x_col=number_format($x_col,2);
						// }
						if(isset($va[2]))
							$this->pdf->Cell($va[1],5,($x_col),0,0,$va[2]);
						else
							$this->pdf->Cell($va[1],5,($x_col),0,0);
					}
					$this->pdf->Ln();
					$total_pu 		= $total_pu + $vvv['precio_detalle']*$vvv['cantidad_detalle'];
					$total_igv 		= $total_igv + $vvv['igv_detalle'];
					$total_total_c 	= $total_total_c + $vvv['subtotal'];
				}
				$this->pdf->Cell($total_lienzo,0,'',1,1);
				$this->pdf->Cell($whit_prod+$whit_fecha+$whit_um+$whit_cant,5,'',0,0);
				$this->pdf->Cell($whit_pu,5,number_format($total_pu,3),1,0,'R');
				$this->pdf->Cell($whit_igv,5,number_format($total_igv,3),1,0,'R');
				$this->pdf->Cell($whit_min,5,number_format(($total_total_c+$total_igv),3),1,1,'R');
			}
			$this->pdf->Ln(5);
		}
		
		$this->pdf->Cell($total_lienzo,0,'',1,1);
		$this->pdf->Ln(1);
		$this->pdf->Cell($total_lienzo,0,'',1,1);
		$this->pdf->Ln(1);
		
		$this->pdf->Cell(100,5,'',0,0);
		$this->pdf->Cell(50,5,'TOTAL',0,1,'C');
		
		foreach($monedas as $k=>$vv){
			$this->pdf->Cell(50,5,'',0,0);
			$this->pdf->Cell(50,5,$vv['moneda'],1,0);
			$filtro = $this->totales_monedas(" AND idmoneda='$vv[idmoneda]' ");
			$this->pdf->Cell(50,5,number_format($filtro,3),1,1,'R');
			
		}
		
		/************************** BODY *****************************************/
		/************************** BODY *****************************************/
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
		$whit_item		= 22;
		$whit_compr		= 12;
		$whit_fecha		= 16;
		$whit_cli		= 96;
		
		$total_ancho	= $whit_compr+$whit_fecha+$whit_cli;

		$cabecera = array( 'item' => array('ITEM',$whit_item,$whit_item*100/$total_ancho,0,'L')
							,'comprobante' => array('COMPROBANTE',$whit_compr,$whit_compr*100/$total_ancho,0,'L')
							,'fecha_operacion' => array('FECHA',$whit_fecha,$whit_fecha*100/$total_ancho,0,'L')
							,'proveedor' => array('PROVEEDOR',$whit_cli,$whit_cli*100/$total_ancho,0,'L')
						);
						
		return $cabecera;
	}
	
	public function array_detalle(){
		$whit_fecha		=10;
		$whit_prov		=115;
		$whit_cant		=10;
		$whit_um		=20;
		$whit_pu		=20;
		$whit_igv		=15;
		$whit_min 		= 25;//PARA EL WHIT DE LAS MONEDAS

		$cabecera = array('item' => array('ITEM',$whit_fecha)
							,'producto' => array('PRODUCTO',$whit_prov)
							,'cantidad_detalle' => array('CANT',$whit_cant)
							,'unidadmedida' => array('UM',$whit_um)
							,'precio_detalle' => array('P.U',$whit_pu,'R')
							,'igv_detalle' => array('IGV',$whit_igv,'R')
							,'subtotal' => array('TOTAL',$whit_min,'R')
						);
		return $cabecera;
	}
	
	public function exportar_resumido(){
		set_time_limit(0);
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE COMPRA RESUMIDO",true);
		
		$col = 9;
		if(!empty($_REQUEST['idproveedor'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'PROVEEDOR : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("compra.proveedor");
			$this->proveedor->find($_REQUEST['idproveedor']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->proveedor->get("nombre")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SUCURSAL : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->sucursal->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO DOCUMENTO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipo_documento");
			$this->tipo_documento->find($_REQUEST['idtipodocumento']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->tipo_documento->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MONEDA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("general.moneda");
			$this->moneda->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->moneda->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		// if(!empty($_REQUEST['idvendedor'])){
			// $Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			// $Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENDEDOR : ');
			// $Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			// $this->load_model("seguridad.usuario");
			// $this->usuario->find($_REQUEST['idvendedor']);
			// $Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->usuario->get("usuario")));
			// $Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			// $col++;
		// }
		
		if(!empty($_REQUEST['idtipoventa'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO VENTA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipo_venta");
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->tipo_venta->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		
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
		
		$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'TOTAL');
		$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($alfabeto+count($this->moneda_colum())-1).$col);
		$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
		$xalfabeto = $alfabeto;
		foreach($this->moneda_colum() as $k=>$v){
			$Oexcel->getActiveSheet()->getStyle(chr($xalfabeto).$col)->applyFromArray(
					array('borders' => array(
									'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
								)
					)
				);
			$xalfabeto++;
		}
		$col++;
		foreach($this->moneda_colum() as $key=>$val){
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val['abreviatura']." ".$val['simbolo']);
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
		}
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
			
			foreach($this->moneda_colum() as $k=>$v){
				$subt = 0;
				if($v['idmoneda']==$val['idmoneda']){
					$subt = $val['total_compra'];
				}
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, (number_format($subt,2)));
				$Oexcel->getActiveSheet()->getStyle(chr($alfabeto))->getNumberFormat()->applyFromArray(
					array(
						'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
					)
				);
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
		foreach($this->moneda_colum() as $k=>$v){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$alfabeto++;
		}
		
		$filename='reportecompra'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
	
	public function exportar_detallado(){
		set_time_limit(0);
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE VENTA RESUMIDO",true);
		
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
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SUCURSAL : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->sucursal->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MONEDA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("general.moneda");
			$this->moneda->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->moneda->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'COMPROBANTE : ');
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
			
			$this->load_model("seguridad.usuario");
			$this->usuario->find($_REQUEST['idvendedor']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->usuario->get("usuario")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO OPERACION : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipo_venta");
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->tipo_venta->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		
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
		
		foreach ($this->proveedor_agrupado() as $key => $val) {
			$alfabeto=65;
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val['proveedor']);
			$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($alfabeto+6).$col);
			
			$col++;
			foreach ($this->array_detalle() as $key => $v) {
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
			$data_head_detealle = $this->seleccion($this->comprobante_agrupado(), array("idproveedor"=>$val['idproveedor']));
			foreach($data_head_detealle as $kk=>$vv){
				$alfabeto=65;
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $vv['tipodocumento']." ".$vv['comprobante']);
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($alfabeto+3).$col);
				
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+4).$col, $vv['fecha_operacion']);
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto+4).$col.':'.chr($alfabeto+5).$col);

				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+6).$col, $vv['moneda']." TC: ".number_format($vv['cambio_moneda'],2));
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto+6).$col.':'.chr($alfabeto+7).$col);

				$col++;
				$data_body_detealle = $this->seleccion($this->dataDetallado(), array("idproveedor"=>$val['idproveedor'],'idcompra'=>$vv['idcompra']));
				$x_cant_aux=0;
				$total_pu= $total_igv=$total_total_c=0;
				foreach($data_body_detealle as $kkk=>$vvv){
					$x_cant_aux++;
					$vvv['item']= $x_cant_aux.")";
					
					$alfabeto=65;
					foreach ($this->array_detalle() as $ke => $va) {
						if($ke=='subtotal')
							$vvv["subtotal"] = redondeosunat($vvv[$ke],$fc);
						$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, ' '.$vvv[$ke]);
						$alfabeto++;
					}
					
					$total_pu 		= $total_pu + $vvv['precio_detalle'];
					$total_total_c 	= $total_total_c + $vvv['subtotal'];
					$col++;
				}
				$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle())-2).$col, ' '.$total_pu);
				$Oexcel->getActiveSheet()->getStyle(chr(65+count($this->array_detalle())-2).$col)->applyFromArray(
					array('borders' => array(
									'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
								)
					)
				);
				$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle())-1).$col, ' '.number_format($total_total_c,$fc,'.',''));
				$Oexcel->getActiveSheet()->getStyle(chr(65+count($this->array_detalle())-1).$col)->applyFromArray(
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
		$col++;
		/************************** CABECERA *****************************************/
		
		
		/************************** PIE *****************************************/
		$alfabeto=65;
		foreach($this->array_head() as $k=>$v){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$alfabeto++;
		}
		/************************** PIE *****************************************/
		
		
		$filename='reportecompra_d'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
}
?>