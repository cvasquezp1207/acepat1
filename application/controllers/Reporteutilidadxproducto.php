<?php

include_once "Controller.php";

class Reporteutilidadxproducto extends Controller {
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
		$this->combobox->setAttr("id","idproducto");
		$this->combobox->setAttr("name","idproducto");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select("idproducto,producto");
		$query = $this->db->order_by("producto")->get("venta.utilidad_producto");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['producto'] = $this->combobox->getObject();
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
		$sql = "SELECT  utilidad_producto.idmarca, 
				  utilidad_producto.marca,
				  utilidad_producto.idproducto, 
				  utilidad_producto.producto, 
				  utilidad_producto.abreviatura, 
				  utilidad_producto.unidades, 
				  utilidad_producto.precio_venta, 
				  utilidad_producto.importe_venta, 
				  utilidad_producto.costo, 
				  utilidad_producto.costo_ventas, 
				  utilidad_producto.utilidad, 
				  utilidad_producto.porcentaje,
  
  					venta.fecha_venta 
				FROM venta.utilidad_producto, 
					  venta.detalle_venta, 
					  venta.venta
				WHERE utilidad_producto.idproducto = detalle_venta.idproducto AND
  						venta.idventa = detalle_venta.idventa  
				{$this->condicion_resumido()}
				{$this->condicion_detallado()}
				GROUP BY venta.fecha_venta,utilidad_producto.idmarca, 
					  utilidad_producto.idproducto, 
					  utilidad_producto.producto, utilidad_producto.marca,
					  utilidad_producto.abreviatura, 
					  utilidad_producto.unidades, 
					  utilidad_producto.precio_venta, 
					  utilidad_producto.importe_venta, 
					  utilidad_producto.costo, 
					  utilidad_producto.costo_ventas, 
					  utilidad_producto.utilidad, 
					  utilidad_producto.porcentaje
				ORDER BY utilidad_producto.producto;
				";
		$query      = $this->db->query($sql);
		// echo $sql;exit;
		$data = $query->result_array();
		// print_r($query->result_array());exit;
		return $data;
	}
	
	public function producto_agrupado(){
		$sql = "SELECT btrim(producto) producto,idproducto
				FROM venta.util_producto 
				WHERE idproducto>=1
				{$this->condicion_resumido()}
				AND idproducto IN (SELECT idproducto FROM venta.util_producto WHERE idproducto>=1 {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY producto,idproducto
				ORDER BY producto;";
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}

	/*public function cliente_agrupado(){
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
	*/
	public function comprobante_agrupado(){
		$sql = "SELECT (serie||'-'||numero) comprobante
				,to_char(fecha_venta, 'DD/MM/YYYY'::text) fecha_venta
				,idcliente 
				,idventa
				,tipodocumento
				,moneda
				,cambio_moneda
				FROM venta.venta_detalle_view 
				WHERE estado='A' 
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='A' {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY comprobante ,fecha_venta,idcliente 
				,moneda,abreviatura,idventa,tipodocumento,cambio_moneda
				ORDER BY fecha_venta;";
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
				COALESCE(SUM(subtotal_venta+igv_venta),0) monto
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
				$where.=" AND (fecha_venta)>='{$_REQUEST['fechainicio']}' AND (fecha_venta)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND (fecha_venta)='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['idproducto'])){
			$where.=" AND idproducto='{$_REQUEST['idproducto']}' ";
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
		$whit_compr=8;
		$whit_fecha=12;
		$whit_prod=40;
		$whit_unidad=13;
		$whit_precio_venta=18;
		$whit_prov=10;
		$whit_importe_venta=20;
        $whit_costo=10;
          $whit_costo_ventas=20;
          $whit_utilidad=18;
		$whit_porcentaje=20;
		$whit_min = 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}

		$cabecera = array('idmarca'=> array('FAM',$whit_compr)
			               ,'marca' => array('MARCA',$whit_fecha)
							,'producto' => array('NOMBRE',$whit_prod)
							,'abreviatura' => array('UNID',$whit_prov)
							,'unidades' => array('CANT',$whit_unidad)
							, 'precio_venta' => array('P.VENTA',$whit_precio_venta)
							,  'importe_venta' => array('IMPT.VTA',$whit_importe_venta)
							,  'costo' => array('CSTO',$whit_costo)
							,  'costo_ventas' => array('CSTO.VTA',$whit_costo_ventas)
							,  'utilidad' => array('UTILIDAD',$whit_utilidad)
							,  'porcentaje' => array(' % ',$whit_porcentaje)
							// ,'moneda' => array('MONEDA',30)
							// ,'total_compra' => array('TOTAL',20,'R')
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.utilidad_producto","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE UTILIDAD X PRODUCTO ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE UTILIDAD X PRODUCTO ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE UTILIDAD X PRODUCTO "), 11, null, true);
			
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
			//if()
			$this->pdf->Cell($val[1],9,$val[0],1,0);
			$total_lienzo = $total_lienzo + $val[1];
		}
		//$this->pdf->Cell($whit_total_m,5,"TOTAL",1,1,'C');
		$this->pdf->Cell($total_lienzo,5,"",0,0,'C');
		foreach($monedas as $key=>$val){
			$this->pdf->Cell($whit_min,4,$val['abreviatura']." ".$val['simbolo'],1,0,'C');			
		}
		$this->pdf->Ln(10); 
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		$totalPorcentaje = 0; $totalUnidades = 0;
		/************************** BODY *****************************************/
		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($datos as $key => $val) {
			foreach ($cabecera as $k => $v) {
				if($k=="producto"){
					$val[$k]=substr($val[$k],0,20);
				}
				if($k=="porcentaje"){
					$totalPorcentaje += $val[$k];
				}
				if($k=="unidades"){
					$totalUnidades += $val[$k];
				}
                
                

				if(isset($v[2])){
					$this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0,$v[2]);
				}else{
					$this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0);
				}
			}
			
			 /*foreach($monedas as $k=>$v){
				$subt = 0;
				if($v['idmoneda']==$val['idmoneda']){
					$subt = $val['total_compra'];
				}
				$this->pdf->Cell($whit_min,5,number_format($subt,2,'.',','),1,0,'R');			
			} */
			$this->pdf->Ln(); 
		} 
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(($whit_compr + $whit_fecha + $whit_prov),5,"TOTAL",0,0,'R');
		//foreach($monedas as $k=>$v){
		//	$filtro = $this->totales_monedas(" AND idmoneda='$v[idmoneda]' ");
			$this->pdf->Cell($whit_min,5,number_format($totalPorcentaje,2,'.',','),0,0,'R');	
			$this->pdf->Ln();
		//$this->pdf->Cell(($whit_compr + $whit_fecha + $whit_prov),5,"Total Unid",0,0,'R');
			//$this->pdf->Cell($whit_min,5,number_format($totalUnidades,2,'.',','),0,0,'R');		
		//}
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
	
	public function detallado(){
		$datos      	= $this->dataDetallado();
		$monedas 		= $this->moneda_colum();
		$comprobantes 	= $this->comprobante_agrupado();
		$proveedor 		= $this->producto_agrupado();
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
				$this->pdf->SetTitle(utf8_decode("REPORTE VENTA DE ".$_REQUEST['fechainicio'].' A '.$_REQUEST['fechainicio']), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE VENTA DE ".$_REQUEST['fechainicio']), 11, null, true);
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
		
		if(!empty($_REQUEST['idproducto'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"PRODUCTO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->cliente_view->find($_REQUEST['idproducto']);
			$this->pdf->Cell(5,3,$this->cliente_view->get("producto"),0,1,'L');
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
			$this->pdf->Cell($whit_prov,5,utf8_decode($val['producto']),0,1);
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
			$data_head_detealle = $this->seleccion($comprobantes, array("idproducto"=>$val['idproducto']));
			foreach($data_head_detealle as $kk=>$vv){
				$cant_aux++;
				$salto=0;
				if(count($data_head_detealle)==($cant_aux)){
					$salto=1;
				}
				$this->pdf->Cell(40,5,$vv['tipodocumento']." ".$vv['comprobante'],0,0);
				$this->pdf->Cell(20,5,$vv['fecha_operacion'],0,0);
				$this->pdf->Cell(40,5,$vv['moneda']." TC: ".number_format($vv['cambio_moneda'],2),0,1);				
				
				$cant_aux=0;
				$x_cant_aux=0;
				$total_pu= $total_igv=$total_total_c=0;
				$data_body_detealle = $this->seleccion($datos, array("idproducto"=>$val['idproducto'],'idventa'=>$vv['idventa']));
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
}
?>