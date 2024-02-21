<?php

include_once "Controller.php";

class Reporteventa extends Controller {
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
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idcliente");
		$this->combobox->setAttr("name","idcliente");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select("idcliente,cliente");
		$query = $this->db->order_by("cliente")->get("venta.cliente_venta_view");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['cliente'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		// $idperfil = 4; // id del perfil vendedor, tal vez deberia ser contante
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);
		
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
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idtipoventa,descripcion');
		$query = $this->db->where("estado","A")->where("mostrar_en_venta","S")->order_by("descripcion")->get("venta.tipo_venta");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['tipopago'] = $this->combobox->getObject();
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
		if ($this->get_var_session("idperfil")!=1) {// SI NO ES ADMINOSTRADOR LA BUSQUEDA SOLO ES POR LA SESION INICIADA
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
				,cliente
				,vendedor
				,unidadmedida
				,cantidad_detalle
				,almacen
				,COALESCE(subtotal_venta,0)subtotal_venta
				,COALESCE(igv_venta,0)igv_venta
				,COALESCE(descuento_venta,0)descuento_venta
				,(COALESCE(subtotal_venta,0)+COALESCE(igv_venta,0)-COALESCE(descuento_venta,0)) total_compra
				,idmoneda
				FROM venta.venta_detalle_view
				WHERE estado='A'
				{$this->condicion_resumido()}
				{$this->condicion_detallado()}
				GROUP BY fecha_operacion
				,tipoventa
				,moneda
				,abreviatura
				,cliente
				,vendedor
				,unidadmedida
				,cantidad_detalle
				,almacen
				,subtotal_venta
				,igv_venta
				,descuento_venta
				,comprobante
				,tipodocumento
				,idmoneda
				ORDER BY comprobante;
				";
		$query      = $this->db->query($sql);
		// echo $sql;exit;
		$data = $query->result_array();
		// print_r($query->result_array());exit;
		return $data;
	}
	
	public function cliente_agrupado(){
		$sql = "SELECT btrim(cliente) cliente,idcliente
				FROM venta.venta_detalle_view 
				WHERE estado='A'
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='A' {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY cliente,idcliente
				ORDER BY cliente;";
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}
	
	public function comprobante_agrupado(){
		$sql = "SELECT (serie||'-'||numero) comprobante
				,to_char(fecha_operacion, 'DD/MM/YYYY'::text) fecha_operacion
				,idcliente 
				,idventa
				,tipodocumento
				,moneda
				,cambio_moneda
				FROM venta.venta_detalle_view 
				WHERE estado='A' 
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='A' {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY comprobante ,fecha_operacion,idcliente 
				,moneda,abreviatura,idventa,tipodocumento,cambio_moneda
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
				FROM venta.venta_detalle_view c
				WHERE estado='A'
				{$this->condicion_resumido()}
				GROUP BY c.idmoneda
				,moneda
				,simbolo
				,abreviatura
				ORDER BY idmoneda;";
		$query= $this->db->query($sql);

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
				SUM(COALESCE(subtotal_venta,0)+COALESCE(igv_venta,0)) monto
				FROM venta.venta_head_view 
				WHERE estado='A'
				{$this->condicion_resumido($where_and)}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='A' {$this->condicion_resumido($where_and)} {$this->condicion_detallado()})
				";
		// echo $sql."<br>";
		$query= $this->db->query($sql);

		$data = $query->row('monto');			
		
		return $data;
	}
	
	public function dataDetallado(){
		$sql="	SELECT
				idventa
				,producto
				,cantidad_detalle
				,unidadmedida
				,precio_venta_detalle
				,idcliente
				,subtotal
				FROM venta.venta_detalle_view
				WHERE estado='A'
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='A' {$this->condicion_resumido()} {$this->condicion_detallado()})";
	 // echo $sql;exit;
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}
	
	public function condicion_resumido($add_where=''){
		$where = "";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND (fecha_operacion)>='{$_REQUEST['fechainicio']}' AND (fecha_operacion)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND (fecha_operacion)='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['idcliente'])){
			$where.=" AND idcliente='{$_REQUEST['idcliente']}' ";
		}else if($_REQUEST['idcliente']==0){
			$where.=" AND idcliente='0' ";
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$where.=" AND idtipoventa='{$_REQUEST['idtipoventa']}' ";
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$where.=" AND idtipodocumento='{$_REQUEST['idtipodocumento']}' ";
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$where.=" AND idvendedor='{$_REQUEST['idvendedor']}' ";
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$where.=" AND idmoneda='{$_REQUEST['idmoneda']}' ";
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND idsucursal='{$_REQUEST['idsucursal']}' ";
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
		$datos      = $this->dataresumido();
		$monedas = $this->moneda_colum();
		$whit_compr=30;
		$whit_fecha=18;
		$whit_prov=70;
		$whit_vend=40;
		$whit_um=17;
		$whit_cant=10;
		$whit_min = 15;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}

		$cabecera = array('comprobante'=> array('Nro DOC',$whit_compr)
							,'fecha_operacion' => array('FECHA',$whit_fecha)
							,'cliente' => array('CLIENTE',$whit_prov)
							,'vendedor' => array('VENDEDOR',$whit_vend)
							,'unidadmedida' => array('UM',$whit_um)
							,'cantidad_detalle' => array('CANT',$whit_cant)
							// ,'moneda' => array('MONEDA',30)
							// ,'total_compra' => array('TOTAL',20,'R')
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE VENTA DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE VENTA DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE VENTA "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
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
			$this->pdf->Cell(5,3,$this->cliente_view->get("cliente"),0,1,'L');
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
			$this->pdf->Cell($val[1],9,$val[0],1,0);
			$total_lienzo = $total_lienzo + $val[1];
		}
		$this->pdf->Cell($whit_total_m,5,"TOTAL",1,1,'C');
		$this->pdf->Cell($total_lienzo,5,"",0,0,'C');
		foreach($monedas as $key=>$val){
			$this->pdf->Cell($whit_min,4,$val['abreviatura']." ".$val['simbolo'],1,0,'C');			
		}
		$this->pdf->Ln(10); 
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		/************************** BODY *****************************************/
		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($datos as $key => $val) {
			foreach ($cabecera as $k => $v) {
				if(isset($v[2])){
					$this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0,$v[2]);
				}else{
					$this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0);
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
		$datos      	= $this->dataDetallado();
		$monedas 		= $this->moneda_colum();
		$comprobantes 	= $this->comprobante_agrupado();
		$proveedor 		= $this->cliente_agrupado();
		$whit_compr		=30;
		$whit_fecha		=10;
		$whit_cant		=10;
		$whit_um		=20;
		$whit_pu		=20;
		$whit_igv		=15;
		$whit_prov		=115;
		$whit_min 		= 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m 	= 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}
		$total_lienzo= $whit_fecha+$whit_prov+$whit_cant+$whit_pu+$whit_igv+$whit_min;

		$cabecera = array('' => array('ITEM',$whit_fecha)
							,'producto' => array('PRODUCTO',$whit_prov)
							,'cantidad_detalle' => array('CANT',$whit_cant)
							,'unidadmedida' => array('UM',$whit_um)
							,'precio_venta_detalle' => array('P.U',$whit_pu,'R')
							// ,'igv_detalle' => array('IGV',$whit_igv,'R')
							,'subtotal' => array('TOTAL',$whit_min,'R')
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE VENTA DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE VENTA DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE VENTA "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
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
			$this->pdf->Cell(5,3,$this->cliente_view->get("cliente"),0,1,'L');
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
		
		foreach ($proveedor as $key => $val) {
			$this->pdf->SetFont('Arial','B',8);
			$cant_aux=0;
			$this->pdf->SetTextColor(22,160,133);
			$this->pdf->Cell($whit_prov,5,utf8_decode($val['cliente']),0,1);
			foreach ($cabecera as $k => $v) {
				$cant_aux++;
				$this->pdf->SetTextColor(0,0,0);
				$salto=0;
				if(count($cabecera)==($cant_aux)){
					$salto=1;
				}
				
				$this->pdf->Cell($v[1],5,($v[0]),1,$salto);
			}
			$this->pdf->SetFont('Arial','',8);
			$cant_aux=0;
			$data_head_detealle = $this->seleccion($comprobantes, array("idcliente"=>$val['idcliente']));
			foreach($data_head_detealle as $kk=>$vv){
				$cant_aux++;
				$salto=0;
				if(count($data_head_detealle)==($cant_aux)){
					$salto=1;
				}
				$this->pdf->Cell(60,5,$vv['tipodocumento']." ".$vv['comprobante'],'B',0);
				$this->pdf->Cell(20,5,$vv['fecha_operacion'],"B",0);
				$this->pdf->Cell(40,5,$vv['moneda']." TC: ".number_format($vv['cambio_moneda'],2),'B',1);
				
				$cant_aux=0;
				$x_cant_aux=0;
				$total_pu= $total_igv=$total_total_c=0;
				$data_body_detealle = $this->seleccion($datos, array("idcliente"=>$val['idcliente'],'idventa'=>$vv['idventa']));
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
						if(is_numeric($x_col)){
							$x_col=number_format($x_col,2);
						}
						if(isset($va[2]))
							$this->pdf->Cell($va[1],5,($x_col),0,0,$va[2]);
						else
							$this->pdf->Cell($va[1],5,($x_col),0,0);
					}
					$this->pdf->Ln();
					$total_pu 		= $total_pu + $vvv['precio_venta_detalle'];
					// $total_igv 		= $total_igv + $vvv['igv_detalle'];
					$total_total_c 	= $total_total_c + $vvv['subtotal'];
					
				}			
				$this->pdf->Cell($total_lienzo,0,'',1,1);
				$this->pdf->Cell($whit_prov+$whit_fecha+$whit_um+$whit_cant,5,'',0,0);
				$this->pdf->Cell($whit_pu,5,number_format($total_pu,2),1,0,'R');
				// $this->pdf->Cell($whit_igv,5,number_format($total_igv,2),1,0,'R');
				$this->pdf->Cell($whit_min,5,number_format($total_total_c,2),1,1,'R');
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
			$this->pdf->Cell(50,5,number_format($filtro,2),1,1,'R');
			
		}
		
		/************************** BODY *****************************************/
		// foreach ($datos as $key => $val) {
			// foreach ($cabecera as $k => $v) {
				// if(isset($v[2])){
					// $this->pdf->Cell($v[1],5,$val[$k],0,0,$v[2]);
				// }else{
					// $this->pdf->Cell($v[1],5,$val[$k],0,0);
				// }
			// }
			
			// foreach($monedas as $k=>$v){
				// $subt = 0;
				// if($v['idmoneda']==$val['idmoneda']){
					// $subt = $val['total_compra'];
				// }
				// $this->pdf->Cell($whit_min,5,number_format($subt,2,'.',','),0,0,'R');			
			// }
			// $this->pdf->Ln(); 
		// }
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
							,'cliente' => array('CLIENTE',$whit_cli,$whit_cli*100/$total_ancho,0,'L')
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
							,'precio_venta_detalle' => array('P.U',$whit_pu,'R')
							// ,'igv_detalle' => array('IGV',$whit_igv,'R')
							,'subtotal' => array('TOTAL',$whit_min,'R')
						);
		return $cabecera;
	}
	
	public function exportar_resumido(){
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
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO DOCUMENTO : ');
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
		$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'TOTAL');
		$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($alfabeto+count($this->moneda_colum())-1).$col);
		$col++;
		foreach($this->moneda_colum() as $key=>$val){
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val['abreviatura']." ".$val['simbolo']);
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
		
		$filename='reporteventa'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
	
	public function exportar_detallado(){
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
		
		foreach ($this->cliente_agrupado() as $key => $val) {
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val['cliente']);
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
			$data_head_detealle = $this->seleccion($this->comprobante_agrupado(), array("idcliente"=>$val['idcliente']));
			foreach($data_head_detealle as $kk=>$vv){
				$alfabeto=65;
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $vv['tipodocumento']." ".$vv['comprobante']);
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($alfabeto+3).$col);
				
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+4).$col, $vv['fecha_operacion']);
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto+4).$col.':'.chr($alfabeto+5).$col);

				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+6).$col, $vv['moneda']." TC: ".number_format($vv['cambio_moneda'],2));
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto+6).$col.':'.chr($alfabeto+7).$col);

				$col++;
				$data_body_detealle = $this->seleccion($this->dataDetallado(), array("idcliente"=>$val['idcliente'],'idventa'=>$vv['idventa']));
				$x_cant_aux=0;
				$total_pu= $total_igv=$total_total_c=0;
				foreach($data_body_detealle as $kkk=>$vvv){
					$x_cant_aux++;
					$vvv['item']= $x_cant_aux.")";
					
					$alfabeto=65;
					foreach ($this->array_detalle() as $ke => $va) {
						$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $vvv[$ke]);
						$alfabeto++;
					}
					
					$total_pu 		= $total_pu + $vvv['precio_venta_detalle'];
					// $total_igv 		= $total_igv + $vvv['igv_detalle'];
					$total_total_c 	= $total_total_c + $vvv['subtotal'];
					$col++;
				}
				$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle())-2).$col, $total_pu);
				$Oexcel->getActiveSheet()->getStyle(chr(65+count($this->array_detalle())-2).$col)->applyFromArray(
					array('borders' => array(
									'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
								)
					)
				);
				$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle())-1).$col, $total_total_c);
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
			// $col++;
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
		
		
		$filename='reporteventa'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
}
?>