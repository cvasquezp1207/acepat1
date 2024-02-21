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
		$data['control_reporte'] = $this->get_var_session("control_reporte")?$this->get_var_session("control_reporte"):'N';
		
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
		
		// combo rutas
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr("id","idubigeo");
		$this->combobox->setAttr("name","idubigeo");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->setAttr("required","");
		$this->db->select('idubigeo,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.ubigeosorsa");
		$this->combobox->addItem("","TODOS");
		$this->combobox->addItem($query->result_array());
		$data['ruta'] = $this->combobox->getObject();
		
		
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
		$idcajero = $this->get_param('idrol_cajero')?$this->get_param('idrol_cajero'):4;
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idcajero);
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idusuario","name"=>"idusuario","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($datos);
		$data["cajero"] = $this->combobox->getObject();
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
		$this->combobox->setAttr("id","idproducto");
		$this->combobox->setAttr("name","idproducto");
		$this->combobox->setAttr("class","chosen-select form-control");
		$this->db->select('idproducto,producto');
		$query = $this->db->order_by("producto")->get("almacen.view_productos_stock");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['producto'] = $this->combobox->getObject();
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
		
		$data['tipoventa'] = $this->combobox->getObject();
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
		
		
		/*---------------------------------------------------------------------*/
		// combo tipopago
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipopago"
				,"name"=>"idtipopago"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipopago, descripcion')->where("estado", "A")->where("mostrar_en_venta", "S")->get("venta.tipopago");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data["tipopago"] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		$query = $this->db->select('idmodalidad, modalidad')
			->where("estado", "A")
			->order_by("modalidad", "asc")->get("venta.modalidad");
		$this->combobox->init();
		$this->combobox->setAttr("id", "idmodalidad");
		$this->combobox->setAttr("name", "idmodalidad");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		// $this->combobox->setSelectedOption("1");
		// $this->combobox->ev =true;
		$data["modalidad"] = $this->combobox->getObject();
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
		$sql = "SELECT 
				DISTINCT idventa
				,tipodocumento
				,(simbolo_tipodoc||' '||serie||'-'||numero) comprobante
				,to_char(fecha_operacion, 'DD/MM/YYYY') fecha_operacion
				,tipoventa
				,moneda
				,abreviatura
				,doc_cliente
				,cliente
				,vendedor_nombre vendedor
				,vendedor_appat
				,almacen
				,COALESCE(subtotal_venta,0)subtotal_venta
				,COALESCE(igv_venta,0)igv_venta
				,COALESCE(descuento_venta,0)descuento_venta
				,(COALESCE(subtotal_venta,0)+COALESCE(igv_venta,0)-COALESCE(descuento_venta,0)) total_compra
				,idmoneda
				,tipopago
				FROM venta.venta_detalle_view
				WHERE estado='{$_REQUEST['estado']}'
				{$this->condicion_resumido()}
				{$this->condicion_detallado()}
				ORDER BY comprobante;
				";
		// echo "<pre>";echo $sql;exit;
		$query      = $this->db->query($sql);
		$data = $query->result_array();

		return $data;
	}
	
	public function query_head(){
		$sql = "SELECT DISTINCT cli.idcliente
				,btrim(cli.cliente) cliente
				,cli.dni
				,cli.ruc
				,cli.tipo_cliente
				,cli.documento_cliente
				,cli.zona
				,cli.direccion
				,to_char(venta_detalle_view.fecha_venta,'DD/MM/YYYY') fecha_venta
				,to_char(venta_detalle_view.fecha_registro,'HH24:DD:SS') hora_venta
				,venta_detalle_view.tipopago
				,venta_detalle_view.sucursal sucursal_venta
				,venta_detalle_view.tipoventa
				,venta_detalle_view.moneda
				,venta_detalle_view.abreviatura moneda_abreviatura
				,venta_detalle_view.cajero
				,venta_detalle_view.vendedor
				,venta_detalle_view.estado
				,venta_detalle_view.idventa
				,venta_detalle_view.fecha_venta fecha_operacion
				,idtipoventa
				,idtipodocumento
				,idvendedor 
				,idusuario
				,idmoneda
				,idsucursal
				,idtipopago
				,serie
				,numero
				,venta_detalle_view.idzona
				,venta_detalle_view.idubigeo
				,idempresa
				FROM venta.venta_detalle_view 
				JOIN venta.cliente_view cli ON cli.idcliente=venta_detalle_view.idcliente
				ORDER BY cliente";
		return $sql;
	}
	
	public function cliente_agrupado(){
		$sql = "SELECT idcliente
				,cliente
				,dni 
				,ruc
				,tipo_cliente
				,documento_cliente
				,zona
				,direccion
				FROM({$this->query_head()}) q
				WHERE estado='{$_REQUEST['estado']}'
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='{$_REQUEST['estado']}' {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY idcliente
				,cliente
				,dni 
				,ruc
				,tipo_cliente
				,documento_cliente
				,zona
				,direccion
				ORDER BY cliente
				";
		// echo "<pre>";echo $sql;exit;
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}
	
	public function informacion_cliente(){
		$sql = "SELECT * FROM ({$this->query_head()}) q
				WHERE estado='A'
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='{$_REQUEST['estado']}' {$this->condicion_resumido()} {$this->condicion_detallado()})
				";
		
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		return $data;
	}
	
	public function comprobante_agrupado(){
		$sql = "SELECT (serie||'-'||numero) comprobante
				,to_char(fecha_operacion, 'DD/MM/YYYY') fecha_operacion
				,idcliente 
				,idventa
				,tipodocumento
				,moneda
				,cambio_moneda
				FROM venta.venta_detalle_view 
				WHERE estado='{$_REQUEST['estado']}' 
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='{$_REQUEST['estado']}' {$this->condicion_resumido()} {$this->condicion_detallado()})
				GROUP BY comprobante ,fecha_operacion,idcliente 
				,moneda,abreviatura,idventa,tipodocumento,cambio_moneda
				ORDER BY comprobante,fecha_operacion;";
				// echo $sql;exit;
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
				WHERE estado='{$_REQUEST['estado']}'
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
				WHERE estado='{$_REQUEST['estado']}'
				{$this->condicion_resumido($where_and)}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='{$_REQUEST['estado']}' {$this->condicion_resumido($where_and)} {$this->condicion_detallado()})
				";
		// echo $sql."<br>";
		$query= $this->db->query($sql);

		$data = $query->row('monto');			
		
		return $data;
	}
	
	public function dataDetallado($detalle_completo=true){
		$sql="	SELECT
				idventa
				,producto
				,cantidad_detalle
				,um unidadmedida
				,CASE WHEN oferta='N' THEN precio_venta_detalle ELSE 0 END precio_venta_detalle
				,idcliente
				,subtotal
				,categoria grupo
				,linea familia
				,factor
				,fac_galon t_factor
				,(simbolo_tipodoc||' '||serie||'-'||numero) comprobante
				,vendedor
				,sucursal sucursal_venta
				,cajero
				,moneda
				,marca
				,modelo
				,linea
				,categoria
				,cliente_apellidos cliente
				,tipoventa
				,fecha_venta
				,to_char(fecha_registro,'HH24:DD:SS') hora_venta
				,serie
				,zona
				,ruta
				,idpreventa
				,mecanico
				,rampa
				FROM venta.venta_detalle_view 
				
				WHERE estado='{$_REQUEST['estado']}'
				{$this->condicion_resumido()}
				AND idventa IN (SELECT idventa FROM venta.venta_detalle_view WHERE estado='{$_REQUEST['estado']}' {$this->condicion_resumido()} {$this->condicion_detallado()})
				";
		if(!$detalle_completo){
			$sql.=$this->condicion_detallado();
		}
		$sql.="	ORDER BY comprobante,producto";
		 // echo $sql;exit;
		$query      = $this->db->query($sql);
		
		$data = $query->result_array();
		// print_r($data);exit;
		return $data;
	}
	
	public function condicion_resumido($add_where=''){
		$where = " AND idempresa={$this->get_var_session('idempresa')}";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND (fecha_operacion)>='{$_REQUEST['fechainicio']}' AND (fecha_operacion)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND (fecha_operacion)='{$_REQUEST['fechainicio']}' ";
			}
		}
		$_REQUEST['idcliente'] = trim($_REQUEST['idcliente']);
		if(!empty($_REQUEST['idcliente'])){
			$where.=" AND idcliente='{$_REQUEST['idcliente']}' ";
		}else if($_REQUEST['idcliente']==0 && $_REQUEST['cliente']!='[TODOS]'){
			$where.=" AND idcliente='0' ";
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$where.=" AND idtipoventa='{$_REQUEST['idtipoventa']}' ";
		}
		
		if(!empty($_REQUEST['idubigeo'])){
			$where.=" AND idubigeo='{$_REQUEST['idubigeo']}' ";
		}
		
		if(!empty($_REQUEST['idzona'])){
			$where.=" AND idzona='{$_REQUEST['idzona']}' ";
		}
		
		if(!empty($_REQUEST['idtipopago'])){
			$where.=" AND idtipopago='{$_REQUEST['idtipopago']}' ";
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$where.=" AND idtipodocumento='{$_REQUEST['idtipodocumento']}' ";
		}
		
		if(!empty($_REQUEST['serie'])){
			$where.=" AND serie='{$_REQUEST['serie']}' ";
		}
		
		if(!empty($_REQUEST['correlativo'])){
			$where.=" AND numero='{$_REQUEST['correlativo']}' ";
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$where.=" AND idvendedor='{$_REQUEST['idvendedor']}' ";
		}
		
		if(!empty($_REQUEST['idusuario'])){
			$where.=" AND idusuario='{$_REQUEST['idusuario']}' ";
		}
		
		if(is_numeric($_REQUEST['idmodalidad'])){
			$where.=" AND idmodalidad='{$_REQUEST['idmodalidad']}' ";
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
		
		if(!empty($_REQUEST['idproducto'])){
			$where.=" AND idproducto='{$_REQUEST['idproducto']}' ";
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
		$whit_prov=60;
		$whit_vend=60;
		$whit_min = 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}

		$cabecera = $this->array_head();

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","venta.tipopago","general.zona","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
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
		
		if(!empty($_REQUEST['idubigeo'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"RUTA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->ubigeosorsa->find($_REQUEST['idubigeo']);
			$this->pdf->Cell(5,3,$this->ubigeosorsa->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idzona'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"LOCALIDAD",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->zona->find($_REQUEST['idzona']);
			$this->pdf->Cell(5,3,$this->zona->get("zona"),0,1,'L');
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
			$this->pdf->Cell(5,3,utf8_decode($this->view_usuario->get("user_nombres")),0,1,'L');
		}
		
		if(!empty($_REQUEST['idusuario'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"CAJERO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->view_usuario->find($_REQUEST['idusuario']);
			$this->pdf->Cell(5,3,utf8_decode($this->view_usuario->get("user_nombres")),0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO OPERACION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$this->pdf->Cell(5,3,$this->tipo_venta->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipopago'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO PAGO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipopago->find($_REQUEST['idtipopago']);
			$this->pdf->Cell(5,3,$this->tipopago->get("descripcion"),0,1,'L');
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
					// $this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0,$v[2]);
				}else{
					$v[2]='L';
					// $this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0);
				}
				$width[] = $v[1];
				$values[] = utf8_decode($val[$k]);
				$pos[] = $v[2];
			}
			
			foreach($monedas as $k=>$v){
				$subt = 0;
				if($v['idmoneda']==$val['idmoneda']){
					$subt = $val['total_compra'];
				}
				// $this->pdf->Cell($whit_min,5,number_format($subt,2,'.',','),1,0,'R');
				$width[] = $whit_min;
				$values[] = number_format($subt,2);
				$pos[] = 'R';
			}
			
			$this->pdf->SetWidths($width);
			$this->pdf->Row($values, $pos, "Y", "Y");
			// $this->pdf->Ln(); 
			$item++;
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
		$proveedor 		= $this->cliente_agrupado();
		
		$whit_fecha		= 10;
		$whit_prov		= 115;
		$whit_cant		= 10;
		$whit_um		= 13;
		$whit_fac		= 15;
		$whit_pu		= 18;
		$whit_igv		= 0;
		$whit_min 		= 20;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m 	= 20;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}
		$total_lienzo= $whit_fecha+$whit_prov+$whit_cant+$whit_pu+$whit_igv+$whit_min + $whit_fac;

		$cabecera = array('' => array('ITEM',$whit_fecha)
							,'producto' => array('PRODUCTO',$whit_prov)
							,'cantidad_detalle' => array('CANT',$whit_cant)
							,'unidadmedida' => array('UM',$whit_um)
							,'factor' => array('FACTOR',$whit_fac,'R')
							,'precio_venta_detalle' => array('P.U',$whit_pu,'R')
							// ,'igv_detalle' => array('IGV',$whit_igv,'R')
							,'subtotal' => array('TOTAL',$whit_min,'R')
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","venta.tipopago","general.zona","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
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
		
		if(!empty($_REQUEST['idubigeo'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"RUTA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->ubigeosorsa->find($_REQUEST['idubigeo']);
			$this->pdf->Cell(5,3,$this->ubigeosorsa->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idzona'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"LOCALIDAD",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->zona->find($_REQUEST['idzona']);
			$this->pdf->Cell(5,3,$this->zona->get("zona"),0,1,'L');
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
		
		if(!empty($_REQUEST['idtipopago'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO PAGO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipopago->find($_REQUEST['idtipopago']);
			$this->pdf->Cell(5,3,$this->tipopago->get("descripcion"),0,1,'L');
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
				
				$this->pdf->Cell($v[1],5,($v[0]),1,$salto,"C");
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
					$total_total_c 	= $total_total_c + $vvv['subtotal'];
					
				}			
				$this->pdf->Cell($total_lienzo,0,'',1,1);
				$this->pdf->Cell($whit_prov+$whit_fecha+$whit_um+$whit_cant+$whit_fac,5,'',0,0);
				$this->pdf->Cell($whit_pu,5,number_format($total_pu,2),1,0,'R');
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
		$whit_item		= 10;
		$whit_compr		= 24;
		$whit_fecha		= 17;
		$whit_cli		= 75;
		$whit_vend		= 30;
		$whit_tpgo		= 20;
		
		$total_ancho	= $whit_compr+$whit_fecha+$whit_cli+$whit_vend + $whit_tpgo;

		$cabecera = array( 'item' => array('ITEM',$whit_item,$whit_item*100/$total_ancho,0,'L')
							,'comprobante' => array('COMPROBANTE',$whit_compr,$whit_compr*100/$total_ancho,0,'L')
							,'fecha_operacion' => array('FECHA',$whit_fecha,$whit_fecha*100/$total_ancho,0,'L')
							,'doc_cliente' => array('NRO DOC',$whit_fecha,$whit_fecha*100/$total_ancho,0,'L')
							,'cliente' => array('CLIENTE',$whit_cli,$whit_cli*10/$total_ancho,0,'L')
							,'vendedor' => array('VENDEDOR',$whit_vend,$whit_vend*10/$total_ancho,0,'L')
							,'tipopago' => array('PAGO',$whit_tpgo,$whit_tpgo*10/$total_ancho,0,'L')
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
		$whit_min 		= 25;//PARA EL WHIT DE LAS MONEDAS

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
	
	public function array_detalle_more(){
		$whit_fecha		=10;
		$whit_prod		=115;
		$whit_mar		=40;
		$whit_gr		=115;
		$whit_fam		=10;$whit_fecha		=12;
		$whit_fac		=20;
		$whit_cant		=20;
		$whit_um		=15;
		$whit_pu		=20;
		$whit_igv		=0;
		$whit_min 		= 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_min 		= 25;

		$cabecera = array('item' => array('ITEM',$whit_fecha,'L')
							,'producto' => array('PRODUCTO',$whit_prod,'L')			//Glosa
							,'marca' => array('MARCA',$whit_mar,'L')				//Marca
							,'grupo' => array('GRUPO',$whit_gr,'L')					//Grupo
							,'familia' => array('LINEA',$whit_fam,'L')			//Familia
							,'comprobante' => array('COMPROBANTE',$whit_fam,'L')	//nro comprobante
							,'tipoventa' => array('TIPO VENTA',$whit_fam,'L')	//nro comprobante
							,'fecha_venta' => array('FECHA VENTA',$whit_fam,'L','fecha')	//
							,'hora_venta' => array('HORA VENTA',$whit_fam,'L')	//hora
							,'cliente' => array('CLIENTE',$whit_fam,'L')			//
							,'ruta' => array('RUTA',$whit_fam,'L')			//
							,'zona' => array('LOCALIDAD',$whit_fam,'L')			//
							,'vendedor' => array('VENDEDOR',$whit_fam,'L')			//Vendedor
							,'cajero' => array('CAJERO',$whit_fam,'L')				//Cajero
							,'moneda' => array('MONEDA',$whit_fam,'L')				//Cajero
							,'sucursal_venta' => array('SUC. VENTA',$whit_fac,'L')	//Sucursal venta
							,'factor' => array('FACTOR',$whit_fac,'L')				//Factor
							,'cantidad_detalle' => array('CANT',$whit_cant,'L')		
							,'t_factor' => array('T. FACTOR(GALON)',$whit_fac,'L')				//Factor
							,'unidadmedida' => array('UM',$whit_um,'L')
							,'precio_venta_detalle' => array('P.U',$whit_pu,'R')
							,'subtotal' => array('TOTAL',$whit_min,'R')
							 ,'idpreventa' => array('PREVENTA',$whit_min,'L')
							 ,'rampa' => array('RAMPA',$whit_min,'L')
							 ,'mecanico' => array('MECANICO',$whit_min,'L')
						);
		return $cabecera;
	}
	
	public function exportar_resumido(){
		set_time_limit(0);
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('CSV');
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
		
		if(!empty($_REQUEST['idtipopago'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO PAGO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipopago");
			$this->tipopago->find($_REQUEST['idtipopago']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->tipopago->get("descripcion")));
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
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, (number_format($subt,2,'.','')));
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
			$filtro = $this->totales_monedas(" AND idmoneda='$v[idmoneda]' ");
			
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $filtro);
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->applyFromArray(
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
		
		$filename='reporteventa'.date("dmYhis").'.xls'; //save our workbook as this file name
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
		$data_detallado = $this->dataDetallado();
		
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('CSV');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE VENTA DETALLADO",true);
		
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
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->tipo_venta->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipopago'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO PAGO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipopago");
			$this->tipopago->find($_REQUEST['idtipopago']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->tipopago->get("descripcion")));
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
		
		$total_global = $total_pu_global = 0;
		foreach ($this->cliente_agrupado() as $key => $val) {
			$alfabeto=65;
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
				$data_body_detealle = $this->seleccion($data_detallado, array("idcliente"=>$val['idcliente'],'idventa'=>$vv['idventa']));
				$x_cant_aux=0;
				$total_pu= $total_igv=$total_total_c=0;
				foreach($data_body_detealle as $kkk=>$vvv){
					$x_cant_aux++;
					$vvv['item']= $x_cant_aux.")";
					
					$alfabeto=65;
					foreach ($this->array_detalle() as $ke => $va) {
						if($va[2]=='R')
							$vvv[$ke] = number_format($vvv[$ke],$fc,'.','');
						$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $vvv[$ke]);
						$alfabeto++;
					}
					
					$total_pu 		= $total_pu + $vvv['precio_venta_detalle'];
					$total_total_c 	= $total_total_c + $vvv['subtotal'];
					
					$col++;
				}
				
				$total_global = $total_global + $total_pu;
				$total_pu_global = $total_pu_global + $total_total_c;
				
				$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle())-2).$col, number_format($total_pu,$fc,'.',''));
				$Oexcel->getActiveSheet()->getStyle(chr(65+count($this->array_detalle())-2).$col)->applyFromArray(
					array('borders' => array(
									'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
								)
					)
				);
				$Oexcel->getActiveSheet()->setCellValue(chr(65+count($this->array_detalle())-1).$col, number_format($total_total_c,$fc,'.',''));
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
		// $Oexcel->getActiveSheet()->setCellValue(chr(64+count($this->array_detalle())).$col, $total_pu_global);
		// $Oexcel->getActiveSheet()->getStyle(chr(64+count($this->array_detalle())).$col)->applyFromArray(
				// array('borders' => array(
								// 'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								// 'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								// 'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								// 'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							// )
				// )
		// );
		// $Oexcel->getActiveSheet()->setCellValue(chr(64+count($this->array_detalle())-1).$col, number_format($total_global,$fc,'.',''));
		// $Oexcel->getActiveSheet()->getStyle(chr(64+count($this->array_detalle())-1).$col)->applyFromArray(
				// array('borders' => array(
							// 'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
							// 'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
							// 'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
							// 'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
						// )
			// )
		// );
		$alfabeto=65;
		foreach($this->moneda_colum() as $k=>$v){
			$filtro = $this->totales_monedas(" AND idmoneda='$v[idmoneda]' ");
			
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+count($this->array_detalle())-2).$col, "Total ".$v['abreviatura']);
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto+count($this->array_detalle())-2).$col)->applyFromArray(
					array('borders' => array(
									'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
									'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
								)
					)
			);	
			
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+count($this->array_detalle())-1).$col, number_format($filtro,$fc,'.',''));
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto+count($this->array_detalle())-1).$col)->applyFromArray(
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
		
		$alfabeto=65;
		foreach($this->array_head() as $k=>$v){
			if(chr($alfabeto)!='B'){
				$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			}else{
				$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setWidth('40');
			}
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